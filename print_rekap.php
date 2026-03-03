<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    die("Akses ditolak: Anda harus login.");
}

if ($_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak: Fitur ini hanya untuk Admin.");
}

// Search
$search_year = isset($_GET['year']) ? (int) $_GET['year'] : 0;
$search_month = isset($_GET['month']) ? (int) $_GET['month'] : 0;

$where = array();
if ($search_year > 0) {
    $where[] = "YEAR(tanggal) = $search_year";
}
if ($search_month > 0 && $search_month <= 12) {
    $where[] = "MONTH(tanggal) = $search_month";
}

$sql_where = "";
if (count($where) > 0) {
    $sql_where = "WHERE " . implode(" AND ", $where);
}

// Fetch data
$q = mysqli_query($koneksi, "
    SELECT
        DATE_FORMAT(tanggal, '%Y-%m') AS bulan,
        SUM(CASE WHEN kategori='pemasukan' THEN nominal ELSE 0 END) AS pemasukan,
        SUM(CASE WHEN kategori='pengeluaran' THEN nominal ELSE 0 END) AS pengeluaran
    FROM transaksi
    $sql_where
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY bulan ASC
");

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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Print Rekap Bulanan</title>
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

    <h2>Laporan Rekap Bulanan Keuangan</h2>
    <p class="subtitle">CV. Zie Net</p>

    <?php if ($search_year > 0 || $search_month > 0): ?>
        <p style="text-align:center; font-size: 13px;">
            Filter:
            <?php
            if ($search_month > 0)
                echo $bulan_names[$search_month] . ' ';
            if ($search_year > 0)
                echo $search_year;
            ?>
        </p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Bulan</th>
                <th class="text-right">Pemasukan</th>
                <th class="text-right">Pengeluaran</th>
                <th class="text-right">Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_masuk = 0;
            $total_keluar = 0;
            if ($q && mysqli_num_rows($q) > 0):
                while ($d = mysqli_fetch_assoc($q)):
                    $saldo = $d['pemasukan'] - $d['pengeluaran'];
                    $parts = explode('-', $d['bulan']);
                    $bulan_label = $bulan_names[(int) $parts[1]] . ' ' . $parts[0];

                    $total_masuk += $d['pemasukan'];
                    $total_keluar += $d['pengeluaran'];
                    ?>
                    <tr>
                        <td class="text-center">
                            <?php echo $no++; ?>
                        </td>
                        <td><strong>
                                <?php echo $bulan_label; ?>
                            </strong></td>
                        <td class="text-right text-success">
                            Rp
                            <?php echo number_format($d['pemasukan'], 0, ',', '.'); ?>
                        </td>
                        <td class="text-right text-danger">
                            Rp
                            <?php echo number_format($d['pengeluaran'], 0, ',', '.'); ?>
                        </td>
                        <td class="text-right" style="color: <?php echo $saldo >= 0 ? 'green' : 'red'; ?>;">
                            Rp
                            <?php echo number_format($saldo, 0, ',', '.'); ?>
                        </td>
                    </tr>
                <?php
                endwhile;
            else:
                ?>
                <tr>
                    <td colspan="5" class="text-center">Tidak ada data rekap ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="2" class="text-right">Total Keseluruhan</th>
                <th class="text-right text-success">Rp
                    <?php echo number_format($total_masuk, 0, ',', '.'); ?>
                </th>
                <th class="text-right text-danger">Rp
                    <?php echo number_format($total_keluar, 0, ',', '.'); ?>
                </th>
                <th class="text-right"
                    style="font-size: 16px; color: <?php echo ($total_masuk - $total_keluar) >= 0 ? 'green' : 'red'; ?>;">
                    Rp
                    <?php echo number_format($total_masuk - $total_keluar, 0, ',', '.'); ?>
                </th>
            </tr>
        </tfoot>
    </table>
</body>

</html>