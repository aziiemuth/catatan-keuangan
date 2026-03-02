<?php
// Temporary script to reset admin password
$koneksi = mysqli_connect("localhost", "root", "", "catatan_keuangan");
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$new_password = "admin123";
$hash = password_hash($new_password, PASSWORD_BCRYPT);

$sql = "UPDATE users SET password='" . mysqli_real_escape_string($koneksi, $hash) . "' WHERE username='admin'";

if (mysqli_query($koneksi, $sql)) {
    echo "Password admin berhasil direset ke: admin123<br>";
    echo "Hash baru: " . $hash . "<br>";

    // Verify
    $r = mysqli_query($koneksi, "SELECT password FROM users WHERE username='admin'");
    $u = mysqli_fetch_assoc($r);
    echo "Verify: " . (password_verify('admin123', $u['password']) ? 'COCOK ✅' : 'TIDAK COCOK ❌');
} else {
    echo "GAGAL: " . mysqli_error($koneksi);
}
?>