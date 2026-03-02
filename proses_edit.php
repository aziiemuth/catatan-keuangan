<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$id = (int) $_POST['id'];
$tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
$kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
$jenis_id = (int) $_POST['jenis_id'];
$rincian = mysqli_real_escape_string($koneksi, $_POST['rincian']);
$keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
$nominal = (int) $_POST['nominal'];

$q = mysqli_query($koneksi, "SELECT bukti FROM transaksi WHERE id=$id");
$old = mysqli_fetch_assoc($q);
$bukti_lama = $old['bukti'];

$bukti_path = $bukti_lama;

if (!empty($_FILES['bukti']['name'])) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $file_ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_extensions)) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Format file tidak diizinkan! Hanya JPG, PNG, GIF, PDF.'];
        header("Location: edit.php?id=$id");
        exit;
    }

    $folder = "bukti";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    // Generate secure random filename
    $new_filename = bin2hex(random_bytes(8)) . "_" . time() . "." . $file_ext;
    $target = $folder . "/" . $new_filename;

    if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target)) {
        if (!empty($bukti_lama) && file_exists($bukti_lama)) {
            @unlink($bukti_lama);
        }
        $bukti_path = $target;
    }
}

$sql = "UPDATE transaksi SET
            tanggal    = '$tanggal',
            kategori   = '$kategori',
            jenis_id   = '$jenis_id',
            rincian    = '$rincian',
            keterangan = '$keterangan',
            nominal    = '$nominal',
            bukti      = '$bukti_path'
        WHERE id = $id";

try {
    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Transaksi berhasil diperbarui!'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal memperbarui transaksi!'];
    }
} catch (Exception $e) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()];
}

header("Location: data.php");
exit;
?>