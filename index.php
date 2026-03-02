<?php
include "koneksi.php";
include "header.php";

// Total pemasukan
$r = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(nominal) AS total FROM transaksi WHERE kategori='pemasukan'"));
$masuk = $r['total'] ?? 0;

// Total pengeluaran
$r = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(nominal) AS total FROM transaksi WHERE kategori='pengeluaran'"));
$keluar = $r['total'] ?? 0;

$saldo = $masuk - $keluar;

// Transaksi terbaru
$transaksi = mysqli_query($koneksi, "
    SELECT t.*, j.nama AS jenis_nama
    FROM transaksi t
    LEFT JOIN jenis_transaksi j ON t.jenis_id = j.id
    ORDER BY t.id DESC
    LIMIT 10
");
?>

<div class="page-header animate-fade-up">
    <h3><i class="fas fa-chart-pie"></i>&nbsp; Dashboard</h3>
    <p>Ringkasan keuangan <?php echo $user['role'] !== 'admin' ? 'Anda' : 'keseluruhan'; ?></p>
</div>

<!-- Summary Cards -->
<div class="summary-grid">
    <div class="summary-card animate-fade-up animate-fade-up-1">
        <div class="card-icon income">
            <i class="fas fa-arrow-trend-up"></i>
        </div>
        <div class="card-data">
            <h5>Total Pemasukan</h5>
            <h3 class="text-success">Rp <?php echo number_format($masuk, 0, ',', '.'); ?></h3>
        </div>
    </div>

    <div class="summary-card animate-fade-up animate-fade-up-2">
        <div class="card-icon expense">
            <i class="fas fa-arrow-trend-down"></i>
        </div>
        <div class="card-data">
            <h5>Total Pengeluaran</h5>
            <h3 class="text-danger">Rp <?php echo number_format($keluar, 0, ',', '.'); ?></h3>
        </div>
    </div>

    <div class="summary-card animate-fade-up animate-fade-up-3">
        <div class="card-icon balance">
            <i class="fas fa-scale-balanced"></i>
        </div>
        <div class="card-data">
            <h5>Saldo Saat Ini</h5>
            <h3 class="text-primary">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></h3>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="card animate-fade-up animate-fade-up-4">
    <div class="card-body">
        <div
            style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:8px;">
            <h5 style="margin:0;font-weight:600;font-size:16px;">
                <i class="fas fa-clock-rotate-left text-primary"></i>&nbsp; Transaksi Terbaru
            </h5>
            <a href="data.php" class="btn btn-primary btn-sm">
                <i class="fas fa-arrow-right"></i> Lihat Semua
            </a>
        </div>

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th>Rincian</th>
                        <th>Nominal</th>
                        <th>Bukti</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($transaksi && mysqli_num_rows($transaksi) > 0): ?>
                        <?php while ($d = mysqli_fetch_assoc($transaksi)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($d['tanggal']); ?></td>
                                <td>
                                    <?php if ($d['kategori'] == 'pemasukan'): ?>
                                        <span class="badge bg-success"><i class="fas fa-arrow-up"></i> Pemasukan</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="fas fa-arrow-down"></i> Pengeluaran</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($d['jenis_nama']); ?></td>
                                <td><?php echo htmlspecialchars($d['rincian']); ?></td>
                                <td>
                                    <?php if ($d['kategori'] == 'pemasukan'): ?>
                                        <span class="text-success fw-bold">+ Rp
                                            <?php echo number_format($d['nominal'], 0, ',', '.'); ?></span>
                                    <?php else: ?>
                                        <span class="text-danger fw-bold">- Rp
                                            <?php echo number_format($d['nominal'], 0, ',', '.'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($d['bukti'])): ?>
                                        <a href="<?php echo htmlspecialchars($d['bukti']); ?>" target="_blank">
                                            <img src="<?php echo htmlspecialchars($d['bukti']); ?>" class="proof-img" alt="bukti">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p>Belum ada transaksi.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>