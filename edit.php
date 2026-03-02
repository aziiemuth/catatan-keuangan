<?php
include "koneksi.php";
include "header.php";

if ($user['role'] != 'admin') {
    echo "<div class='alert alert-danger'><i class='fas fa-lock'></i> Hanya admin yang boleh mengedit.</div>";
    include "footer.php";
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$q = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id=$id");
if (!$q || mysqli_num_rows($q) == 0) {
    echo "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> Data tidak ditemukan.</div>";
    include "footer.php";
    exit;
}
$data = mysqli_fetch_assoc($q);
?>

<div class="page-header animate-fade-up">
    <h3><i class="fas fa-pen-to-square"></i>&nbsp; Edit Transaksi</h3>
    <p>Perbarui data transaksi #<?php echo $data['id']; ?></p>
</div>

<div class="card animate-fade-up animate-fade-up-1" style="max-width:800px;">
    <div class="card-body">
        <form method="post" action="proses_edit.php" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-calendar-day"></i>&nbsp; Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?php echo $data['tanggal']; ?>"
                        required>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-layer-group"></i>&nbsp; Kategori</label>
                    <select name="kategori" class="form-control">
                        <option value="pemasukan" <?php if ($data['kategori'] == 'pemasukan')
                            echo 'selected'; ?>>
                            Pemasukan</option>
                        <option value="pengeluaran" <?php if ($data['kategori'] == 'pengeluaran')
                            echo 'selected'; ?>>
                            Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-tag"></i>&nbsp; Jenis</label>
                    <select name="jenis_id" class="form-control">
                        <?php
                        $j = mysqli_query($koneksi, "SELECT * FROM jenis_transaksi ORDER BY nama ASC");
                        while ($d = mysqli_fetch_assoc($j)) {
                            $sel = ($d['id'] == $data['jenis_id']) ? "selected" : "";
                            echo "<option value=\"" . $d['id'] . "\" $sel>" . htmlspecialchars($d['nama']) . "</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label class="form-label"><i class="fas fa-align-left"></i>&nbsp; Rincian</label>
                <input type="text" name="rincian" class="form-control"
                    value="<?php echo htmlspecialchars($data['rincian']); ?>">
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fas fa-sticky-note"></i>&nbsp; Keterangan</label>
                <textarea name="keterangan" class="form-control"
                    rows="3"><?php echo htmlspecialchars($data['keterangan']); ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fas fa-money-bill-wave"></i>&nbsp; Nominal</label>
                <input type="number" name="nominal" class="form-control" value="<?php echo $data['nominal']; ?>"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fas fa-image"></i>&nbsp; Bukti</label>
                <?php if (!empty($data['bukti'])): ?>
                    <div style="margin-bottom:8px;">
                        <a href="<?php echo htmlspecialchars($data['bukti']); ?>" target="_blank">
                            <img src="<?php echo htmlspecialchars($data['bukti']); ?>"
                                style="width:80px;height:80px;object-fit:cover;border-radius:8px;border:2px solid var(--border-color);">
                        </a>
                    </div>
                <?php endif; ?>
                <input type="file" name="bukti" class="form-control" accept="image/*">
                <div class="form-text">Kosongkan jika tidak ingin mengganti bukti.</div>
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan Perubahan</button>
                <a href="data.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</div>

<?php include "footer.php"; ?>