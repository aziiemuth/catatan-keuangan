<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    die("Akses ditolak: Anda harus login.");
}

if ($_SESSION['user']['role'] !== 'admin') {
    die("Akses ditolak: Fitur ini hanya untuk Admin.");
}

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=transaksi_semua.csv");

$output = fopen("php://output", "w");

fputcsv($output, array("Tanggal", "Kategori", "Jenis", "Rincian", "Nominal", "Keterangan"));

$q = mysqli_query($koneksi, "
    SELECT t.*, j.nama AS jenis_nama
    FROM transaksi t
    LEFT JOIN jenis_transaksi j ON t.jenis_id = j.id
    ORDER BY t.id ASC
");

$total_masuk = 0;
$total_keluar = 0;

while ($d = mysqli_fetch_assoc($q)) {
    if ($d['kategori'] == 'pemasukan') {
        $total_masuk += $d['nominal'];
    } else {
        $total_keluar += $d['nominal'];
    }

    fputcsv($output, array(
        $d['tanggal'],
        $d['kategori'],
        $d['jenis_nama'],
        $d['rincian'],
        $d['nominal'],
        $d['keterangan']
    ));
}

$saldo = $total_masuk - $total_keluar;

fputcsv($output, array());
fputcsv($output, array("TOTAL PEMASUKAN", $total_masuk));
fputcsv($output, array("TOTAL PENGELUARAN", $total_keluar));
fputcsv($output, array("SALDO AKHIR", $saldo));

fclose($output);
exit;
?>