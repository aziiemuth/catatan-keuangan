<?php
include "koneksi.php";
include "header.php";

if ($user['role'] != 'admin') {
    echo "<div class='alert alert-danger'><i class='fas fa-lock'></i> Hanya admin yang boleh mengelola jenis transaksi.</div>";
    include "footer.php"; exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    if ($nama != '') {
        mysqli_query($koneksi, "INSERT INTO jenis_transaksi (nama) VALUES ('$nama')");
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Jenis transaksi berhasil ditambahkan!'];
    }
    header("Location: jenis.php");
    exit;
}

$q = mysqli_query($koneksi, "SELECT * FROM jenis_transaksi ORDER BY nama ASC");
?>

<div class="page-header animate-fade-up">
    <h3><i class="fas fa-tags"></i>&nbsp; Jenis Transaksi</h3>
    <p>Kelola kategori jenis transaksi</p>
</div>

<div class="row g-4">
    <!-- Add Form -->
    <div class="col-md-4 animate-fade-up animate-fade-up-1">
        <div class="card">
            <div class="card-body">
                <h5 style="font-size:15px;font-weight:600;margin-bottom:16px;">
                    <i class="fas fa-plus text-primary"></i>&nbsp; Tambah Jenis
                </h5>
                <form method="post">
                    <div class="mb-3">
                        <label class="form-label">Nama Jenis</label>
                        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama jenis" required>
                    </div>
                    <button class="btn btn-primary w-100"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <!-- List -->
    <div class="col-md-8 animate-fade-up animate-fade-up-2">
        <div class="card">
            <div class="card-body" style="padding:0;">
                <div style="padding:20px 24px 12px;">
                    <h5 style="font-size:15px;font-weight:600;margin:0;">
                        <i class="fas fa-list text-primary"></i>&nbsp; Daftar Jenis
                    </h5>
                </div>
                <div class="table-wrapper" style="border:none;border-radius:0;">
                    <table class="table" style="margin-bottom:0;">
                        <thead>
                            <tr>
                                <th style="width:50px;">#</th>
                                <th>Nama</th>
                                <th style="width:120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        $no = 1;
                        while ($d = mysqli_fetch_assoc($q)): ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($d['nama']); ?></td>
                                <td>
                                    <a href="jenis_edit.php?id=<?php echo $d['id']; ?>" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <button onclick="confirmDelete('jenis_hapus.php?id=<?php echo $d['id']; ?>', '<?php echo addslashes($d['nama']); ?>')" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div style="padding:12px 24px;">
                    <p class="text-muted" style="font-size:12px;margin:0;">
                        <i class="fas fa-info-circle"></i> Hati-hati menghapus jenis yang sudah dipakai transaksi.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
