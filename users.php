<?php
include "koneksi.php";
include "header.php";

if ($user['role'] != 'admin') {
    echo "<div class='alert alert-danger'><i class='fas fa-lock'></i> Hanya admin yang boleh mengelola user.</div>";
    include "footer.php";
    exit;
}

$q = mysqli_query($koneksi, "SELECT * FROM users ORDER BY id ASC");
?>

<div class="page-header animate-fade-up">
    <h3><i class="fas fa-users-cog"></i>&nbsp; Manajemen User</h3>
    <p>Kelola akun pengguna aplikasi</p>
</div>

<div class="animate-fade-up animate-fade-up-1" style="margin-bottom:16px;">
    <a href="user_add.php" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Tambah User
    </a>
</div>

<div class="card animate-fade-up animate-fade-up-2">
    <div class="card-body" style="padding:0;">
        <div class="table-wrapper" style="border:none;border-radius:0;">
            <table class="table" style="margin-bottom:0;">
                <thead>
                    <tr>
                        <th style="width:60px;">ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th style="width:130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = mysqli_fetch_assoc($q)): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <div
                                        style="width:32px;height:32px;border-radius:50%;background:var(--primary-gradient);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700;font-size:13px;flex-shrink:0;">
                                        <?php echo strtoupper(substr($u['username'], 0, 1)); ?>
                                    </div>
                                    <span style="font-weight:500;"><?php echo htmlspecialchars($u['username']); ?></span>
                                </div>
                            </td>
                            <td>
                                <?php if ($u['role'] == 'admin'): ?>
                                    <span class="badge bg-warning"><i class="fas fa-shield-halved"></i> Admin</span>
                                <?php else: ?>
                                    <span class="badge bg-info"><i class="fas fa-user"></i> User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="user_edit.php?id=<?php echo $u['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <?php if ($u['id'] != $user['id']): ?>
                                    <button
                                        onclick="confirmDelete('user_delete.php?id=<?php echo $u['id']; ?>', '<?php echo addslashes($u['username']); ?>')"
                                        class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>