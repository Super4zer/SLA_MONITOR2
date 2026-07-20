-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               12.0.2-MariaDB-log - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for logklikdsi
DROP DATABASE IF EXISTS `logklikdsi`;
CREATE DATABASE IF NOT EXISTS `logklikdsi` /*!40100 DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci */;
USE `logklikdsi`;

-- Dumping structure for table logklikdsi.rcustomer
DROP TABLE IF EXISTS `rcustomer`;
CREATE TABLE IF NOT EXISTS `rcustomer` (
  `kodcustomer` varchar(20) NOT NULL DEFAULT '',
  `nmcustomer` varchar(75) NOT NULL DEFAULT '',
  `almtcustomer` varchar(200) NOT NULL DEFAULT '',
  `piutang` double NOT NULL DEFAULT 0,
  `cp` varchar(50) NOT NULL DEFAULT '',
  `telp` varchar(40) NOT NULL,
  `fax` varchar(30) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL,
  `npwp` varchar(50) NOT NULL,
  `tgl_kontrak` date DEFAULT NULL,
  `valid_until` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=Active, 0=Suspended',
  `iduser` varchar(15) NOT NULL,
  `nmwpajak` varchar(50) NOT NULL,
  `tipe_fasilitas` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`kodcustomer`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rcustomer_kontak
DROP TABLE IF EXISTS `rcustomer_kontak`;
CREATE TABLE IF NOT EXISTS `rcustomer_kontak` (
  `id_kontak` int(11) NOT NULL AUTO_INCREMENT,
  `kodcustomer` varchar(50) NOT NULL,
  `nama_kontak` varchar(100) NOT NULL,
  `no_wa` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_kontak`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rcustomer_produk
DROP TABLE IF EXISTS `rcustomer_produk`;
CREATE TABLE IF NOT EXISTS `rcustomer_produk` (
  `id_produk` int(11) NOT NULL AUTO_INCREMENT,
  `kodcustomer` varchar(50) NOT NULL,
  `deskripsi_produk` varchar(255) NOT NULL,
  `harga_disepakati` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`id_produk`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rmaster_account
DROP TABLE IF EXISTS `rmaster_account`;
CREATE TABLE IF NOT EXISTS `rmaster_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kode_account` varchar(20) NOT NULL,
  `nama_account` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `urutan` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_account` (`kode_account`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rmaster_fasilitas
DROP TABLE IF EXISTS `rmaster_fasilitas`;
CREATE TABLE IF NOT EXISTS `rmaster_fasilitas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) NOT NULL,
  `nama_fasilitas` varchar(255) NOT NULL,
  `warna` varchar(7) DEFAULT '#6c757d',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rmaster_kategori_agenda
DROP TABLE IF EXISTS `rmaster_kategori_agenda`;
CREATE TABLE IF NOT EXISTS `rmaster_kategori_agenda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  `warna` varchar(10) NOT NULL DEFAULT '#7f8c8d',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rmaster_media
DROP TABLE IF EXISTS `rmaster_media`;
CREATE TABLE IF NOT EXISTS `rmaster_media` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama_media` varchar(100) NOT NULL,
  `warna` varchar(10) NOT NULL DEFAULT '#5bc0de',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_media` (`nama_media`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rmaster_pipeline
DROP TABLE IF EXISTS `rmaster_pipeline`;
CREATE TABLE IF NOT EXISTS `rmaster_pipeline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pipeline` varchar(100) NOT NULL,
  `singkatan` varchar(20) DEFAULT '',
  `warna` varchar(20) DEFAULT '#6c757d',
  `urutan` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rmaster_produk
DROP TABLE IF EXISTS `rmaster_produk`;
CREATE TABLE IF NOT EXISTS `rmaster_produk` (
  `id_master_produk` int(11) NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_master_produk`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rmaster_prospek_status
DROP TABLE IF EXISTS `rmaster_prospek_status`;
CREATE TABLE IF NOT EXISTS `rmaster_prospek_status` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama_status` varchar(100) NOT NULL,
  `label_display` varchar(100) NOT NULL,
  `warna` varchar(20) NOT NULL DEFAULT '#6c757d',
  `urutan` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_status` (`nama_status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rmaster_template_invoice
DROP TABLE IF EXISTS `rmaster_template_invoice`;
CREATE TABLE IF NOT EXISTS `rmaster_template_invoice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama_template` varchar(100) NOT NULL DEFAULT 'Default',
  `konten_html` text NOT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rmaster_tipe_tugas
DROP TABLE IF EXISTS `rmaster_tipe_tugas`;
CREATE TABLE IF NOT EXISTS `rmaster_tipe_tugas` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama_tipe` varchar(100) NOT NULL,
  `warna` varchar(10) NOT NULL DEFAULT '#5bc0de',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_tipe` (`nama_tipe`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.rtarget_revenue
DROP TABLE IF EXISTS `rtarget_revenue`;
CREATE TABLE IF NOT EXISTS `rtarget_revenue` (
  `tahun` int(11) NOT NULL,
  `target_value` bigint(20) NOT NULL,
  PRIMARY KEY (`tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.ruser
DROP TABLE IF EXISTS `ruser`;
CREATE TABLE IF NOT EXISTS `ruser` (
  `iduser` varchar(15) NOT NULL DEFAULT '',
  `passwd` varchar(15) NOT NULL DEFAULT '',
  `nama` varchar(50) NOT NULL DEFAULT '',
  `nik` varchar(50) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL,
  `inisial` varchar(4) NOT NULL,
  `stsaktif` tinyint(1) NOT NULL DEFAULT 0,
  `isLogin` tinyint(1) NOT NULL DEFAULT 0,
  `kodjab` smallint(6) NOT NULL DEFAULT 0,
  `divisi` varchar(100) DEFAULT NULL,
  `tgl_masuk` date DEFAULT NULL,
  `file_kontrak` varchar(255) DEFAULT NULL,
  `fingerprint_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`iduser`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.r_akses_client
DROP TABLE IF EXISTS `r_akses_client`;
CREATE TABLE IF NOT EXISTS `r_akses_client` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_user_client` int(11) unsigned NOT NULL,
  `kodcustomer` varchar(50) NOT NULL COMMENT 'Akses ke data perusahaan ini',
  `stsaktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_client` (`id_user_client`),
  KEY `idx_kodcustomer` (`kodcustomer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.r_user_client
DROP TABLE IF EXISTS `r_user_client`;
CREATE TABLE IF NOT EXISTS `r_user_client` (
  `id_user_client` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `iduser` varchar(50) NOT NULL COMMENT 'Username untuk login client',
  `nama` varchar(100) NOT NULL,
  `passwd` varchar(255) NOT NULL,
  `kodcustomer` varchar(50) NOT NULL COMMENT 'FK ke rcustomer, client milik perusahaan mana',
  `stsaktif` tinyint(1) NOT NULL DEFAULT 1,
  `tgl_dibuat` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_user_client`),
  UNIQUE KEY `iduser` (`iduser`),
  KEY `idx_kodcustomer` (`kodcustomer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.setting_hak_akses
DROP TABLE IF EXISTS `setting_hak_akses`;
CREATE TABLE IF NOT EXISTS `setting_hak_akses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `menu_nama` varchar(100) NOT NULL,
  `akses_admin` tinyint(1) DEFAULT 1,
  `akses_staff` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_menu` (`menu_nama`)
) ENGINE=InnoDB AUTO_INCREMENT=3378 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tagenda
DROP TABLE IF EXISTS `tagenda`;
CREATE TABLE IF NOT EXISTS `tagenda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kategori` varchar(50) DEFAULT 'Internal',
  `tanggal` date NOT NULL,
  `jam` time DEFAULT NULL COMMENT 'Jam kegiatan (opsional)',
  `tempat_kunjungan` varchar(255) NOT NULL,
  `agenda` text NOT NULL,
  `peserta` text NOT NULL COMMENT 'Nama-nama yang terlibat, dipisahkan koma',
  `dokumen` varchar(255) DEFAULT NULL COMMENT 'Nama file dokumen yang diupload',
  `created_by` varchar(50) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tanggal` (`tanggal`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.takun
DROP TABLE IF EXISTS `takun`;
CREATE TABLE IF NOT EXISTS `takun` (
  `kodakun` varchar(20) NOT NULL,
  `kodgroup` int(11) NOT NULL,
  `nmakun` varchar(100) NOT NULL,
  `tipe` enum('H','D') NOT NULL DEFAULT 'D',
  PRIMARY KEY (`kodakun`),
  KEY `fk_takun_tgroup_idx` (`kodgroup`),
  CONSTRAINT `fk_takun_tgroup` FOREIGN KEY (`kodgroup`) REFERENCES `tgroup_akun` (`kodgroup`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tangsuran
DROP TABLE IF EXISTS `tangsuran`;
CREATE TABLE IF NOT EXISTS `tangsuran` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_pinjaman` int(11) unsigned NOT NULL,
  `cicilan_ke` int(11) NOT NULL,
  `bulan` int(2) NOT NULL,
  `tahun` int(4) NOT NULL,
  `nominal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status_bayar` enum('Belum','Lunas') NOT NULL DEFAULT 'Belum',
  `tgl_bayar` datetime DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pinjaman` (`id_pinjaman`)
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tbl_divisi
DROP TABLE IF EXISTS `tbl_divisi`;
CREATE TABLE IF NOT EXISTS `tbl_divisi` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama_divisi` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tbl_hak_akses
DROP TABLE IF EXISTS `tbl_hak_akses`;
CREATE TABLE IF NOT EXISTS `tbl_hak_akses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `iduser` varchar(50) NOT NULL,
  `menu_nama` varchar(100) NOT NULL,
  `aktif` tinyint(1) DEFAULT 1 COMMENT '1=boleh akses, 0=tidak',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_user_menu` (`iduser`,`menu_nama`)
) ENGINE=InnoDB AUTO_INCREMENT=1279 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tbl_jabatan
DROP TABLE IF EXISTS `tbl_jabatan`;
CREATE TABLE IF NOT EXISTS `tbl_jabatan` (
  `kodjab` int(11) NOT NULL,
  `nama_jabatan` varchar(100) NOT NULL,
  PRIMARY KEY (`kodjab`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tbl_kode_surat
DROP TABLE IF EXISTS `tbl_kode_surat`;
CREATE TABLE IF NOT EXISTS `tbl_kode_surat` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_surat` varchar(50) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `dibuat_oleh` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_surat` (`nomor_surat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tbl_siswa_magang
DROP TABLE IF EXISTS `tbl_siswa_magang`;
CREATE TABLE IF NOT EXISTS `tbl_siswa_magang` (
  `id_magang` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `no_ktp` varchar(50) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `ttl` varchar(255) DEFAULT NULL,
  `alamat_asal` text DEFAULT NULL,
  `alamat_domisili` text DEFAULT NULL,
  `nama_orang_tua` varchar(255) DEFAULT NULL,
  `asal_instansi` varchar(255) DEFAULT NULL,
  `fakultas` varchar(150) DEFAULT NULL,
  `prodi` varchar(150) DEFAULT NULL,
  `pembimbing_instansi` varchar(255) DEFAULT NULL,
  `no_hp_pembimbing` varchar(50) DEFAULT NULL,
  `nik_magang` varchar(50) DEFAULT NULL,
  `file_foto_ktp` varchar(255) DEFAULT NULL,
  `file_dok_pengajuan` varchar(255) DEFAULT NULL,
  `file_dok_kontrak` varchar(255) DEFAULT NULL,
  `file_dok_asset` varchar(255) DEFAULT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `tgl_selesai` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_magang`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tclients_file
DROP TABLE IF EXISTS `tclients_file`;
CREATE TABLE IF NOT EXISTS `tclients_file` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kodcustomer` varchar(50) NOT NULL,
  `tipe_fasilitas` varchar(20) DEFAULT '',
  `nama_dokumen` varchar(255) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `nama_file_asli` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `uploaded_by` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tclient_update
DROP TABLE IF EXISTS `tclient_update`;
CREATE TABLE IF NOT EXISTS `tclient_update` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kodcustomer` varchar(50) NOT NULL,
  `kondisi_terbaru` text NOT NULL,
  `tgl_update` date NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tdaftar_content
DROP TABLE IF EXISTS `tdaftar_content`;
CREATE TABLE IF NOT EXISTS `tdaftar_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `judul_content` varchar(255) NOT NULL,
  `media` varchar(50) NOT NULL COMMENT 'IG, LinkedIn, Website',
  `created_by` varchar(50) NOT NULL,
  `tgl_publish` date NOT NULL,
  `aktual_publish` date DEFAULT NULL,
  `status` enum('Process','Selesai','Approved','Published') NOT NULL DEFAULT 'Process',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nilai_manager` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tdokumen_about
DROP TABLE IF EXISTS `tdokumen_about`;
CREATE TABLE IF NOT EXISTS `tdokumen_about` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama_dokumen` varchar(255) NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `divisi` varchar(100) NOT NULL DEFAULT '',
  `uploaded_by` varchar(50) DEFAULT NULL,
  `tgl_upload` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tdokumen_client
DROP TABLE IF EXISTS `tdokumen_client`;
CREATE TABLE IF NOT EXISTS `tdokumen_client` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kodcustomer` varchar(50) NOT NULL,
  `nama_dokumen` varchar(255) NOT NULL,
  `nama_file` varchar(255) NOT NULL COMMENT 'nama file di server (unik)',
  `nama_file_asli` varchar(255) NOT NULL COMMENT 'nama file asli saat upload',
  `keterangan` text DEFAULT NULL,
  `uploaded_by` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tdtllembur
DROP TABLE IF EXISTS `tdtllembur`;
CREATE TABLE IF NOT EXISTS `tdtllembur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idlembur` int(11) NOT NULL,
  `iduser_pegawai` varchar(15) NOT NULL,
  `tugas` text NOT NULL,
  `target` text DEFAULT NULL,
  `kodcustomer` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idlembur` (`idlembur`),
  KEY `iduser_pegawai` (`iduser_pegawai`),
  CONSTRAINT `fk_tdtllembur_lembur` FOREIGN KEY (`idlembur`) REFERENCES `tlembur` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tgaji
DROP TABLE IF EXISTS `tgaji`;
CREATE TABLE IF NOT EXISTS `tgaji` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iduser_pegawai` varchar(15) NOT NULL COMMENT 'FK ke ruser.iduser',
  `periode` varchar(7) NOT NULL COMMENT 'Format: YYYY-MM',
  `gaji_pokok` decimal(15,2) DEFAULT 0.00,
  `tunj_jabatan` decimal(15,2) DEFAULT 0.00,
  `tunj_perjalanan` decimal(15,2) DEFAULT 0.00,
  `lembur` decimal(15,2) DEFAULT 0.00,
  `bonus` decimal(15,2) DEFAULT 0.00,
  `bpjs_tk` decimal(15,2) DEFAULT 0.00,
  `bpjs_kesehatan` decimal(15,2) DEFAULT 0.00,
  `pot_pinjaman` decimal(15,2) DEFAULT 0.00,
  `pot_lain` decimal(15,2) DEFAULT 0.00,
  `sync_pinjaman` tinyint(1) DEFAULT 0,
  `total_terima` decimal(15,2) DEFAULT 0.00,
  `status_gaji` enum('Draft','Generated','Paid') DEFAULT 'Draft',
  `tgl_input` datetime DEFAULT NULL,
  `tgl_update` datetime DEFAULT NULL,
  `tgl_generate` datetime DEFAULT NULL,
  `tgl_bayar` datetime DEFAULT NULL,
  `bukti_tf` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pegawai` (`iduser_pegawai`),
  KEY `idx_periode` (`periode`),
  KEY `idx_status` (`status_gaji`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tgroup_akun
DROP TABLE IF EXISTS `tgroup_akun`;
CREATE TABLE IF NOT EXISTS `tgroup_akun` (
  `kodgroup` int(11) NOT NULL AUTO_INCREMENT,
  `nmgroup` varchar(100) NOT NULL,
  PRIMARY KEY (`kodgroup`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.timplementasi
DROP TABLE IF EXISTS `timplementasi`;
CREATE TABLE IF NOT EXISTS `timplementasi` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `iduser` varchar(15) NOT NULL,
  `kodcustomer` varchar(20) NOT NULL,
  `aktivitas` text NOT NULL,
  `tglmulai` date NOT NULL,
  `tglselesai` date NOT NULL,
  `userorder` varchar(15) NOT NULL,
  `userpj` varchar(15) NOT NULL,
  `stsdel` int(1) DEFAULT 0,
  `isselesai` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=belum selesai, 1=sudah selesai',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.timpl_check
DROP TABLE IF EXISTS `timpl_check`;
CREATE TABLE IF NOT EXISTS `timpl_check` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kodcustomer` varchar(50) NOT NULL,
  `tipe_fasilitas` varchar(20) DEFAULT 'REG',
  `tgl_kontrak` date DEFAULT NULL,
  `no_kontrak` varchar(100) DEFAULT NULL,
  `status_project` enum('Starting','On Track','Almost Done','Completed') DEFAULT 'Starting',
  `status_pembayaran` enum('Payment Tahap-1','Payment Tahap-2','Lunas') DEFAULT 'Payment Tahap-1',
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `stsdel` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_kodcustomer` (`kodcustomer`),
  KEY `idx_stsdel` (`stsdel`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.timpl_check_comm
DROP TABLE IF EXISTS `timpl_check_comm`;
CREATE TABLE IF NOT EXISTS `timpl_check_comm` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `impl_id` int(11) unsigned NOT NULL,
  `comm_date` date NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_impl_id` (`impl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.timpl_check_tasks
DROP TABLE IF EXISTS `timpl_check_tasks`;
CREATE TABLE IF NOT EXISTS `timpl_check_tasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `impl_id` int(11) unsigned NOT NULL,
  `deskripsi` text NOT NULL,
  `tgl_mulai` date DEFAULT NULL,
  `target_selesai` date DEFAULT NULL,
  `realisasi` date DEFAULT NULL,
  `dikerjakan_oleh` varchar(50) DEFAULT NULL,
  `pic_project` varchar(50) DEFAULT NULL,
  `is_done` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_impl_id` (`impl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tinvoice
DROP TABLE IF EXISTS `tinvoice`;
CREATE TABLE IF NOT EXISTS `tinvoice` (
  `noinvoice` varchar(30) NOT NULL,
  `kodcustomer` varchar(20) NOT NULL,
  `tglinvoice` date DEFAULT '2000-01-01',
  `bsuinvoice` double DEFAULT 0,
  `isiinvoice` varchar(100) DEFAULT '-',
  `validinvoice` date DEFAULT '2000-01-01',
  `iduserinvoice` varchar(15) DEFAULT '-',
  `isbayarinvoice` tinyint(1) DEFAULT 0,
  `tglbayarinvoice` date DEFAULT '2000-01-01',
  `bsubayarinvoice` double DEFAULT 0,
  `ketbayarinvoice` varchar(100) DEFAULT '-',
  `iduserbayarinvoice` varchar(15) DEFAULT '-',
  `noefaktur` varchar(30) DEFAULT '-',
  `tglefaktur` date DEFAULT '2000-01-01',
  `bsuefaktur` double DEFAULT 0,
  `isbayarefaktur` tinyint(1) DEFAULT 0,
  `tglbayarefaktur` date DEFAULT '2000-01-01',
  `iduserefaktur` varchar(15) DEFAULT '-',
  `nopph` varchar(50) DEFAULT NULL,
  `tglpph` date DEFAULT '2000-01-01',
  `bsupph` double DEFAULT 0,
  `isbayarpph` tinyint(1) DEFAULT NULL,
  `tglbayarpph` date DEFAULT '2000-01-01',
  `iduserpph` varchar(15) DEFAULT '-',
  `deskripsi_efaktur` text DEFAULT NULL,
  `file_efaktur` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`noinvoice`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tinvoice_new
DROP TABLE IF EXISTS `tinvoice_new`;
CREATE TABLE IF NOT EXISTS `tinvoice_new` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `noinvoice` varchar(100) NOT NULL,
  `kodcustomer` varchar(50) NOT NULL,
  `tipe_fasilitas` varchar(20) DEFAULT '',
  `tglinvoice` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `po_number` varchar(100) DEFAULT NULL,
  `subtotal` decimal(15,2) DEFAULT 0.00,
  `discount` decimal(15,2) DEFAULT 0.00,
  `dpp_amount` decimal(15,2) DEFAULT 0.00,
  `tax_rate` decimal(5,2) DEFAULT 12.00,
  `ppn_amount` decimal(15,2) DEFAULT 0.00,
  `is_ppn_suspended` tinyint(1) DEFAULT 0,
  `is_include_ppn` tinyint(1) DEFAULT 0,
  `pph23_rate` decimal(5,2) DEFAULT 2.00,
  `pph23_amount` decimal(15,2) DEFAULT 0.00,
  `grand_total` decimal(15,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `status` enum('draft','sent','paid','cancelled') DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `account` varchar(20) DEFAULT NULL,
  `iduserinvoice` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `stsdel` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `noinvoice` (`noinvoice`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tinvoice_new_items
DROP TABLE IF EXISTS `tinvoice_new_items`;
CREATE TABLE IF NOT EXISTS `tinvoice_new_items` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `noinvoice` varchar(100) NOT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `internal_description` text DEFAULT NULL,
  `qty` int(11) DEFAULT 1,
  `description` text NOT NULL,
  `unit_price` decimal(15,2) DEFAULT 0.00,
  `line_total` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_noinvoice` (`noinvoice`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tipkantor
DROP TABLE IF EXISTS `tipkantor`;
CREATE TABLE IF NOT EXISTS `tipkantor` (
  `noip` varchar(15) NOT NULL DEFAULT '-',
  `nmkantor` varchar(20) NOT NULL DEFAULT '-',
  PRIMARY KEY (`noip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tizin
DROP TABLE IF EXISTS `tizin`;
CREATE TABLE IF NOT EXISTS `tizin` (
  `idizin` bigint(20) NOT NULL AUTO_INCREMENT,
  `iduser` varchar(15) NOT NULL,
  `kategori` varchar(20) DEFAULT NULL,
  `lamanya` varchar(20) NOT NULL,
  `keperluan` text NOT NULL,
  `tglizin` date NOT NULL DEFAULT '0000-00-00',
  `isapprove` int(1) NOT NULL DEFAULT 0 COMMENT '1=setuju, 2=ditolak',
  `tglapprove` date NOT NULL DEFAULT '0000-00-00',
  `tglentri` date NOT NULL DEFAULT '0000-00-00',
  `stsdel` int(1) NOT NULL DEFAULT 0,
  `keterangan` text NOT NULL,
  `file_izin` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`idizin`)
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tkantong
DROP TABLE IF EXISTS `tkantong`;
CREATE TABLE IF NOT EXISTS `tkantong` (
  `id_kantong` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama_kantong` varchar(100) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `saldo_awal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_kantong`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tkas
DROP TABLE IF EXISTS `tkas`;
CREATE TABLE IF NOT EXISTS `tkas` (
  `idkas` int(11) NOT NULL AUTO_INCREMENT,
  `notransaksi` varchar(20) NOT NULL,
  `id_kantong` int(11) unsigned DEFAULT NULL,
  `tgltransaksi` date NOT NULL,
  `kodakun` varchar(20) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `satuan` varchar(50) DEFAULT NULL,
  `plusmin` enum('+','-') NOT NULL DEFAULT '-',
  `jumlah` decimal(10,2) DEFAULT NULL,
  `hargaunit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `totalharga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `iduser_proses` varchar(20) NOT NULL,
  `sts_approve` enum('B','Y') NOT NULL DEFAULT 'B',
  `keterangan` varchar(255) DEFAULT NULL,
  `bukti_kas` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idkas`),
  KEY `fk_tkas_takun_idx` (`kodakun`),
  KEY `fk_tkas_ruser_idx` (`iduser_proses`),
  KEY `idx_tgltransaksi` (`tgltransaksi`),
  KEY `idx_kantong` (`id_kantong`),
  KEY `idx_notransaksi` (`notransaksi`),
  CONSTRAINT `fk_tkas_takun` FOREIGN KEY (`kodakun`) REFERENCES `takun` (`kodakun`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=899 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tkas_saldo_awal
DROP TABLE IF EXISTS `tkas_saldo_awal`;
CREATE TABLE IF NOT EXISTS `tkas_saldo_awal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `saldo_awal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` varchar(255) DEFAULT NULL,
  `iduser_input` varchar(20) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tkehadiran
DROP TABLE IF EXISTS `tkehadiran`;
CREATE TABLE IF NOT EXISTS `tkehadiran` (
  `iduser` varchar(20) NOT NULL,
  `tanggal` date NOT NULL,
  `hadir` varchar(10) DEFAULT '',
  `pulang` varchar(10) DEFAULT '',
  `keterangan` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`iduser`,`tanggal`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tketentuan_perizinan
DROP TABLE IF EXISTS `tketentuan_perizinan`;
CREATE TABLE IF NOT EXISTS `tketentuan_perizinan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `isi_ketentuan` text NOT NULL,
  `updated_by` varchar(50) NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tlembur
DROP TABLE IF EXISTS `tlembur`;
CREATE TABLE IF NOT EXISTS `tlembur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deskripsi` text NOT NULL,
  `latarbelakang` text NOT NULL,
  `iduser_pengaju` varchar(15) NOT NULL,
  `tgl_lembur` date NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `tgl_pengajuan` datetime NOT NULL,
  `status_approval` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `iduser_approver` varchar(15) DEFAULT NULL,
  `tgl_approval` datetime DEFAULT NULL,
  `catatan_approval` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `iduser_pengaju` (`iduser_pengaju`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tletter_code
DROP TABLE IF EXISTS `tletter_code`;
CREATE TABLE IF NOT EXISTS `tletter_code` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_surat` varchar(50) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `dibuat_oleh` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_surat` (`nomor_surat`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tlog
DROP TABLE IF EXISTS `tlog`;
CREATE TABLE IF NOT EXISTS `tlog` (
  `idlog` bigint(20) NOT NULL AUTO_INCREMENT,
  `iduser` varchar(15) NOT NULL,
  `kodcustomer` varchar(20) NOT NULL,
  `jnsbisnis` varchar(1) NOT NULL DEFAULT 'M' COMMENT 'M=Maintenance, D=Developing, B=BIsnis, A=Administrasi',
  `tglorder` date NOT NULL,
  `fasorder` varchar(50) NOT NULL COMMENT 'Order melalui ..., terangkan dengan WA dari ssiapa atau email siapa',
  `desorder` longtext DEFAULT NULL,
  `deslayan` longtext DEFAULT NULL,
  `isselesai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=belum, 1=selesai',
  `tglselesai` date NOT NULL DEFAULT '0000-00-00',
  `userorder` varchar(15) NOT NULL DEFAULT '-',
  `tgltarget` date NOT NULL DEFAULT '0000-00-00' COMMENT 'Target Tanggal Selesai',
  `prioritas` varchar(1) NOT NULL COMMENT '1=Sangat Tinggi, 2=Tinggi, 3=Biasa',
  `stsdel` int(1) DEFAULT 0 COMMENT '0=belum delete, 1=sudah delete',
  `isupdate` int(1) DEFAULT 0 COMMENT '0=belum selesai, 1=selesai',
  `tglupdate` date DEFAULT '0000-00-00' COMMENT 'tgl update ke atas',
  `istesting` int(1) DEFAULT 0 COMMENT '0=belum testing, 1= sudah testing',
  `tgltesting` date DEFAULT '0000-00-00' COMMENT 'tanggal testing',
  `ketterlambat` text DEFAULT NULL,
  `nilai` enum('1','2','3','4','5') DEFAULT NULL,
  `independence` varchar(50) DEFAULT NULL,
  `quality` varchar(50) DEFAULT NULL,
  `file_uploads` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON metadata file uploads' CHECK (json_valid(`file_uploads`)),
  `idsprint` int(11) DEFAULT NULL,
  `difficulty` enum('Mudah','Medium','Sulit') DEFAULT NULL,
  `id_parent` int(11) DEFAULT NULL,
  PRIMARY KEY (`idlog`)
) ENGINE=MyISAM AUTO_INCREMENT=8464 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tlog_20250815
DROP TABLE IF EXISTS `tlog_20250815`;
CREATE TABLE IF NOT EXISTS `tlog_20250815` (
  `idlog` bigint(20) NOT NULL AUTO_INCREMENT,
  `iduser` varchar(15) NOT NULL,
  `kodcustomer` varchar(20) NOT NULL,
  `jnsbisnis` varchar(1) NOT NULL DEFAULT 'M' COMMENT 'M=Maintenance, D=Developing, B=BIsnis, A=Administrasi',
  `tglorder` date NOT NULL,
  `fasorder` varchar(50) NOT NULL COMMENT 'Order melalui ..., terangkan dengan WA dari ssiapa atau email siapa',
  `desorder` text NOT NULL,
  `deslayan` text NOT NULL COMMENT 'Tuliskan Tanggal dan Aktifitasnya',
  `isselesai` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=belum, 1=selesai',
  `tglselesai` date NOT NULL DEFAULT '0000-00-00',
  `userorder` varchar(15) NOT NULL DEFAULT '-',
  `tgltarget` date NOT NULL DEFAULT '0000-00-00' COMMENT 'Target Tanggal Selesai',
  `prioritas` varchar(1) NOT NULL COMMENT '1=Sangat Tinggi, 2=Tinggi, 3=Biasa',
  `stsdel` int(1) DEFAULT 0 COMMENT '0=belum delete, 1=sudah delete',
  `isupdate` int(1) DEFAULT 0 COMMENT '0=belum selesai, 1=selesai',
  `tglupdate` date DEFAULT '0000-00-00' COMMENT 'tgl update ke atas',
  `istesting` int(1) DEFAULT 0 COMMENT '0=belum testing, 1= sudah testing',
  `tgltesting` date DEFAULT '0000-00-00' COMMENT 'tanggal testing',
  `ketterlambat` text DEFAULT NULL,
  `nilai` enum('1','2','3','4','5') DEFAULT NULL,
  PRIMARY KEY (`idlog`)
) ENGINE=MyISAM AUTO_INCREMENT=6086 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tlog_cs
DROP TABLE IF EXISTS `tlog_cs`;
CREATE TABLE IF NOT EXISTS `tlog_cs` (
  `idlog_cs` bigint(20) NOT NULL AUTO_INCREMENT,
  `iduser` varchar(15) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time DEFAULT NULL,
  `durasi` varchar(100) DEFAULT NULL,
  `kodcustomer` varchar(20) DEFAULT NULL,
  `nama_klien` varchar(150) DEFAULT NULL,
  `nomor_klien` varchar(50) DEFAULT NULL,
  `prioritas` varchar(50) DEFAULT NULL,
  `kategori_masalah` varchar(100) DEFAULT NULL,
  `modul_menu` varchar(150) DEFAULT NULL,
  `deskripsi_masalah` longtext DEFAULT NULL,
  `solusi` longtext DEFAULT NULL,
  `estimasi` datetime DEFAULT NULL,
  `catatan` longtext DEFAULT NULL,
  `stsdel` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idlog_cs`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tminutes_of_meeting
DROP TABLE IF EXISTS `tminutes_of_meeting`;
CREATE TABLE IF NOT EXISTS `tminutes_of_meeting` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `kodcustomer` varchar(50) NOT NULL,
  `tgl_meeting` date NOT NULL,
  `waktu_meeting` varchar(50) NOT NULL,
  `lokasi_meeting` varchar(100) NOT NULL,
  `media_meeting` varchar(50) DEFAULT NULL,
  `topik_diskusi` text DEFAULT NULL,
  `peserta_klien` text DEFAULT NULL,
  `peserta_internal` text DEFAULT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `foto_kegiatan` varchar(255) DEFAULT NULL,
  `youtube_link` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.toutstanding_payment
DROP TABLE IF EXISTS `toutstanding_payment`;
CREATE TABLE IF NOT EXISTS `toutstanding_payment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `income` varchar(255) NOT NULL,
  `value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('Listed','Invoice Sent') NOT NULL DEFAULT 'Listed',
  `payment_target_bulan` int(2) NOT NULL,
  `payment_target_tahun` int(4) NOT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tpassword_manager
DROP TABLE IF EXISTS `tpassword_manager`;
CREATE TABLE IF NOT EXISTS `tpassword_manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kodcustomer` varchar(20) NOT NULL,
  `nama_aplikasi` varchar(150) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `auth_secret` text DEFAULT NULL,
  `qr_mfa_file` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tpinjaman
DROP TABLE IF EXISTS `tpinjaman`;
CREATE TABLE IF NOT EXISTS `tpinjaman` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `iduser_pemohon` varchar(20) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `jabatan_pemohon` varchar(100) DEFAULT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `nominal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `keperluan` text NOT NULL,
  `tenor` int(11) NOT NULL DEFAULT 1,
  `cicilan_perbulan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `periode_awal` varchar(7) DEFAULT NULL,
  `periode_akhir` varchar(7) DEFAULT NULL,
  `jumlah_dibayar` decimal(15,2) DEFAULT 0.00,
  `sisa_pinjaman` decimal(15,2) DEFAULT 0.00,
  `tgl_pengajuan` datetime DEFAULT current_timestamp(),
  `status_approval` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `catatan_approval` varchar(255) DEFAULT NULL,
  `tgl_approval` datetime DEFAULT NULL,
  `iduser_approval` varchar(20) DEFAULT NULL,
  `status_lunas` enum('Belum','Lunas') NOT NULL DEFAULT 'Belum',
  `file_dokumen` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pemohon` (`iduser_pemohon`),
  KEY `idx_status` (`status_approval`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tprospek
DROP TABLE IF EXISTS `tprospek`;
CREATE TABLE IF NOT EXISTS `tprospek` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nama_prospek` varchar(255) NOT NULL,
  `nama_pic` varchar(255) NOT NULL,
  `bidang_bisnis` varchar(255) DEFAULT '',
  `alamat` text DEFAULT NULL,
  `tipe_fasilitas` varchar(20) NOT NULL DEFAULT 'REG',
  `penawaran_produk` int(11) DEFAULT NULL COMMENT 'FK ke rmaster_produk.id_master_produk',
  `harga_penawaran` decimal(15,2) DEFAULT 0.00,
  `pipeline_stage` varchar(100) NOT NULL DEFAULT '',
  `status` varchar(30) NOT NULL DEFAULT '',
  `estimasi_closing` varchar(20) DEFAULT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tprospek_komunikasi
DROP TABLE IF EXISTS `tprospek_komunikasi`;
CREATE TABLE IF NOT EXISTS `tprospek_komunikasi` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_prospek` int(11) unsigned NOT NULL,
  `tgl_komunikasi` date NOT NULL,
  `deskripsi` text NOT NULL,
  `handled_by` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tsetting_poin
DROP TABLE IF EXISTS `tsetting_poin`;
CREATE TABLE IF NOT EXISTS `tsetting_poin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` enum('Tingkat Kesulitan','Kecepatan','Kemandirian','Keterlambatan','Kualitas','Skema Bonus','Kedisiplinan') NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_label` varchar(100) NOT NULL,
  `point_value` int(11) NOT NULL DEFAULT 0,
  `difficulty_ref` enum('Mudah','Medium','Sulit') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_setting` (`category`,`setting_key`,`difficulty_ref`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tsprint
DROP TABLE IF EXISTS `tsprint`;
CREATE TABLE IF NOT EXISTS `tsprint` (
  `idsprint` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(200) NOT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `status` varchar(20) DEFAULT 'aktif',
  `iduser_create` varchar(20) NOT NULL,
  `pic_sprint` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `file_uploads` text DEFAULT NULL,
  `deskripsi` longtext DEFAULT NULL,
  PRIMARY KEY (`idsprint`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tsprint_plan
DROP TABLE IF EXISTS `tsprint_plan`;
CREATE TABLE IF NOT EXISTS `tsprint_plan` (
  `idplan` int(11) NOT NULL AUTO_INCREMENT,
  `idsprint` int(11) NOT NULL,
  `judul_plan` varchar(500) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `iduser_assign` varchar(20) NOT NULL,
  `status` varchar(20) DEFAULT 'belum',
  `prioritas` int(11) DEFAULT 3,
  `progress` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`idplan`),
  KEY `idsprint` (`idsprint`),
  CONSTRAINT `tsprint_plan_ibfk_1` FOREIGN KEY (`idsprint`) REFERENCES `tsprint` (`idsprint`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.tsupporting_task
DROP TABLE IF EXISTS `tsupporting_task`;
CREATE TABLE IF NOT EXISTS `tsupporting_task` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `deskripsi_tugas` text NOT NULL,
  `tipe_tugas` varchar(100) NOT NULL,
  `resource` varchar(50) NOT NULL COMMENT 'iduser dari divisi Marketing & Bisnis',
  `created_by` varchar(50) DEFAULT NULL,
  `tgl_mulai` date NOT NULL,
  `tgl_selesai` date NOT NULL,
  `tgl_aktualisasi` date DEFAULT NULL,
  `status` enum('Dijadwalkan','Dalam Proses','Selesai','Ditunda','Terlambat') NOT NULL DEFAULT 'Dijadwalkan',
  `updated_by` varchar(50) DEFAULT NULL COMMENT 'iduser yang mengupdate status',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `nilai_manager` int(11) DEFAULT NULL COMMENT 'Nilai dari Manager Marketing',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.ttanggungjawab_lembur
DROP TABLE IF EXISTS `ttanggungjawab_lembur`;
CREATE TABLE IF NOT EXISTS `ttanggungjawab_lembur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idlembur` int(11) NOT NULL,
  `idtask` int(11) DEFAULT NULL,
  `status_lembur` int(1) NOT NULL,
  `kesimpulan` text NOT NULL,
  `foto` text DEFAULT NULL,
  `iduser_pelapor` varchar(15) NOT NULL,
  `tgl_lapor` datetime NOT NULL,
  `tgl_update` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idlembur` (`idlembur`),
  KEY `iduser_pelapor` (`iduser_pelapor`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.ttax_invoice
DROP TABLE IF EXISTS `ttax_invoice`;
CREATE TABLE IF NOT EXISTS `ttax_invoice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tanggal_efaktur` date NOT NULL,
  `no_efaktur` varchar(100) NOT NULL,
  `kodcustomer` varchar(50) NOT NULL,
  `invoice_ref` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `file_efaktur` varchar(255) DEFAULT NULL,
  `created_by` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.t_followup_client
DROP TABLE IF EXISTS `t_followup_client`;
CREATE TABLE IF NOT EXISTS `t_followup_client` (
  `id_followup` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `id_log` int(11) NOT NULL COMMENT 'FK ke tlog, log mana yang di-follow up',
  `id_user_client` int(11) unsigned NOT NULL COMMENT 'Siapa client yang mengirim pesan',
  `pesan` text NOT NULL,
  `tgl_kirim` datetime DEFAULT current_timestamp(),
  `status_baca` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Belum dibaca, 1 = Sudah dibaca tim internal',
  PRIMARY KEY (`id_followup`),
  KEY `idx_id_log` (`id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for table logklikdsi.t_notif_internal
DROP TABLE IF EXISTS `t_notif_internal`;
CREATE TABLE IF NOT EXISTS `t_notif_internal` (
  `id_notif` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `jenis_notif` varchar(50) NOT NULL COMMENT 'Misal: Followup Client',
  `id_referensi` int(11) unsigned NOT NULL COMMENT 'ID dari tabel referensi (misal id_followup)',
  `pesan_notif` varchar(255) NOT NULL,
  `status_baca` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Unread, 1 = Read',
  `tgl_notif` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_notif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data exporting was unselected.

-- Dumping structure for view logklikdsi.vw_log_report
DROP VIEW IF EXISTS `vw_log_report`;
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `vw_log_report` (
	`assigned` VARCHAR(1) NOT NULL COLLATE 'latin1_swedish_ci',
	`client` VARCHAR(1) NOT NULL COLLATE 'latin1_swedish_ci',
	`task` LONGTEXT NULL COLLATE 'latin1_swedish_ci',
	`service` LONGTEXT NULL COLLATE 'latin1_swedish_ci',
	`order_date` DATE NOT NULL,
	`due_date` DATE NOT NULL COMMENT 'Target Tanggal Selesai',
	`actual_date` DATE NOT NULL,
	`status` TINYINT(4) NOT NULL COMMENT '0=belum, 1=selesai',
	`is_late` INT(1) NOT NULL,
	`reason` TEXT NULL COLLATE 'latin1_swedish_ci'
) ENGINE=MyISAM;

-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `vw_log_report`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vw_log_report` AS select `a`.`userorder` AS `assigned`,`b`.`nmcustomer` AS `client`,`a`.`desorder` AS `task`,`a`.`deslayan` AS `service`,`a`.`tglorder` AS `order_date`,`a`.`tgltarget` AS `due_date`,`a`.`tglselesai` AS `actual_date`,`a`.`isselesai` AS `status`,if(`a`.`tglselesai` > `a`.`tgltarget`,1,0) AS `is_late`,`a`.`ketterlambat` AS `reason` from (`tlog` `a` join `rcustomer` `b` on(`a`.`kodcustomer` = `b`.`kodcustomer`)) ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
