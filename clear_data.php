<?php
session_start();
include "koneksi.php";

// Only admin can clear data
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Require POST method with confirmation token
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Akses tidak valid!'];
    header("Location: data.php");
    exit;
}

// Verify confirmation text
$confirm_text = isset($_POST['confirm_text']) ? trim($_POST['confirm_text']) : '';
if ($confirm_text !== 'HAPUS SEMUA') {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Konfirmasi tidak valid! Ketik "HAPUS SEMUA" untuk melanjutkan.'];
    header("Location: data.php");
    exit;
}

try {
    // Delete all proof files
    $bukti_q = mysqli_query($koneksi, "SELECT bukti FROM transaksi WHERE bukti != ''");
    while ($b = mysqli_fetch_assoc($bukti_q)) {
        if (!empty($b['bukti']) && file_exists($b['bukti'])) {
            @unlink($b['bukti']);
        }
    }

    // Clear all transactions
    mysqli_query($koneksi, "DELETE FROM transaksi");
    $deleted = mysqli_affected_rows($koneksi);

    $_SESSION['flash'] = ['type' => 'success', 'message' => "Berhasil menghapus $deleted transaksi! Data sekarang kosong."];
} catch (Exception $e) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()];
}

header("Location: data.php");
exit;
?>