<?php
if (!isset($_SESSION)) {
    session_start();
}
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
$user = $_SESSION['user'];

// Flash message helper
$flash_type = '';
$flash_msg = '';
if (isset($_SESSION['flash'])) {
    $flash_type = $_SESSION['flash']['type'];
    $flash_msg = $_SESSION['flash']['message'];
    unset($_SESSION['flash']);
}

// Current page for active nav
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CV. Zie Net — Catatan Keuangan</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap 5 (grid & utilities only) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

    <!-- Flash Message Data (consumed by app.js) -->
    <?php if ($flash_msg): ?>
        <div id="flashMessage" data-type="<?php echo $flash_type; ?>"
            data-message="<?php echo htmlspecialchars($flash_msg); ?>" style="display:none;"></div>
    <?php endif; ?>

    <!-- Hamburger Button (Mobile) -->
    <button class="hamburger-btn" id="hamburgerBtn" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Brand -->
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="brand-text">
                CV. Zie Net
                <small>Catatan Keuangan</small>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            <div class="nav-section">Menu Utama</div>

            <a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>"
                data-page="index.php">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>

            <a href="data.php" class="nav-link <?php echo $current_page == 'data.php' ? 'active' : ''; ?>"
                data-page="data.php">
                <i class="fas fa-folder-open"></i> Data Transaksi
            </a>

            <a href="input.php" class="nav-link <?php echo $current_page == 'input.php' ? 'active' : ''; ?>"
                data-page="input.php">
                <i class="fas fa-plus-circle"></i> Input Transaksi
            </a>

            <?php if ($user['role'] == 'admin'): ?>
                <a href="jenis.php" class="nav-link <?php echo $current_page == 'jenis.php' ? 'active' : ''; ?>"
                    data-page="jenis.php">
                    <i class="fas fa-tags"></i> Jenis Transaksi
                </a>
            <?php endif; ?>

            <a href="rekap_bulanan.php"
                class="nav-link <?php echo $current_page == 'rekap_bulanan.php' ? 'active' : ''; ?>"
                data-page="rekap_bulanan.php">
                <i class="fas fa-calendar-alt"></i> Rekap Bulanan
            </a>

            <?php if ($user['role'] == 'admin'): ?>
                <div class="nav-section">Admin</div>

                <a href="users.php" class="nav-link <?php echo $current_page == 'users.php' ? 'active' : ''; ?>"
                    data-page="users.php">
                    <i class="fas fa-users-cog"></i> User Management
                </a>
            <?php endif; ?>

            <!-- Spacer -->
            <div style="flex:1;"></div>

            <!-- Logout -->
            <a href="logout.php" class="nav-link logout-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <!-- User Info -->
            <div class="sidebar-user">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars($user['role']); ?></div>
                </div>
            </div>

            <!-- Theme Toggle -->
            <div class="theme-toggle" id="themeToggle">
                <span><i id="themeIcon" class="fas fa-moon"></i>&nbsp; Dark Mode</span>
                <div class="theme-toggle-switch"></div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">