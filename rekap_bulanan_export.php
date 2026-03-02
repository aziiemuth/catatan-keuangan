<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    die("Akses ditolak: Anda harus login.");
}

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=rekap_bulanan.csv");

$output = fopen("php://output", "w");

fputcsv($output, array("Bulan", "Pemasukan", "Pengeluaran", "Saldo"));

$q = mysqli_query($koneksi, "
    SELECT 
        DATE_FORMAT(tanggal, '%Y-%m') AS bulan,
        SUM(CASE WHEN kategori='pemasukan' THEN nominal ELSE 0 END) AS pemasukan,
        SUM(CASE WHEN kategori='pengeluaran' THEN nominal ELSE 0 END) AS pengeluaran
    FROM transaksi
    GROUP BY DATE_FORMAT(tanggal, '%Y-%m')
    ORDER BY bulan ASC
");

$total_masuk = 0;
$total_keluar = 0;

while ($d = mysqli_fetch_assoc($q)) {
    $saldo = $d['pemasukan'] - $d['pengeluaran'];

    fputcsv($output, array(
        $d['bulan'],
        $d['pemasukan'],
        $d['pengeluaran'],
        $saldo
    ));

    $total_masuk += $d['pemasukan'];
    $total_keluar += $d['pengeluaran'];
}

$total_saldo = $total_masuk - $total_keluar;

fputcsv($output, array());
fputcsv($output, array("TOTAL PEMASUKAN", $total_masuk));
fputcsv($output, array("TOTAL PENGELUARAN", $total_keluar));
fputcsv($output, array("SALDO SAAT INI", $total_saldo));

fclose($output);
exit;
?>