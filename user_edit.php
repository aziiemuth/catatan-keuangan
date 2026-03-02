<?php
include "koneksi.php";
include "header.php";

if ($user['role'] != 'admin') {
    echo "<div class='alert alert-danger'><i class='fas fa-lock'></i> Hanya admin yang boleh mengedit user.</div>";
    include "footer.php";
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$q = mysqli_query($koneksi, "SELECT * FROM users WHERE id=$id");
if (!$q || mysqli_num_rows($q) == 0) {
    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> User tidak ditemukan.</div>";
    include "footer.php";
    exit;
}
$data = mysqli_fetch_assoc($q);

$err = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $role = $_POST['role'];
    $password = $_POST['password'];

    if ($username == '') {
        $err = "Username wajib diisi.";
    } else {
        // Check duplicate (exclude current user)
        $check = mysqli_query($koneksi, "SELECT id FROM users WHERE username='$username' AND id != $id");
        if (mysqli_num_rows($check) > 0) {
            $err = "Username sudah digunakan!";
        } else {
            if ($password != '') {
                $hash = password_hash($password, PASSWORD_BCRYPT);
                mysqli_query($koneksi, "UPDATE users SET username='$username', role='$role', password='$hash' WHERE id=$id");
            } else {
                mysqli_query($koneksi, "UPDATE users SET username='$username', role='$role' WHERE id=$id");
            }
            if ($id == $user['id']) {
                $_SESSION['user']['username'] = $username;
                $_SESSION['user']['role'] = $role;
            }
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'User berhasil diperbarui!'];
            header("Location: users.php");
            exit;
        }
    }
}
?>

<div class="page-header animate-fade-up">
    <h3><i class="fas fa-user-pen"></i>&nbsp; Edit User</h3>
    <p>Perbarui data akun pengguna</p>
</div>

<div class="card animate-fade-up animate-fade-up-1" style="max-width:500px;">
    <div class="card-body">
        <?php if ($err): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $err; ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-user"></i>&nbsp; Username</label>
                <input type="text" name="username" class="form-control"
                    value="<?php echo htmlspecialchars($data['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-shield-halved"></i>&nbsp; Role</label>
                <select name="role" class="form-control">
                    <option value="admin" <?php if ($data['role'] == 'admin')
                        echo 'selected'; ?>>Admin</option>
                    <option value="user" <?php if ($data['role'] == 'user')
                        echo 'selected'; ?>>User</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label"><i class="fas fa-lock"></i>&nbsp; Password Baru</label>
                <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diubah">
                <div class="form-text">Biarkan kosong jika tidak ingin mengubah password.</div>
            </div>
            <div style="display:flex;gap:8px;">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="users.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</div>

<?php include "footer.php"; ?>