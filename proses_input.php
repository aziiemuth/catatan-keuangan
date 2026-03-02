<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];

$tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
$kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
$jenis_id = (int) $_POST['jenis_id'];
$rincian = mysqli_real_escape_string($koneksi, $_POST['rincian']);
$keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
$nominal = (int) $_POST['nominal'];
$user_id = (int) $user['id'];

$bukti_path = "";

if (!empty($_FILES['bukti']['name'])) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
    $file_ext = strtolower(pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_extensions)) {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Format file tidak diizinkan! Hanya JPG, PNG, GIF, PDF.'];
        header("Location: input.php");
        exit;
    }

    $folder = "bukti";
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    // Generate secure random filename to prevent guessing/execution
    $new_filename = bin2hex(random_bytes(8)) . "_" . time() . "." . $file_ext;
    $target = $folder . "/" . $new_filename;

    if (move_uploaded_file($_FILES['bukti']['tmp_name'], $target)) {
        $bukti_path = $target;
    }
}

$sql = "INSERT INTO transaksi
        (tanggal, kategori, jenis_id, rincian, nominal, keterangan, bukti, user_id)
        VALUES (
            '$tanggal',
            '$kategori',
            '$jenis_id',
            '$rincian',
            '$nominal',
            '$keterangan',
            '$bukti_path',
            '$user_id'
        )";

try {
    if (mysqli_query($koneksi, $sql)) {
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Transaksi berhasil disimpan!'];
    } else {
        $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal menyimpan transaksi!'];
    }
} catch (Exception $e) {
    $_SESSION['flash'] = ['type' => 'error', 'message' => 'Error: ' . $e->getMessage()];
}

header("Location: data.php");
exit;
?>