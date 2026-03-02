<?php
session_start();
include "koneksi.php";

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $q = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' LIMIT 1");

    if ($q && mysqli_num_rows($q) == 1) {
        $user = mysqli_fetch_assoc($q);
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            header("Location: index.php");
            exit;
        }
    }

    $error = "Username atau password salah!";
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — CV. Zie Net</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-title">
                <div class="login-icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <h4>CV. Zie Net</h4>
                <p>Sistem Catatan Keuangan</p>
            </div>

            <form method="post" id="loginForm">
                <div class="input-group">
                    <i class="fas fa-user input-icon"></i>
                    <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
                </div>

                <div class="input-group">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>&nbsp; Masuk
                </button>
            </form>

            <div class="login-footer">
                By Zizan
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if ($error): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Login Gagal',
                    text: '<?php echo addslashes($error); ?>',
                    confirmButtonColor: '#6366f1'
                });
            });
        </script>
    <?php endif; ?>

</body>

</html>