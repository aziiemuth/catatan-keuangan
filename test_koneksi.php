<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h3>1. PHP OK</h3>";

echo "<h3>2. Testing MySQL...</h3>";
$koneksi = mysqli_connect("localhost", "root", "", "catatan_keuangan");
if (!$koneksi) {
    die("GAGAL koneksi: " . mysqli_connect_error());
}
echo "Koneksi OK!<br>";

$r = mysqli_query($koneksi, "SELECT id, username, role FROM users");
echo "<h3>3. Users:</h3>";
while ($u = mysqli_fetch_assoc($r)) {
    echo "ID: {$u['id']} | Username: {$u['username']} | Role: {$u['role']}<br>";
}

echo "<h3>4. Password verify test:</h3>";
$r2 = mysqli_query($koneksi, "SELECT password FROM users WHERE username='admin'");
$u2 = mysqli_fetch_assoc($r2);
echo "Hash: " . substr($u2['password'], 0, 30) . "...<br>";
echo "Verify 'admin123': " . (password_verify('admin123', $u2['password']) ? 'COCOK' : 'TIDAK COCOK') . "<br>";

echo "<h3>5. Session test:</h3>";
session_start();
echo "Session ID: " . session_id() . "<br>";
echo "Session status: " . session_status() . "<br>";

echo "<h3>6. CSS file exists:</h3>";
echo file_exists("assets/css/style.css") ? "style.css ADA" : "style.css TIDAK ADA";
?>