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
        $q = mysqli_query($koneksi, "SELECT bukti FROM transaksi WHERE id=$id");

        if ($q && mysqli_num_rows($q) > 0) {
            $d = mysqli_fetch_assoc($q);
            // Delete proof image file if exists
            if (!empty($d['bukti']) && file_exists($d['bukti'])) {
                @unlink($d['bukti']);
            }
            mysqli_query($koneksi, "DELETE FROM transaksi WHERE id=$id");
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Transaksi berhasil dihapus!'];
        } else {
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Data tidak ditemukan atau Anda tidak punya izin!'];
        }
    } catch (Exception $e) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal menghapus: ' . $e->getMessage()];
    }
} else {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'ID tidak valid!'];
}

header("Location: data.php");
exit;
?>