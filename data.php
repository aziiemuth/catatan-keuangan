<?php
include "koneksi.php";
include "header.php";

// Filters
$tgl1 = isset($_GET['tgl1']) ? $_GET['tgl1'] : '';
$tgl2 = isset($_GET['tgl2']) ? $_GET['tgl2'] : '';
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$jenis_id = isset($_GET['jenis_id']) ? $_GET['jenis_id'] : '';
$nominal_min = isset($_GET['nominal_min']) ? $_GET['nominal_min'] : '';
$nominal_max = isset($_GET['nominal_max']) ? $_GET['nominal_max'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$where = array();

if ($tgl1 != '' && $tgl2 != '') {
    $where[] = "t.tanggal BETWEEN '" . mysqli_real_escape_string($koneksi, $tgl1) . "' AND '" . mysqli_real_escape_string($koneksi, $tgl2) . "'";
}
if ($kategori != '') {
    $where[] = "t.kategori = '" . mysqli_real_escape_string($koneksi, $kategori) . "'";
}
if ($jenis_id != '') {
    $where[] = "t.jenis_id = " . (int) $jenis_id;
}
if ($nominal_min !== '') {
    $where[] = "t.nominal >= " . (int) $nominal_min;
}
if ($nominal_max !== '') {
    $where[] = "t.nominal <= " . (int) $nominal_max;
}
if ($search !== '') {
    $s = mysqli_real_escape_string($koneksi, $search);
    $where[] = "(t.rincian LIKE '%$s%' OR t.keterangan LIKE '%$s%' OR j.nama LIKE '%$s%')";
}

$sql_where = "";
if (count($where) > 0) {
    $sql_where = "WHERE " . implode(" AND ", $where);
}

// Pagination
$per_page = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$offset = ($page - 1) * $per_page;

// Count total
$count_sql = "SELECT COUNT(*) AS total FROM transaksi t LEFT JOIN jenis_transaksi j ON t.jenis_id = j.id $sql_where";
$count_result = mysqli_fetch_assoc(mysqli_query($koneksi, $count_sql));
$total_rows = $count_result['total'];
$total_pages = max(1, ceil($total_rows / $per_page));

// Fetch data
$sql = "
    SELECT t.*, j.nama AS jenis_nama
    FROM transaksi t
    LEFT JOIN jenis_transaksi j ON t.jenis_id = j.id
    $sql_where
    ORDER BY t.id DESC
    LIMIT $per_page OFFSET $offset
";
$q = mysqli_query($koneksi, $sql);

// Jenis list for filter
$jenis_list = mysqli_query($koneksi, "SELECT * FROM jenis_transaksi ORDER BY nama ASC");

// Build query string for pagination links
$query_params = $_GET;
unset($query_params['page']);
$query_string = http_build_query($query_params);
?>

<div class="page-header animate-fade-up"
    style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:12px;">
    <div>
        <h3><i class="fas fa-folder-open"></i>&nbsp; Data Transaksi</h3>
        <p>Kelola dan filter semua transaksi keuangan</p>
    </div>
    <?php if ($user['role'] == 'admin'): ?>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="input.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Input Baru</a>
            <button type="button" class="btn btn-danger btn-sm" onclick="confirmClearAll()">
                <i class="fas fa-trash-alt"></i> Hapus Semua Data
            </button>
        </div>
        <!-- Hidden form for clear data -->
        <form id="clearDataForm" method="post" action="clear_data.php" style="display:none;">
            <input type="hidden" name="confirm_text" id="clearConfirmInput" value="">
        </form>
        <script>
            function confirmClearAll() {
                Swal.fire({
                    title: '⚠️ Hapus Semua Data?',
                    html: '<p style="margin-bottom:8px;">Ini akan menghapus <b>SELURUH transaksi</b> secara permanen!</p>' +
                        '<p style="color:#ef4444;font-weight:600;margin-bottom:12px;">Tindakan ini TIDAK BISA dibatalkan.</p>' +
                        '<p>Ketik <b style="color:#ef4444;">HAPUS SEMUA</b> untuk konfirmasi:</p>',
                    input: 'text',
                    inputPlaceholder: 'Ketik HAPUS SEMUA',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: '<i class="fas fa-trash-alt"></i> Ya, Hapus Semua!',
                    cancelButtonText: 'Batal',
                    inputValidator: function (value) {
                        if (value !== 'HAPUS SEMUA') {
                            return 'Ketik "HAPUS SEMUA" dengan benar untuk melanjutkan!';
                        }
                    }
                }).then(function (result) {
                    if (result.isConfirmed) {
                        document.getElementById('clearConfirmInput').value = result.value;
                        document.getElementById('clearDataForm').submit();
                    }
                });
            }
        </script>
    <?php endif; ?>
</div>

<!-- Filter Panel -->
<div class="filter-panel animate-fade-up animate-fade-up-1">
    <div class="filter-title">
        <i class="fas fa-filter"></i> Filter & Pencarian
    </div>
    <form method="get">
        <div class="row g-3">
            <div class="col-md-6 col-lg-3">
                <label class="form-label">Pencarian</label>
                <input type="text" name="search" class="form-control" placeholder="Cari rincian, keterangan..."
                    value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="tgl1" class="form-control" value="<?php echo htmlspecialchars($tgl1); ?>">
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="tgl2" class="form-control" value="<?php echo htmlspecialchars($tgl2); ?>">
            </div>
            <div class="col-md-6 col-lg-3">
                <label class="form-label">Kategori</label>
                <select name="kategori" class="form-control">
                    <option value="">Semua</option>
                    <option value="pemasukan" <?php if ($kategori == 'pemasukan')
                        echo 'selected'; ?>>Pemasukan</option>
                    <option value="pengeluaran" <?php if ($kategori == 'pengeluaran')
                        echo 'selected'; ?>>Pengeluaran
                    </option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Jenis</label>
                <select name="jenis_id" class="form-control">
                    <option value="">Semua</option>
                    <?php while ($d = mysqli_fetch_assoc($jenis_list)) {
                        $sel = ($jenis_id == $d['id']) ? "selected" : "";
                        echo "<option value=\"" . $d['id'] . "\" $sel>" . htmlspecialchars($d['nama']) . "</option>";
                    } ?>
                </select>
            </div>
            <div class="col-md-4 col-6">
                <label class="form-label">Nominal Min</label>
                <input type="number" name="nominal_min" class="form-control"
                    value="<?php echo htmlspecialchars($nominal_min); ?>">
            </div>
            <div class="col-md-4 col-6">
                <label class="form-label">Nominal Max</label>
                <input type="number" name="nominal_max" class="form-control"
                    value="<?php echo htmlspecialchars($nominal_max); ?>">
            </div>
        </div>

        <div
            style="display:flex;align-items:center;justify-content:space-between;margin-top:16px;flex-wrap:wrap;gap:8px;">
            <div class="filter-actions">
                <button class="btn btn-primary"><i class="fas fa-search"></i> Filter</button>
                <a href="data.php" class="btn btn-secondary"><i class="fas fa-rotate-left"></i> Reset</a>
            </div>
            <div class="filter-actions">
                <a href="export_transaksi_filter.php?<?php echo http_build_query($_GET); ?>"
                    class="btn btn-success btn-sm">
                    <i class="fas fa-file-csv"></i> Export CSV (Filter)
                </a>
                <a href="export_transaksi_all.php" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-file-csv"></i> Export Semua
                </a>
                <a href="input.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Input Transaksi
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Data Table -->
<div class="card animate-fade-up animate-fade-up-2">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Jenis</th>
                        <th>Rincian</th>
                        <th>Nominal</th>
                        <th>Keterangan</th>
                        <th>Bukti</th>
                        <?php if ($user['role'] == 'admin'): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($q && mysqli_num_rows($q) > 0): ?>
                        <?php while ($d = mysqli_fetch_assoc($q)): ?>
                            <tr>
                                <td style="white-space:nowrap;"><?php echo htmlspecialchars($d['tanggal']); ?></td>
                                <td>
                                    <?php if ($d['kategori'] == 'pemasukan'): ?>
                                        <span class="badge bg-success"><i class="fas fa-arrow-up"></i> Masuk</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="fas fa-arrow-down"></i> Keluar</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($d['jenis_nama']); ?></td>
                                <td><?php echo htmlspecialchars($d['rincian']); ?></td>
                                <td style="white-space:nowrap;">
                                    <?php if ($d['kategori'] == 'pemasukan'): ?>
                                        <span class="text-success fw-bold">+ Rp
                                            <?php echo number_format($d['nominal'], 0, ',', '.'); ?></span>
                                    <?php else: ?>
                                        <span class="text-danger fw-bold">- Rp
                                            <?php echo number_format($d['nominal'], 0, ',', '.'); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo nl2br(htmlspecialchars($d['keterangan'])); ?></td>
                                <td>
                                    <?php if (!empty($d['bukti'])): ?>
                                        <a href="<?php echo htmlspecialchars($d['bukti']); ?>" target="_blank">
                                            <img src="<?php echo htmlspecialchars($d['bukti']); ?>" class="proof-img" alt="bukti">
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <?php if ($user['role'] == 'admin'): ?>
                                    <td style="white-space:nowrap;">
                                    <a href="edit.php?id=<?php echo $d['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <button onclick="confirmDelete('hapus.php?id=<?php echo $d['id']; ?>')"
                                        class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?php echo $user['role'] == 'admin' ? '8' : '7'; ?>">
                                <div class="empty-state">
                                    <i class="fas fa-search"></i>
                                    <p>Tidak ada transaksi ditemukan.</p>
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
                    Menampilkan <?php echo $offset + 1; ?>–<?php echo min($offset + $per_page, $total_rows); ?> dari
                    <?php echo $total_rows; ?> data
                </div>
                <ul class="pagination">
                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link" href="?<?php echo $query_string; ?>&page=<?php echo $page - 1; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>
                    <?php
                    $start_page = max(1, $page - 2);
                    $end_page = min($total_pages, $page + 2);
                    for ($i = $start_page; $i <= $end_page; $i++):
                        ?>
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