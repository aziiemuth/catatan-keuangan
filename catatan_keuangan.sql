-- SQL untuk database catatan_keuangan
DROP DATABASE IF EXISTS catatan_keuangan;
CREATE DATABASE catatan_keuangan;
USE catatan_keuangan;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'admin',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$uH3ZmepqFW2Jkc1SKP0fk.WJfzlY0cWvAc/yP2r8loe2Ypi51CUN2', 'admin');

CREATE TABLE `jenis_transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO jenis_transaksi (nama) VALUES
('Belanja'),
('Setor Tukang Tagih'),
('Listrik'),
('PDAM'),
('Internet'),
('Gaji Teknisi'),
('Gaji Tukang Tagih'),
('Opsional');

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` date NOT NULL,
  `kategori` enum('pemasukan','pengeluaran') NOT NULL,
  `jenis_id` int(11) NOT NULL,
  `rincian` varchar(255) NOT NULL,
  `nominal` int(20) NOT NULL,
  `keterangan` text,
  `bukti` varchar(255),
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jenis_id` (`jenis_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_transaksi_jenis` FOREIGN KEY (`jenis_id`) REFERENCES `jenis_transaksi` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
