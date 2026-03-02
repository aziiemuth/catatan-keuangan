<?php
include "koneksi.php";
include "header.php";

if ($user['role'] != 'admin') {
    echo "<div class='alert alert-danger'><i class='fas fa-lock'></i> Hanya admin yang boleh mengedit jenis.</div>";
    include "footer.php";
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$q = mysqli_query($koneksi, "SELECT * FROM jenis_transaksi WHERE id=$id");
if (!$q || mysqli_num_rows($q) == 0) {
    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Data tidak ditemukan.</div>";
    include "footer.php";
    exit;
}
$data = mysqli_fetch_assoc($q);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    if ($nama != '') {
        mysqli_query($koneksi, "UPDATE jenis_transaksi SET nama='$nama' WHERE id=$id");
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Jenis transaksi berhasil diperbarui!'];
    }
    header("Location: jenis.php");
    exit;
}
?>

<div class="page-header animate-fade-up">
    <h3><i class="fas fa-pen-to-square"></i>&nbsp; Edit Jenis Transaksi</h3>
    <p>Perbarui nama jenis transaksi</p>
</div>

<div class="card animate-fade-up animate-fade-up-1" style="max-width:500px;">
    <div class="card-body">
        <form method="post">
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-tag"></i>&nbsp; Nama Jenis</label>
                <input type="text" name="nama" class="form-control"
                    value="<?php echo htmlspecialchars($data['nama']); ?>" required>
            </div>
            <div style="display:flex;gap:8px;">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="jenis.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</div>

<?php include "footer.php"; ?>