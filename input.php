<?php
include "koneksi.php";
include "header.php";
?>

<div class="page-header animate-fade-up">
    <h3><i class="fas fa-plus-circle"></i>&nbsp; Input Transaksi</h3>
    <p>Tambahkan transaksi keuangan baru</p>
</div>

<div class="card animate-fade-up animate-fade-up-1" style="max-width:800px;">
    <div class="card-body">
        <form method="post" action="proses_input.php" enctype="multipart/form-data">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-calendar-day"></i>&nbsp; Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-layer-group"></i>&nbsp; Kategori</label>
                    <select name="kategori" class="form-control">
                        <option value="pemasukan">Pemasukan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label"><i class="fas fa-tag"></i>&nbsp; Jenis</label>
                    <select name="jenis_id" class="form-control">
                        <?php
                        $j = mysqli_query($koneksi, "SELECT * FROM jenis_transaksi ORDER BY nama ASC");
                        while ($d = mysqli_fetch_assoc($j)) {
                            echo "<option value=\"".$d['id']."\">".htmlspecialchars($d['nama'])."</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="mb-3 mt-3">
                <label class="form-label"><i class="fas fa-align-left"></i>&nbsp; Rincian</label>
                <input type="text" name="rincian" class="form-control" placeholder="Masukkan rincian transaksi">
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fas fa-sticky-note"></i>&nbsp; Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan (opsional)"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fas fa-money-bill-wave"></i>&nbsp; Nominal</label>
                <input type="number" name="nominal" class="form-control" placeholder="Masukkan nominal" required>
            </div>

            <div class="mb-3">
                <label class="form-label"><i class="fas fa-image"></i>&nbsp; Bukti (opsional)</label>
                <input type="file" name="bukti" class="form-control" accept="image/*">
            </div>

            <div style="display:flex;gap:8px;flex-wrap:wrap;">
                <button class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
                <a href="data.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </form>
    </div>
</div>

<?php include "footer.php"; ?>
