<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id > 0) {
    try {
        // Check if jenis is used in any transaction
        $used = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM transaksi WHERE jenis_id=$id"));
        if ($used && $used['c'] > 0) {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Tidak bisa dihapus! Jenis ini digunakan oleh ' . $used['c'] . ' transaksi.'];
        } else {
            mysqli_query($koneksi, "DELETE FROM jenis_transaksi WHERE id=$id");
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Jenis transaksi berhasil dihapus!'];
        }
    } catch (Exception $e) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal menghapus! ' . $e->getMessage()];
    }
}
header("Location: jenis.php");
exit;
?>