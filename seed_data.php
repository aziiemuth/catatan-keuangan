<?php
/**
 * Insert dummy data for WiFi RT/RW Net business
 * Period: 2021 - 2026 (72 months)
 * Run once: http://localhost/catatan_keuangan/seed_data.php
 * 
 * Business growth:
 * - 2021: Starting year, 15 pelanggan awal, income ~3-4jt/bln
 * - 2022: Growing, 25-35 pelanggan, income ~4.5-5.5jt/bln
 * - 2023: Established, 40-50 pelanggan, income ~6-7jt/bln
 * - 2024: Expanding, 55-65 pelanggan, income ~7.5-9jt/bln
 * - 2025: Mature, 70-80 pelanggan, income ~9-11jt/bln
 * - 2026: Peak, 80-90 pelanggan, income ~11-13jt/bln
 */

set_time_limit(120);

$koneksi = mysqli_connect("localhost", "root", "", "catatan_keuangan");
if (!$koneksi)
    die("Koneksi gagal: " . mysqli_connect_error());

echo "<h2>🌐 Seed Data — WiFi RT/RW Net (2021-2026)</h2>";

// ========== 1. USERS ==========
$user_exists = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM users WHERE username='operator'"));
if ($user_exists['c'] == 0) {
    $hash = password_hash('operator123', PASSWORD_BCRYPT);
    mysqli_query($koneksi, "INSERT INTO users (username, password, role) VALUES ('operator', '" . mysqli_real_escape_string($koneksi, $hash) . "', 'user')");
    echo "✅ User 'operator' (password: operator123) ditambahkan<br>";
} else {
    echo "ℹ️ User 'operator' sudah ada<br>";
}

// ========== 2. JENIS TRANSAKSI ==========
$jenis_names = [
    'Iuran WiFi',
    'Pemasangan Baru',
    'Belanja',
    'Listrik',
    'PDAM',
    'Internet (ISP)',
    'Gaji Teknisi',
    'Gaji Tukang Tagih',
    'Setor Tukang Tagih',
    'Opsional',
    'Perawatan Alat'
];

foreach ($jenis_names as $nama) {
    $check = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM jenis_transaksi WHERE nama='" . mysqli_real_escape_string($koneksi, $nama) . "'"));
    if ($check['c'] == 0) {
        mysqli_query($koneksi, "INSERT INTO jenis_transaksi (nama) VALUES ('" . mysqli_real_escape_string($koneksi, $nama) . "')");
    }
}
echo "✅ Jenis transaksi updated<br>";

// Get IDs
$jenis = [];
$r = mysqli_query($koneksi, "SELECT id, nama FROM jenis_transaksi");
while ($d = mysqli_fetch_assoc($r))
    $jenis[$d['nama']] = $d['id'];

$admin_id = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id FROM users WHERE username='admin'"))['id'];
$op_r = mysqli_query($koneksi, "SELECT id FROM users WHERE username='operator'");
$operator_id = $op_r ? mysqli_fetch_assoc($op_r)['id'] : $admin_id;

// Clear old data
mysqli_query($koneksi, "DELETE FROM transaksi");
echo "✅ Old transaksi cleared<br><br>";

// ========== 3. GENERATE DATA ==========

/**
 * Business trajectory per year
 * [pelanggan_awal, pelanggan_akhir, iuran_per_pelanggan, isp_cost, listrik, gaji_teknisi, gaji_tagih]
 */
$yearly_config = [
    2021 => ['p_start' => 30, 'p_end' => 38, 'iuran' => 80000, 'isp' => 1000000, 'listrik' => 180000, 'gaji_tek' => 500000, 'gaji_tag' => 300000],
    2022 => ['p_start' => 40, 'p_end' => 48, 'iuran' => 85000, 'isp' => 1300000, 'listrik' => 240000, 'gaji_tek' => 600000, 'gaji_tag' => 350000],
    2023 => ['p_start' => 50, 'p_end' => 58, 'iuran' => 90000, 'isp' => 1600000, 'listrik' => 300000, 'gaji_tek' => 800000, 'gaji_tag' => 450000],
    2024 => ['p_start' => 60, 'p_end' => 70, 'iuran' => 95000, 'isp' => 1900000, 'listrik' => 340000, 'gaji_tek' => 900000, 'gaji_tag' => 500000],
    2025 => ['p_start' => 72, 'p_end' => 82, 'iuran' => 100000, 'isp' => 2200000, 'listrik' => 380000, 'gaji_tek' => 1000000, 'gaji_tag' => 500000],
    2026 => ['p_start' => 84, 'p_end' => 95, 'iuran' => 100000, 'isp' => 2500000, 'listrik' => 400000, 'gaji_tek' => 1200000, 'gaji_tag' => 600000],
];

// RT distribution names
$rt_labels = ['RT 01', 'RT 02', 'RT 03', 'RT 04'];

// Nama pelanggan baru
$nama_pelanggan = [
    'Pak Ahmad',
    'Bu Siti',
    'Mas Roni',
    'Bu Rina',
    'Pak Joko',
    'Mas Deri',
    'Bu Wati',
    'Pak Hasan',
    'Mas Eko',
    'Bu Ani',
    'Pak Budi',
    'Bu Lestari',
    'Mas Fajar',
    'Bu Sri',
    'Pak Udin',
    'Mas Agus',
    'Bu Nani',
    'Pak Sugi',
    'Mas Wahyu',
    'Bu Dewi',
    'Pak Tarno',
    'Mas Rizal',
    'Bu Yuni',
    'Pak Karto',
    'Mas Bayu',
    'Bu Endah',
    'Pak Suroto',
    'Mas Ilham',
    'Bu Sari',
    'Pak Darto',
    'Mas Yoga',
    'Bu Lia',
    'Pak Maman',
    'Mas Dani',
    'Bu Tuti',
    'Pak Slamet',
    'Mas Arif',
    'Bu Mega',
    'Pak Jono',
    'Mas Riki',
    'Bu Fitri',
    'Pak Gunawan',
    'Warung Bu Yanti',
    'Toko Pak Heri',
    'Warung Mas Dedi',
    'Kontrakan Pak Ali',
    'Kos Bu Ratna',
    'Warnet Mas Adi',
    'Bengkel Pak Rohman',
    'Laundry Bu Ina',
];

// Belanja items
$belanja_items = [
    ['Beli kabel UTP Cat6 1 box (305m)', 350000, 500000],
    ['Beli RJ45 + crimping tool', 100000, 180000],
    ['Beli kabel fiber optic 50m + konektor SC', 150000, 250000],
    ['Beli Access Point TP-Link Outdoor', 650000, 900000],
    ['Beli switch hub 8 port', 150000, 250000],
    ['Beli ONU/router pelanggan (5 unit)', 400000, 650000],
    ['Beli tiang mounting + bracket', 200000, 350000],
    ['Beli POE injector (3 unit)', 120000, 200000],
    ['Beli kabel listrik + stop kontak outdoor', 80000, 150000],
    ['Beli box panel outdoor tahan air', 180000, 300000],
    ['Beli adapter + kabel power cadangan', 75000, 120000],
    ['Beli splitter FO 1:8', 100000, 180000],
];

// Perawatan items
$perawatan_items = [
    ['Service router Mikrotik - ganti adaptor', 80000, 150000],
    ['Ganti kabel FO putus (akibat pohon tumbang)', 100000, 300000],
    ['Service AP outdoor - ganti POE', 100000, 200000],
    ['Perbaikan tiang antena miring', 50000, 150000],
    ['Ganti ONU pelanggan rusak', 80000, 150000],
    ['Reset & konfigurasi ulang router', 50000, 100000],
    ['Perbaikan grounding BTS', 100000, 200000],
    ['Ganti SFP module', 150000, 300000],
];

// Opsional items
$opsional_items = [
    ['Bensin motor survei lokasi', 30000, 75000],
    ['Bensin + parkir pasang pelanggan baru', 25000, 60000],
    ['Pulsa telepon koordinasi pelanggan', 25000, 50000],
    ['Makan siang tim kerja lapangan', 30000, 75000],
    ['Print brosur promosi WiFi', 50000, 100000],
    ['Beli stiker label kabel', 20000, 40000],
    ['Fotocopy form pendaftaran', 15000, 30000],
];

$success = 0;
$total_masuk = 0;
$total_keluar = 0;
$nama_idx = 0;

foreach ($yearly_config as $year => $cfg) {
    // Determine month range
    $month_start = 1;
    $month_end = ($year == 2026) ? 12 : 12;

    for ($month = $month_start; $month <= $month_end; $month++) {
        $m = str_pad($month, 2, '0', STR_PAD_LEFT);
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Calculate current pelanggan count (linear growth)
        $month_progress = (($month - 1) / 11);
        $pelanggan = (int) round($cfg['p_start'] + ($cfg['p_end'] - $cfg['p_start']) * $month_progress);

        // Distribute pelanggan across RTs
        $rt_counts = [];
        $remaining = $pelanggan;
        $rt_ratios = [0.32, 0.28, 0.22, 0.18];
        for ($i = 0; $i < 4; $i++) {
            $rt_counts[$i] = ($i < 3) ? (int) round($pelanggan * $rt_ratios[$i]) : $remaining;
            if ($i < 3)
                $remaining -= $rt_counts[$i];
        }

        $data = [];
        $iuran = $cfg['iuran'];

        // ===== PEMASUKAN =====

        // 1. Iuran WiFi per RT (tanggal 3-8)
        $bulan_indo = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        for ($rt = 0; $rt < 4; $rt++) {
            $day = min(3 + $rt, $days_in_month);
            $nominal = $rt_counts[$rt] * $iuran;
            $data[] = [
                "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
                'pemasukan',
                'Iuran WiFi',
                "Iuran WiFi {$bulan_indo[$month]} - {$rt_labels[$rt]} ({$rt_counts[$rt]} pelanggan @" . number_format($iuran, 0, ',', '.') . ")",
                $nominal,
                $rt_counts[$rt] >= ($cfg['p_start'] * $rt_ratios[$rt]) ? 'Lunas semua tepat waktu' : 'Ada 1-2 yang telat',
                $admin_id
            ];
        }

        // 2. Pemasangan baru (0-3 per month depending on growth)
        $new_customers = max(0, min(3, (int) round(($cfg['p_end'] - $cfg['p_start']) / 12 + (rand(0, 100) > 60 ? 1 : 0))));
        for ($n = 0; $n < $new_customers; $n++) {
            $day = min(10 + $n * 5 + rand(0, 3), $days_in_month);
            $biaya_pasang = ($year <= 2022) ? 250000 : (($year <= 2024) ? 300000 : 350000);
            $nama = $nama_pelanggan[$nama_idx % count($nama_pelanggan)];
            $rt = $rt_labels[rand(0, 3)];
            $nama_idx++;
            $data[] = [
                "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
                'pemasukan',
                'Pemasangan Baru',
                "Pemasangan baru - $nama $rt",
                $biaya_pasang,
                'Termasuk kabel ' . rand(15, 50) . 'm + ODP',
                $admin_id
            ];
        }

        // 3. Setoran tukang tagih (occasional late payments)
        if (rand(0, 10) > 3) {
            $day = min(rand(8, 12), $days_in_month);
            $telat_count = rand(1, 5);
            $setoran = $telat_count * $iuran;
            $data[] = [
                "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
                'pemasukan',
                'Setor Tukang Tagih',
                "Setoran tagihan bulan lalu ($telat_count pelanggan telat bayar)",
                $setoran,
                "Pelanggan bayar telat dari bulan sebelumnya",
                $admin_id
            ];
        }

        // ===== PENGELUARAN =====

        // 1. ISP (tanggal 1-3)
        $day = min(rand(1, 3), $days_in_month);
        $isp_var = $cfg['isp'] + (rand(-1, 1) * 50000);
        $data[] = [
            "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
            'pengeluaran',
            'Internet (ISP)',
            "Bayar ISP {$bulan_indo[$month]} - " . ($year <= 2022 ? '50 Mbps' : ($year <= 2024 ? '100 Mbps' : '200 Mbps')) . " dedicated",
            $isp_var,
            $year <= 2022 ? 'Provider: Indihome Bisnis' : 'Provider: Biznet',
            $admin_id
        ];

        // 2. Listrik (tanggal 2-5)
        $day = min(rand(2, 5), $days_in_month);
        $listrik_var = $cfg['listrik'] + rand(-20000, 30000);
        $data[] = [
            "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
            'pengeluaran',
            'Listrik',
            "Bayar listrik {$bulan_indo[$month]} - BTS + Router",
            $listrik_var,
            'Token listrik PLN',
            $admin_id
        ];

        // 3. Gaji Teknisi (tanggal 10)
        $day = min(10, $days_in_month);
        $data[] = [
            "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
            'pengeluaran',
            'Gaji Teknisi',
            "Gaji teknisi {$bulan_indo[$month]} - Andi",
            $cfg['gaji_tek'],
            '',
            $admin_id
        ];

        // 4. Gaji Tukang Tagih (tanggal 10)
        $data[] = [
            "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
            'pengeluaran',
            'Gaji Tukang Tagih',
            "Gaji tukang tagih {$bulan_indo[$month]} - Budi",
            $cfg['gaji_tag'],
            '',
            $admin_id
        ];

        // 5. Belanja (1-2 items per month)
        $belanja_count = rand(1, 2);
        for ($b = 0; $b < $belanja_count; $b++) {
            $item = $belanja_items[rand(0, count($belanja_items) - 1)];
            $day = min(rand(12, 20), $days_in_month);
            $nominal_bel = rand($item[1], $item[2]);
            $nominal_bel = (int) (round($nominal_bel / 5000) * 5000); // round to 5k
            $data[] = [
                "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
                'pengeluaran',
                'Belanja',
                $item[0],
                $nominal_bel,
                'Toko Jaya Komputer',
                $admin_id
            ];
        }

        // 6. Perawatan (every 2-3 months)
        if (rand(0, 10) > 5) {
            $item = $perawatan_items[rand(0, count($perawatan_items) - 1)];
            $day = min(rand(15, 25), $days_in_month);
            $nominal_prw = rand($item[1], $item[2]);
            $nominal_prw = (int) (round($nominal_prw / 5000) * 5000);
            $data[] = [
                "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
                'pengeluaran',
                'Perawatan Alat',
                $item[0],
                $nominal_prw,
                '',
                $admin_id
            ];
        }

        // 7. PDAM (every month, small)
        $day = min(rand(15, 20), $days_in_month);
        $data[] = [
            "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
            'pengeluaran',
            'PDAM',
            "Bayar PDAM {$bulan_indo[$month]} - Pos BTS utama",
            rand(70, 95) * 1000,
            '',
            $operator_id
        ];

        // 8. Opsional (1-2 small items)
        $ops_count = rand(1, 2);
        for ($o = 0; $o < $ops_count; $o++) {
            $item = $opsional_items[rand(0, count($opsional_items) - 1)];
            $day = min(rand(5, 28), $days_in_month);
            $nominal_ops = rand($item[1], $item[2]);
            $nominal_ops = (int) (round($nominal_ops / 5000) * 5000);
            $data[] = [
                "$year-$m-" . str_pad($day, 2, '0', STR_PAD_LEFT),
                'pengeluaran',
                'Opsional',
                $item[0],
                $nominal_ops,
                '',
                $operator_id
            ];
        }

        // Insert all data for this month
        $month_masuk = 0;
        $month_keluar = 0;
        foreach ($data as $d) {
            $jenis_id = isset($jenis[$d[2]]) ? $jenis[$d[2]] : 1;
            $sql = "INSERT INTO transaksi (tanggal, kategori, jenis_id, rincian, nominal, keterangan, bukti, user_id) VALUES (
                '" . $d[0] . "',
                '" . $d[1] . "',
                " . $jenis_id . ",
                '" . mysqli_real_escape_string($koneksi, $d[3]) . "',
                " . $d[4] . ",
                '" . mysqli_real_escape_string($koneksi, $d[5]) . "',
                '',
                " . $d[6] . "
            )";
            if (mysqli_query($koneksi, $sql)) {
                $success++;
                if ($d[1] == 'pemasukan')
                    $month_masuk += $d[4];
                else
                    $month_keluar += $d[4];
            } else {
                echo "❌ Error: " . mysqli_error($koneksi) . "<br>";
            }
        }

        $saldo = $month_masuk - $month_keluar;
        $total_masuk += $month_masuk;
        $total_keluar += $month_keluar;
        $status = $saldo > 0 ? '✅ UNTUNG' : '❌ RUGI';
        echo "{$bulan_indo[$month]} $year: Masuk Rp " . number_format($month_masuk, 0, ',', '.') .
            " | Keluar Rp " . number_format($month_keluar, 0, ',', '.') .
            " | Saldo Rp " . number_format($saldo, 0, ',', '.') .
            " $status<br>";
    }
    echo "<hr>";
}

echo "<br><h3>📊 Ringkasan Total</h3>";
echo "<b>Total transaksi berhasil dimasukkan:</b> $success<br>";
echo "<b>Total Pemasukan:</b> Rp " . number_format($total_masuk, 0, ',', '.') . "<br>";
echo "<b>Total Pengeluaran:</b> Rp " . number_format($total_keluar, 0, ',', '.') . "<br>";
echo "<b>Saldo Akhir:</b> Rp " . number_format($total_masuk - $total_keluar, 0, ',', '.') . "<br><br>";

echo "<a href='index.php' style='padding:10px 20px;background:#6366f1;color:#fff;border-radius:8px;text-decoration:none;'>➡️ Buka Dashboard</a>";
?>