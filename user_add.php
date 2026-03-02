<?php
include "koneksi.php";
include "header.php";

if ($user['role'] != 'admin') {
    echo "<div class='alert alert-danger'><i class='fas fa-lock'></i> Hanya admin yang boleh menambah user.</div>";
    include "footer.php";
    exit;
}

$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if ($username == '' || $password == '') {
        $err = "Username dan password wajib diisi.";
    } else {
        // Check duplicate
        $check = mysqli_query($koneksi, "SELECT id FROM users WHERE username='$username'");
        if (mysqli_num_rows($check) > 0) {
            $err = "Username sudah digunakan!";
        } else {
            $hash = password_hash($password, PASSWORD_BCRYPT);
            mysqli_query($koneksi, "INSERT INTO users (username, password, role) VALUES ('$username', '$hash', '$role')");
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User berhasil ditambahkan!'];
            header("Location: users.php");
            exit;
        }
    }
}
?>

<div class="page-header animate-fade-up">
    <h3><i class="fas fa-user-plus"></i>&nbsp; Tambah User</h3>
    <p>Buat akun pengguna baru</p>
</div>

<div class="card animate-fade-up animate-fade-up-1" style="max-width:500px;">
    <div class="card-body">
        <?php if ($err): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $err; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-user"></i>&nbsp; Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-lock"></i>&nbsp; Password</label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-shield-halved"></i>&nbsp; Role</label>
                <select name="role" class="form-control">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="users.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</div>

<?php include "footer.php"; ?>