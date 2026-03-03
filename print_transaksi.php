<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    die("Akses ditolak: Anda harus login.");
}
if ($_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak: Fitur ini hanya untuk Admin.");
}

// Filters (same logic as data.php)
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

// Fetch data
$sql = "
    SELECT t.*, j.nama AS jenis_nama
    FROM transaksi t
    LEFT JOIN jenis_transaksi j ON t.jenis_id = j.id
    $sql_where
    ORDER BY t.tanggal DESC, t.id DESC
";
$q = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Print Transaksi Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 5px;
        }

        p.subtitle {
            text-align: center;
            margin-top: 0;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-success {
            color: green;
        }

        .text-danger {
            color: red;
        }

        @media print {
            body {
                margin: 0;
                padding: 10px;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 8px 16px; font-size: 16px; cursor: pointer;">🖨️ Cetak</button>
        <button onclick="window.close()" style="padding: 8px 16px; font-size: 16px; cursor: pointer;">❌ Tutup</button>
    </div>

    <h2>Laporan Transaksi Keuangan</h2>
    <p class="subtitle">CV. Zie Net</p>

    <?php if ($tgl1 != '' && $tgl2 != ''): ?>
        <p style="text-align:center; font-size: 13px;">Periode:
            <?php echo htmlspecialchars($tgl1); ?> s/d
            <?php echo htmlspecialchars($tgl2); ?>
        </p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th>Rincian</th>
                <th class="text-right">Pemasukan</th>
                <th class="text-right">Pengeluaran</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_masuk = 0;
            $total_keluar = 0;
            if ($q && mysqli_num_rows($q) > 0):
                while ($d = mysqli_fetch_assoc($q)):
                    $is_masuk = $d['kategori'] == 'pemasukan';
                    if ($is_masuk) {
                        $total_masuk += $d['nominal'];
                    } else {
                        $total_keluar += $d['nominal'];
                    }
                    ?>
                    <tr>
                        <td class="text-center">
                            <?php echo $no++; ?>
                        </td>
                        <td style="white-space:nowrap;">
                            <?php echo htmlspecialchars($d['tanggal']); ?>
                        </td>
                        <td>
                            <?php echo ucfirst($d['kategori']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($d['jenis_nama']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($d['rincian']); ?>
                        </td>
                        <td class="text-right <?php echo $is_masuk ? 'text-success' : ''; ?>">
                            <?php echo $is_masuk ? 'Rp ' . number_format($d['nominal'], 0, ',', '.') : '-'; ?>
                        </td>
                        <td class="text-right <?php echo !$is_masuk ? 'text-danger' : ''; ?>">
                            <?php echo !$is_masuk ? 'Rp ' . number_format($d['nominal'], 0, ',', '.') : '-'; ?>
                        </td>
                        <td>
                            <?php echo nl2br(htmlspecialchars($d['keterangan'])); ?>
                        </td>
                    </tr>
                <?php
                endwhile;
            else:
                ?>
                <tr>
                    <td colspan="8" class="text-center">Tidak ada transaksi ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">Total</th>
                <th class="text-right text-success">Rp
                    <?php echo number_format($total_masuk, 0, ',', '.'); ?>
                </th>
                <th class="text-right text-danger">Rp
                    <?php echo number_format($total_keluar, 0, ',', '.'); ?>
                </th>
                <th></th>
            </tr>
            <tr>
                <th colspan="5" class="text-right">Saldo Akhir</th>
                <th colspan="2" class="text-center" style="font-size: 16px;">
                    <?php
                    $saldo = $total_masuk - $total_keluar;
                    echo 'Rp ' . number_format($saldo, 0, ',', '.');
                    ?>
                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</body>

</html>