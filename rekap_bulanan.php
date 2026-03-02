<?php
include "koneksi.php";
include "header.php";

// Search
$search_year = isset($_GET['year']) ? (int) $_GET['year'] : 0;
$search_month = isset($_GET['month']) ? (int) $_GET['month'] : 0;

$having = array();
if ($search_year > 0) {
    $having[] = "YEAR(tanggal) = $search_year";
}
if ($search_month > 0 && $search_month <= 12) {
    $having[] = "MONTH(tanggal) = $search_month";
}

$where = "";

$having_sql = "";
if (count($having) > 0) {
    // Apply year/month filter as WHERE conditions
    if ($where === "") {
        $where = "WHERE " . implode(" AND ", $having);
    } else {
        $where .= " AND " . implode(" AND ", $having);
    }
}

// Pagination
$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

// Count total months
$count_sql = "SELECT COUNT(*) as total FROM (
    SELECT DATE_FORMAT(tanggal, '%Y-%m') AS bulan
    FROM transaksi
    $where
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
) AS sub";
$count_result = mysqli_fetch_assoc(mysqli_query($koneksi, $count_sql));
$total_rows = $count_result['total'];
$total_pages = max(1, ceil($total_rows / $per_page));

// Fetch data
$q = mysqli_query($koneksi, "
    SELECT
        DATE_FORMAT(tanggal, '%Y-%m') AS bulan,
        SUM(CASE WHEN kategori='pemasukan' THEN nominal ELSE 0 END) AS pemasukan,
        SUM(CASE WHEN kategori='pengeluaran' THEN nominal ELSE 0 END) AS pengeluaran
    FROM transaksi
    $where
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY bulan DESC
    LIMIT $per_page OFFSET $offset
");

$total_masuk = 0;
$total_keluar = 0;

// Grand totals (all data)
$grand = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT
        SUM(CASE WHEN kategori='pemasukan' THEN nominal ELSE 0 END) AS pemasukan,
        SUM(CASE WHEN kategori='pengeluaran' THEN nominal ELSE 0 END) AS pengeluaran
    FROM transaksi
"));
$grand_masuk = $grand['pemasukan'] ?? 0;
$grand_keluar = $grand['pengeluaran'] ?? 0;

// Month names in Indonesian
$bulan_names = [
    '',
    'Januari',
    'Februari',
    'Maret',
    'April',
    'Mei',
    'Juni',
    'Juli',
    'Agustus',
    'September',
    'Oktober',
    'November',
    'Desember'
];

// Build query string for pagination
$query_params = $_GET;
unset($query_params['page']);
$query_string = http_build_query($query_params);
?>

<div class="page-header animate-fade-up">
    <h3><i class="fas fa-calendar-alt"></i>&nbsp; Rekap Bulanan</h3>
    <p>Ringkasan keuangan per bulan</p>
</div>

<!-- Search & Export -->
<div class="filter-panel animate-fade-up animate-fade-up-1">
    <div class="filter-title">
        <i class="fas fa-search"></i> Pencarian
    </div>
    <form method="get">
        <div class="row g-3">
            <div class="col-md-4 col-6">
                <label class="form-label">Tahun</label>
                <input type="number" name="year" class="form-control" placeholder="Contoh: 2026"
                    value="<?php echo $search_year > 0 ? $search_year : ''; ?>" min="2000" max="2099">
            </div>
            <div class="col-md-4 col-6">
                <label class="form-label">Bulan</label>
                <select name="month" class="form-control">
                    <option value="">Semua</option>
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php echo $search_month == $m ? 'selected' : ''; ?>>
                            <?php echo $bulan_names[$m]; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button class="btn btn-primary"><i class="fas fa-search"></i> Cari</button>
                <a href="rekap_bulanan.php" class="btn btn-secondary"><i class="fas fa-rotate-left"></i> Reset</a>
                <a href="rekap_bulanan_export.php" class="btn btn-success btn-sm">
                    <i class="fas fa-file-csv"></i> Export CSV
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Grand Total Cards -->
<div class="summary-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
    <div class="summary-card animate-fade-up animate-fade-up-2">
        <div class="card-icon income"><i class="fas fa-arrow-trend-up"></i></div>
        <div class="card-data">
            <h5>Total Pemasukan</h5>
            <h3 class="text-success" style="font-size:18px;">Rp <?php echo number_format($grand_masuk, 0, ',', '.'); ?>
            </h3>
        </div>
    </div>
    <div class="summary-card animate-fade-up animate-fade-up-3">
        <div class="card-icon expense"><i class="fas fa-arrow-trend-down"></i></div>
        <div class="card-data">
            <h5>Total Pengeluaran</h5>
            <h3 class="text-danger" style="font-size:18px;">Rp <?php echo number_format($grand_keluar, 0, ',', '.'); ?>
            </h3>
        </div>
    </div>
    <div class="summary-card animate-fade-up animate-fade-up-4">
        <div class="card-icon balance"><i class="fas fa-scale-balanced"></i></div>
        <div class="card-data">
            <h5>Saldo Keseluruhan</h5>
            <h3 class="text-primary" style="font-size:18px;">Rp
                <?php echo number_format($grand_masuk - $grand_keluar, 0, ',', '.'); ?>
            </h3>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card animate-fade-up">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none;border-radius:0;">
            <table class="table" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th>Bulan</th>
                        <th>Pemasukan</th>
                        <th>Pengeluaran</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($q && mysqli_num_rows($q) > 0): ?>
                        <?php while ($d = mysqli_fetch_assoc($q)):
                            $saldo = $d['pemasukan'] - $d['pengeluaran'];
                            $parts = explode('-', $d['bulan']);
                            $bulan_label = $bulan_names[(int) $parts[1]] . ' ' . $parts[0];
                            ?>
                            <tr>
                                <td>
                                    <span style="font-weight:600;"><?php echo $bulan_label; ?></span>
                                </td>
                                <td>
                                    <span class="text-success fw-bold">Rp
                                        <?php echo number_format($d['pemasukan'], 0, ',', '.'); ?></span>
                                </td>
                                <td>
                                    <span class="text-danger fw-bold">Rp
                                        <?php echo number_format($d['pengeluaran'], 0, ',', '.'); ?></span>
                                </td>
                                <td>
                                    <span class="fw-bold"
                                        style="color:<?php echo $saldo >= 0 ? 'var(--primary)' : 'var(--danger)'; ?>;">
                                        Rp <?php echo number_format($saldo, 0, ',', '.'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-state">
                                    <i class="fas fa-calendar-xmark"></i>
                                    <p>Tidak ada data rekap ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination-wrapper" style="padding:16px 24px;">
                <div class="pagination-info">
                    Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?>
                </div>
                <ul class="pagination">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo $query_string; ?>&page=<?php echo $page - 1; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                            <a class="page-link"
                                href="?<?php echo $query_string; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?php echo $page >= $total_pages ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo $query_string; ?>&page=<?php echo $page + 1; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include "footer.php"; ?>