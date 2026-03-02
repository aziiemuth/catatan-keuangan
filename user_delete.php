<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0 && $id != $_SESSION['user']['id']) {
    try {
        // Get admin user ID for reassigning transactions
        $admin = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id FROM users WHERE role='admin' AND id != $id ORDER BY id ASC LIMIT 1"));

        if ($admin) {
            // Reassign user's transactions to admin before deleting
            mysqli_query($koneksi, "UPDATE transaksi SET user_id = " . $admin['id'] . " WHERE user_id = $id");
        } else {
            // No other admin exists, delete the transactions
            mysqli_query($koneksi, "DELETE FROM transaksi WHERE user_id = $id");
        }

        // Now safe to delete the user
        mysqli_query($koneksi, "DELETE FROM users WHERE id=$id");
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'User berhasil dihapus! Transaksi dipindahkan ke admin.'];
    } catch (Exception $e) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal menghapus user: ' . $e->getMessage()];
    }
} else {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Tidak dapat menghapus akun sendiri!'];
}

header("Location: users.php");
exit;
?>