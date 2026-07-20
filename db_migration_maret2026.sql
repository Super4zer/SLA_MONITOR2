-- ===========================================
-- 05/03/2026
-- ===========================================

-- 1. Tabel Saldo Awal Kas
CREATE TABLE IF NOT EXISTS `tkas_saldo_awal` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `bulan` INT(2) NOT NULL,
  `tahun` INT(4) NOT NULL,
  `saldo_awal` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `keterangan` VARCHAR(255) NULL,
  `iduser_input` VARCHAR(20) NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unik_bulan_tahun` (`bulan`, `tahun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Subtask: Tambah kolom id_parent di tlog untuk relasi parent-child
ALTER TABLE tlog ADD COLUMN IF NOT EXISTS `id_parent` INT DEFAULT NULL;


-- ===========================================
-- 06/03/2026
-- ===========================================

-- 1. Tabel Kode Surat (Administrasi - Marketing)
CREATE TABLE IF NOT EXISTS tkode_surat (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(50) NOT NULL UNIQUE,
    tanggal_surat DATE NOT NULL,
    deskripsi TEXT,
    dibuat_oleh VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2. Penambahan kolom pada tabel rcustomer (Daftar Client)
--    Menambahkan: NPWP, Valid Until, dan memperbarui keterangan Status
ALTER TABLE rcustomer 
    ADD COLUMN IF NOT EXISTS `npwp` VARCHAR(30) DEFAULT '' AFTER `almtcustomer`,
    ADD COLUMN IF NOT EXISTS `valid_until` DATE DEFAULT NULL AFTER `npwp`,
    MODIFY COLUMN `status` TINYINT(1) NOT NULL DEFAULT 1 
        COMMENT '1=Active, 0=Suspended';

-- 2.1 Tabel Kontak Client (Narahubung)
CREATE TABLE IF NOT EXISTS `rcustomer_kontak` (
    `id_kontak` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `kodcustomer` VARCHAR(50) NOT NULL,
    `nama_kontak` VARCHAR(100) NOT NULL,
    `no_wa` VARCHAR(30),
    `email` VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2.2 Tabel Produk Client
CREATE TABLE IF NOT EXISTS `rcustomer_produk` (
    `id_produk` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `kodcustomer` VARCHAR(50) NOT NULL,
    `deskripsi_produk` VARCHAR(255) NOT NULL,
    `harga_disepakati` DECIMAL(15,2) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Tabel Daftar Content (Marketing - Pemasaran)
CREATE TABLE IF NOT EXISTS `tdaftar_content` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `judul_content` VARCHAR(255) NOT NULL,
    `media` VARCHAR(50) NOT NULL COMMENT 'IG, LinkedIn, Website',
    `created_by` VARCHAR(50) NOT NULL,
    `tgl_publish` DATE NOT NULL,
    `aktual_publish` DATE DEFAULT NULL,
    `status` ENUM('Process', 'Approved', 'Published') NOT NULL DEFAULT 'Process',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- ===========================================
-- 09/03/2026
-- ===========================================

-- Clients File - Tasking
CREATE TABLE IF NOT EXISTS `tclients_file` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kodcustomer` VARCHAR(50) NOT NULL,
    `nama_dokumen` VARCHAR(255) NOT NULL,
    `nama_file` VARCHAR(255) NOT NULL,
    `nama_file_asli` VARCHAR(255) NOT NULL ,
    `keterangan` TEXT,
    `uploaded_by` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- Tabel Minutes of Meeting (MoM)
CREATE TABLE IF NOT EXISTS `tminutes_of_meeting` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kodcustomer` VARCHAR(50) NOT NULL,
    `tgl_meeting` DATE NOT NULL,
    `waktu_meeting` VARCHAR(50) NOT NULL,
    `lokasi_meeting` VARCHAR(100) NOT NULL,
    `media_meeting` VARCHAR(50),
    `topik_diskusi` TEXT,
    `peserta_klien` TEXT,
    `peserta_internal` TEXT,
    `created_by` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel Tugas Pendukung (Marketing - Pemasaran)
CREATE TABLE IF NOT EXISTS `ttugas_pendukung` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `deskripsi_tugas` TEXT NOT NULL,
    `tipe_tugas` ENUM('Administrasi', 'Asistensi', 'Pelatihan', 'Pemasaran') NOT NULL,
    `resource` VARCHAR(50) NOT NULL COMMENT 'iduser dari divisi Marketing & Bisnis',
    `tgl_mulai` DATE NOT NULL,
    `tgl_selesai` DATE NOT NULL,
    `status` ENUM('Belum', 'Selesai') NOT NULL DEFAULT 'Belum' COMMENT 'Hanya bisa diupdate oleh Manager Marketing & Bisnis',
    `updated_by` VARCHAR(50) DEFAULT NULL COMMENT 'iduser yang mengupdate status',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `nilai_manager` INT(11) DEFAULT NULL COMMENT 'Nilai dari Manager Marketing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================================
-- 10/03/2026
-- ===========================================

-- Tabel Master Deskripsi Produk
CREATE TABLE IF NOT EXISTS `rmaster_produk` (
    `id_master_produk` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `nama_produk` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================================
-- 11/03/2026 
-- ===========================================

-- LIST PROSPEK 
-- Tabel Master Tipe Fasilitas
CREATE TABLE IF NOT EXISTS `rmaster_fasilitas` (
    `id` INT(11) AUTO_INCREMENT PRIMARY KEY,
    `kode` VARCHAR(20) NOT NULL UNIQUE,
    `nama_fasilitas` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data default tipe fasilitas
INSERT IGNORE INTO `rmaster_fasilitas` (`kode`, `nama_fasilitas`) VALUES
('KABER', 'Kawasan Berikat'),
('KEK', 'Kawasan Ekonomi Khusus'),
('KITE', 'Kemudahan Impor Tujuan Ekspor'),
('GB', 'Gudang Berikat'),
('REG', 'Reguler Manufacturing');

-- Tabel Prospek
CREATE TABLE IF NOT EXISTS `tprospek` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_prospek` VARCHAR(255) NOT NULL,
    `nama_pic` VARCHAR(255) NOT NULL,
    `bidang_bisnis` VARCHAR(255) DEFAULT '',
    `alamat` TEXT,
    `tipe_fasilitas` VARCHAR(20) NOT NULL DEFAULT 'REG',
    `penawaran_produk` INT(11) DEFAULT NULL COMMENT 'FK ke rmaster_produk.id_master_produk',
    `harga_penawaran` DECIMAL(15,2) DEFAULT 0,
    `status` ENUM('Leads','Cold Prospect','Warm Prospect','Hot Prospect','Deal','Pending/Cancel') NOT NULL DEFAULT 'Leads',
    `created_by` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel Riwayat Komunikasi Prospek
CREATE TABLE IF NOT EXISTS `tprospek_komunikasi` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_prospek` INT(11) UNSIGNED NOT NULL,
    `tgl_komunikasi` DATE NOT NULL,
    `deskripsi` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================================
-- 16/03/2026
-- ===========================================

-- Update Client (Resume Kondisi Terbaru Klien)
CREATE TABLE IF NOT EXISTS `tclient_update` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kodcustomer` VARCHAR(50) NOT NULL,
    `kondisi_terbaru` TEXT NOT NULL,
    `tgl_update` DATE NOT NULL,
    `created_by` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================================
-- 27/03/2026
-- ===========================================

-- E-Faktur Redesign: tambah kolom deskripsi dan file upload
ALTER TABLE tinvoice ADD COLUMN IF NOT EXISTS `deskripsi_efaktur` TEXT NULL;
ALTER TABLE tinvoice ADD COLUMN IF NOT EXISTS `file_efaktur` VARCHAR(255) NULL;



-- ===========================================
-- 30/03/2026
-- ===========================================

-- Rename tabel Kode Surat → Letter Code (skip jika sudah di-rename)
DROP PROCEDURE IF EXISTS _rename_tkode_surat;
DELIMITER //
CREATE PROCEDURE _rename_tkode_surat()
BEGIN
    DECLARE src_exists INT DEFAULT 0;
    DECLARE tgt_exists INT DEFAULT 0;
    SELECT COUNT(*) INTO src_exists FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'tkode_surat';
    SELECT COUNT(*) INTO tgt_exists FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'tletter_code';
    IF src_exists > 0 AND tgt_exists = 0 THEN
        RENAME TABLE tkode_surat TO tletter_code;
    ELSEIF src_exists > 0 AND tgt_exists > 0 THEN
        DROP TABLE tkode_surat;
    END IF;
END //
DELIMITER ;
CALL _rename_tkode_surat();
DROP PROCEDURE IF EXISTS _rename_tkode_surat;

-- Rename menu Kode Surat → Letter Code (safe: hapus duplikat lama)
UPDATE IGNORE tbl_hak_akses SET menu_nama = 'Letter Code' WHERE menu_nama = 'Kode Surat';
DELETE FROM tbl_hak_akses WHERE menu_nama = 'Kode Surat';
UPDATE IGNORE setting_hak_akses SET menu_nama = 'Letter Code' WHERE menu_nama = 'Kode Surat';
DELETE FROM setting_hak_akses WHERE menu_nama = 'Kode Surat';

-- Tambahan Kolom Image & Youtube Embed pada Minutes of Meeting
ALTER TABLE tminutes_of_meeting ADD COLUMN IF NOT EXISTS foto_kegiatan VARCHAR(255) DEFAULT NULL;
ALTER TABLE tminutes_of_meeting ADD COLUMN IF NOT EXISTS youtube_link VARCHAR(255) DEFAULT NULL;

-- Rename ttugas_pendukung ke tsupporting_task (safe: skip jika sudah di-rename)
DROP PROCEDURE IF EXISTS _rename_ttugas;
DELIMITER //
CREATE PROCEDURE _rename_ttugas()
BEGIN
    DECLARE src_exists INT DEFAULT 0;
    DECLARE tgt_exists INT DEFAULT 0;
    SELECT COUNT(*) INTO src_exists FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'ttugas_pendukung';
    SELECT COUNT(*) INTO tgt_exists FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'tsupporting_task';
    IF src_exists > 0 AND tgt_exists = 0 THEN
        RENAME TABLE ttugas_pendukung TO tsupporting_task;
    ELSEIF src_exists > 0 AND tgt_exists > 0 THEN
        DROP TABLE ttugas_pendukung;
    END IF;
END //
DELIMITER ;
CALL _rename_ttugas();
DROP PROCEDURE IF EXISTS _rename_ttugas;
ALTER TABLE tsupporting_task ADD COLUMN IF NOT EXISTS tgl_aktualisasi DATE DEFAULT NULL AFTER tgl_selesai;

UPDATE IGNORE tbl_hak_akses SET menu_nama = 'Supporting Task' WHERE menu_nama = 'Tugas Pendukung';
DELETE FROM tbl_hak_akses WHERE menu_nama = 'Tugas Pendukung';
UPDATE IGNORE setting_hak_akses SET menu_nama = 'Supporting Task' WHERE menu_nama = 'Tugas Pendukung';
DELETE FROM setting_hak_akses WHERE menu_nama = 'Tugas Pendukung';

-- Tambah kolom nilai_manager ke tsupporting_task jika belum ada
ALTER TABLE tsupporting_task ADD COLUMN IF NOT EXISTS nilai_manager INT(11) DEFAULT NULL;

-- Rename menu Dokumen Client → Clients File
UPDATE IGNORE tbl_hak_akses SET menu_nama = 'Clients File' WHERE menu_nama = 'Dokumen Client';
DELETE FROM tbl_hak_akses WHERE menu_nama = 'Dokumen Client';
UPDATE IGNORE setting_hak_akses SET menu_nama = 'Clients File' WHERE menu_nama = 'Dokumen Client';
DELETE FROM setting_hak_akses WHERE menu_nama = 'Dokumen Client';

-- ===========================================
-- 01/04/2026
-- ===========================================

-- Tambah kolom Tanggal Kontrak pada tabel rcustomer (Client List)
ALTER TABLE `rcustomer` ADD COLUMN IF NOT EXISTS `tgl_kontrak` DATE DEFAULT NULL AFTER `npwp`;

-- Rename menu tab 'Customer' → 'Client List' di tabel hak akses
-- Hapus entry 'Client List' lama jika ada (hindari konflik duplikat unique key)
DELETE FROM `tbl_hak_akses` WHERE `menu_nama` = 'Client List';
DELETE FROM `setting_hak_akses` WHERE `menu_nama` = 'Client List';
-- Rename: nilai aktif/nonaktif per user ikut terbawa
UPDATE `tbl_hak_akses` SET `menu_nama` = 'Client List' WHERE `menu_nama` = 'Customer';
UPDATE `setting_hak_akses` SET `menu_nama` = 'Client List' WHERE `menu_nama` = 'Customer';
-- Pastikan master menu punya entry 'Client List'
INSERT IGNORE INTO `setting_hak_akses` (`menu_nama`) VALUES ('Client List');


-- Revisi Perizinan: Tambah kolom file upload dan ketentuan perizinan 
-- Tambah kolom file_izin di tabel tizin (untuk upload gambar pengajuan izin)
SET sql_mode = '';
ALTER TABLE tizin ADD COLUMN IF NOT EXISTS `file_izin` VARCHAR(255) DEFAULT NULL;
-- Tabel Ketentuan Perizinan (global setting, diinput oleh GM kodjab=2)
CREATE TABLE IF NOT EXISTS `tketentuan_perizinan` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `isi_ketentuan` TEXT NOT NULL,
    `updated_by` VARCHAR(50) NOT NULL,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
-- Insert default row (hanya 1 row global)
INSERT IGNORE INTO `tketentuan_perizinan` (`id`, `isi_ketentuan`, `updated_by`) 
VALUES (1, '1.\n2.\n3.', 'system');

-- ===========================================
-- 04/04/2026 - Revisi Prospect List
-- ===========================================

-- 1. Tambah kolom pipeline_stage pada tprospek (default kosong, user harus isi manual)
ALTER TABLE `tprospek` ADD COLUMN IF NOT EXISTS `pipeline_stage` VARCHAR(30) NOT NULL DEFAULT '' AFTER `harga_penawaran`;
-- 2. Tambah kolom estimasi_closing pada tprospek
ALTER TABLE `tprospek` ADD COLUMN IF NOT EXISTS `estimasi_closing` VARCHAR(20) DEFAULT NULL AFTER `status`;
-- 3. Ubah kolom status dari ENUM ke VARCHAR (default kosong)
ALTER TABLE `tprospek` MODIFY COLUMN `status` VARCHAR(30) NOT NULL DEFAULT '';
-- 4. Tambah warna pada master fasilitas
ALTER TABLE `rmaster_fasilitas` ADD COLUMN IF NOT EXISTS `warna` VARCHAR(7) DEFAULT '#6c757d' AFTER `nama_fasilitas`;

-- Update warna default untuk fasilitas yang sudah ada
UPDATE `rmaster_fasilitas` SET `warna` = '#e74c3c' WHERE `kode` = 'KABER' AND (`warna` IS NULL OR `warna` = '#6c757d');
UPDATE `rmaster_fasilitas` SET `warna` = '#3498db' WHERE `kode` = 'KEK' AND (`warna` IS NULL OR `warna` = '#6c757d');
UPDATE `rmaster_fasilitas` SET `warna` = '#e67e22' WHERE `kode` = 'KITE' AND (`warna` IS NULL OR `warna` = '#6c757d');
UPDATE `rmaster_fasilitas` SET `warna` = '#27ae60' WHERE `kode` = 'GB' AND (`warna` IS NULL OR `warna` = '#6c757d');
UPDATE `rmaster_fasilitas` SET `warna` = '#9b59b6' WHERE `kode` = 'REG' AND (`warna` IS NULL OR `warna` = '#6c757d');

-- ===========================================
-- Outstanding Payment
-- ===========================================
CREATE TABLE IF NOT EXISTS `toutstanding_payment` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `income` VARCHAR(255) NOT NULL,
    `value` DECIMAL(15,2) NOT NULL DEFAULT 0,
    `status` ENUM('Listed', 'Invoice Sent') NOT NULL DEFAULT 'Listed',
    `payment_target_bulan` INT(2) NOT NULL,
    `payment_target_tahun` INT(4) NOT NULL,
    `created_by` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `setting_hak_akses` (`menu_nama`) VALUES ('Outstanding Payment');

-- ===========================================
-- 06/04/2026 - Invoice Baru (Multi-Item + PDF)
-- ===========================================

-- 1. Tabel Master Invoice Baru
CREATE TABLE IF NOT EXISTS `tinvoice_new` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `noinvoice` VARCHAR(100) NOT NULL UNIQUE,
    `kodcustomer` VARCHAR(50) NOT NULL,
    `tglinvoice` DATE NOT NULL ,
    `due_date` DATE NOT NULL,
    `po_number` VARCHAR(100) DEFAULT NULL ,

    `subtotal` DECIMAL(15,2) DEFAULT 0,
    `discount` DECIMAL(15,2) DEFAULT 0,
    `dpp_amount` DECIMAL(15,2) DEFAULT 0,
    `tax_rate` DECIMAL(5,2) DEFAULT 12,
    `ppn_amount` DECIMAL(15,2) DEFAULT 0,
    `pph23_rate` DECIMAL(5,2) DEFAULT 2,
    `pph23_amount` DECIMAL(15,2) DEFAULT 0,
    `grand_total` DECIMAL(15,2) DEFAULT 0,

    `notes` TEXT DEFAULT NULL,
    `status` ENUM('draft','sent','paid'),
    `payment_date` DATE DEFAULT NULL,

    `iduserinvoice` VARCHAR(50) NOT NULL ,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `stsdel` TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tabel Detail Item Invoice
CREATE TABLE IF NOT EXISTS `tinvoice_new_items` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `noinvoice` VARCHAR(100) NOT NULL,
    `item_name` VARCHAR(255) DEFAULT NULL,
    `qty` INT(11) DEFAULT 1,
    `description` TEXT NOT NULL,
    `unit_price` DECIMAL(15,2) DEFAULT 0,
    `line_total` DECIMAL(15,2) DEFAULT 0,
    INDEX `idx_noinvoice` (`noinvoice`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================================
-- 07/04/2026 - Sales Pipeline Target Module
-- ===========================================
CREATE TABLE IF NOT EXISTS `rtarget_revenue` (
    `tahun` INT PRIMARY KEY,
    `target_value` BIGINT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================================
-- 08/04/2026 - Implementation Check Module
-- ===========================================

CREATE TABLE IF NOT EXISTS `timpl_check` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kodcustomer` VARCHAR(50) NOT NULL,
    `tipe_fasilitas` VARCHAR(20) DEFAULT 'REG',
    `tgl_kontrak` DATE DEFAULT NULL,
    `no_kontrak` VARCHAR(100) DEFAULT NULL,
    `status_project` ENUM('Starting','On Track','Almost Done') DEFAULT 'Starting',
    `status_pembayaran` ENUM('Payment Tahap-1','Payment Tahap-2','Lunas') DEFAULT 'Payment Tahap-1',
    `created_by` VARCHAR(50) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `stsdel` TINYINT(1) DEFAULT 0,
    INDEX `idx_kodcustomer` (`kodcustomer`),
    INDEX `idx_stsdel` (`stsdel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `timpl_check_tasks` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `impl_id` INT(11) UNSIGNED NOT NULL,
    `deskripsi` TEXT NOT NULL,
    `tgl_mulai` DATE DEFAULT NULL,
    `target_selesai` DATE DEFAULT NULL,
    `realisasi` DATE DEFAULT NULL,
    `dikerjakan_oleh` VARCHAR(50) DEFAULT NULL,
    `pic_project` VARCHAR(50) DEFAULT NULL,
    `is_done` TINYINT(1) DEFAULT 0,
    INDEX `idx_impl_id` (`impl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `timpl_check_comm` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `impl_id` INT(11) UNSIGNED NOT NULL,
    `comm_date` DATE NOT NULL,
    `description` TEXT NOT NULL,
    INDEX `idx_impl_id` (`impl_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1. Tax Invoice Module

CREATE TABLE IF NOT EXISTS `ttax_invoice` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `tanggal_efaktur` DATE NOT NULL,
    `no_efaktur` VARCHAR(100) NOT NULL,
    `kodcustomer` VARCHAR(50) NOT NULL,
    `invoice_ref` VARCHAR(100) DEFAULT NULL,
    `deskripsi` TEXT,
    `file_efaktur` VARCHAR(255) DEFAULT NULL,
    `created_by` VARCHAR(50) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================================
-- 09/04/2026 - Invoice: Tambah Account & Filter
-- ===========================================

-- 1. Tabel Master Account 
CREATE TABLE IF NOT EXISTS `rmaster_account` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `kode_account` VARCHAR(20) NOT NULL UNIQUE,
    `nama_account` VARCHAR(100) NOT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `urutan` INT(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Data default account
INSERT IGNORE INTO `rmaster_account` (`kode_account`, `nama_account`, `urutan`) VALUES
('BCAS', 'BCA Syariah', 1),
('CASH', 'Cash', 2);

-- 2. Tambah kolom account pada tabel tinvoice_new
ALTER TABLE `tinvoice_new` ADD COLUMN IF NOT EXISTS `account` VARCHAR(20) DEFAULT NULL AFTER `payment_date`;

-- ===========================================
-- 13/04/2026 - Supporting Task: Tambah status Terlambat
-- ===========================================
ALTER TABLE `tsupporting_task` MODIFY COLUMN `status` ENUM('Belum', 'Selesai', 'Terlambat') NOT NULL DEFAULT 'Belum';

-- ===========================================
-- 14/04/2026 - Master Tipe Tugas Dinamis (Supporting Task)
-- ===========================================
CREATE TABLE IF NOT EXISTS `rmaster_tipe_tugas` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_tipe` VARCHAR(100) NOT NULL UNIQUE,
    `warna` VARCHAR(10) NOT NULL DEFAULT '#5bc0de'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- data awal 
INSERT IGNORE INTO `rmaster_tipe_tugas` (`nama_tipe`, `warna`) VALUES 
('Administrasi', '#337ab7'), 
('Asistensi', '#5bc0de'), 
('Pelatihan', '#5cb85c'), 
('Pemasaran', '#f0ad4e');

-- Ubah tipe kolom di tsupporting_task agar menerima string dinamis
ALTER TABLE `tsupporting_task` MODIFY COLUMN `tipe_tugas` VARCHAR(100) NOT NULL;


-- Content Plan: Tambah Aktual Publish & Status
ALTER TABLE `tdaftar_content` ADD COLUMN IF NOT EXISTS `aktual_publish` DATE DEFAULT NULL AFTER `tgl_publish`;
ALTER TABLE `tdaftar_content` ADD COLUMN IF NOT EXISTS `status` ENUM('Process', 'Approved', 'Published') NOT NULL DEFAULT 'Process' AFTER `aktual_publish`;

-- Content Plan: Master Media Dinamis
CREATE TABLE IF NOT EXISTS `rmaster_media` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_media` VARCHAR(100) NOT NULL UNIQUE,
    `warna` VARCHAR(10) NOT NULL DEFAULT '#5bc0de'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `rmaster_media` (`nama_media`, `warna`) VALUES 
('IG', '#d9534f'), 
('LinkedIn', '#5bc0de'), 
('Website', '#5cb85c');

-- ===========================================
-- 15/04/2026 - Client File: Tambah Tipe Fasilitas
-- ===========================================
ALTER TABLE `tclients_file` ADD COLUMN IF NOT EXISTS `tipe_fasilitas` VARCHAR(20) DEFAULT '' AFTER `kodcustomer`;

-- ===========================================
-- 17/04/2026 - Invoice: Tambah Tipe Fasilitas untuk PDF filename
-- ===========================================
ALTER TABLE `tinvoice_new` ADD COLUMN IF NOT EXISTS `tipe_fasilitas` VARCHAR(20) DEFAULT '' AFTER `kodcustomer`;

-- Agenda Kalender: Custom Kategori & Warna
ALTER TABLE `tagenda` ADD COLUMN IF NOT EXISTS `kategori` VARCHAR(50) DEFAULT 'Internal' AFTER `id`;

-- Master Kategori Dinamis
CREATE TABLE IF NOT EXISTS `rmaster_kategori_agenda` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `warna` VARCHAR(10) NOT NULL DEFAULT '#7f8c8d',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `rmaster_kategori_agenda` (`nama_kategori`, `warna`) VALUES 
('Internal', '#7f8c8d'),
('Visit / Offline Meeting', '#27ae60'),
('Online Meeting / Zoom', '#2980b9'),
('Training / Asistensi', '#8e44ad'),
('Presentasi / Demo', '#d35400')
ON DUPLICATE KEY UPDATE `warna`=`warna`;
-- ===========================================
-- April 2026 - Pengajuan Magang
-- ===========================================
CREATE TABLE IF NOT EXISTS `tbl_siswa_magang` (
    `id_magang` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `no_ktp` VARCHAR(50) NOT NULL,
    `nama` VARCHAR(255) NOT NULL,
    `ttl` VARCHAR(255),
    `alamat_asal` TEXT,
    `alamat_domisili` TEXT,
    `nama_orang_tua` VARCHAR(255),
    `asal_instansi` VARCHAR(255),
    `fakultas` VARCHAR(150),
    `prodi` VARCHAR(150),
    `pembimbing_instansi` VARCHAR(255),
    `no_hp_pembimbing` VARCHAR(50),
    `nik_magang` VARCHAR(50),
    `file_foto_ktp` VARCHAR(255),
    `file_dok_pengajuan` VARCHAR(255),
    `file_dok_kontrak` VARCHAR(255),
    `file_dok_asset` VARCHAR(255),
    `tgl_mulai` DATE,
    `tgl_selesai` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================================
-- 23/04/2026 - Master Status Prospek Dinamis
-- ===========================================
CREATE TABLE IF NOT EXISTS `rmaster_prospek_status` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_status` VARCHAR(100) NOT NULL UNIQUE,
    `label_display` VARCHAR(100) NOT NULL,
    `warna` VARCHAR(20) NOT NULL DEFAULT '#6c757d',
    `urutan` INT(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `rmaster_prospek_status` (`nama_status`, `label_display`, `warna`, `urutan`) VALUES
('Deal', 'Deal (100%)', '#27ae60', 1),
('High', 'High (80-99%)', '#3498db', 2),
('Medium', 'Medium (50-79%)', '#f39c12', 3),
('Low', 'Low (<50%)', '#e67e22', 4),
('Canceled', 'Canceled', '#7f8c8d', 5);

-- Update warna status prospek (untuk data yang sudah ada)
UPDATE `rmaster_prospek_status` SET `warna` = '#27ae60' WHERE `nama_status` = 'Deal';
UPDATE `rmaster_prospek_status` SET `warna` = '#3498db' WHERE `nama_status` = 'High';
UPDATE `rmaster_prospek_status` SET `warna` = '#f39c12' WHERE `nama_status` = 'Medium';
UPDATE `rmaster_prospek_status` SET `warna` = '#e67e22' WHERE `nama_status` = 'Low';
UPDATE `rmaster_prospek_status` SET `warna` = '#7f8c8d' WHERE `nama_status` = 'Canceled';

-- Update nama_pipeline dan urutan pada tabel rmaster_pipeline
UPDATE `rmaster_pipeline` SET `nama_pipeline` = 'Initial Contact' WHERE `nama_pipeline` = 'Lead';
UPDATE `rmaster_pipeline` SET `nama_pipeline` = 'Sales Qualified Leads (SQL)' WHERE `nama_pipeline` = 'SQL';
UPDATE `rmaster_pipeline` SET `nama_pipeline` = 'System Demo & Needs Analysis' WHERE `nama_pipeline` = 'Konsultasi';
UPDATE `rmaster_pipeline` SET `nama_pipeline` = 'Proposal & Negotiation' WHERE `nama_pipeline` = 'Proposal';
UPDATE `rmaster_pipeline` SET `nama_pipeline` = 'Closing & Contract Finalization' WHERE `nama_pipeline` = 'Negosiasi';

-- Tukar urutan agar SQL jadi urutan 1 dan Initial Contact urutan 2
UPDATE `rmaster_pipeline` SET `urutan` = 1 WHERE `nama_pipeline` = 'Sales Qualified Leads (SQL)';
UPDATE `rmaster_pipeline` SET `urutan` = 2 WHERE `nama_pipeline` = 'Initial Contact';

-- Update pipeline_stage pada tabel tprospek
ALTER TABLE `tprospek` MODIFY COLUMN `pipeline_stage` VARCHAR(100) NOT NULL DEFAULT '';

UPDATE `tprospek` SET `pipeline_stage` = 'Initial Contact' WHERE `pipeline_stage` = 'Lead';
UPDATE `tprospek` SET `pipeline_stage` = 'Sales Qualified Leads (SQL)' WHERE `pipeline_stage` = 'SQL';
UPDATE `tprospek` SET `pipeline_stage` = 'System Demo & Needs Analysis' WHERE `pipeline_stage` = 'Konsultasi';
UPDATE `tprospek` SET `pipeline_stage` = 'Proposal & Negotiation' WHERE `pipeline_stage` = 'Proposal';
UPDATE `tprospek` SET `pipeline_stage` = 'Closing & Contract Finalization' WHERE `pipeline_stage` = 'Negosiasi';

-- Tambah kolom singkatan pada rmaster_pipeline
ALTER TABLE `rmaster_pipeline` ADD COLUMN IF NOT EXISTS `singkatan` VARCHAR(20) DEFAULT '' AFTER `nama_pipeline`;

-- Update singkatan untuk pipeline stage yang sudah ada
UPDATE `rmaster_pipeline` SET `singkatan` = 'SQL' WHERE `nama_pipeline` = 'Sales Qualified Leads (SQL)' AND (`singkatan` IS NULL OR `singkatan` = '');
UPDATE `rmaster_pipeline` SET `singkatan` = 'IC' WHERE `nama_pipeline` = 'Initial Contact' AND (`singkatan` IS NULL OR `singkatan` = '');
UPDATE `rmaster_pipeline` SET `singkatan` = 'SDNA' WHERE `nama_pipeline` = 'System Demo & Needs Analysis' AND (`singkatan` IS NULL OR `singkatan` = '');
UPDATE `rmaster_pipeline` SET `singkatan` = 'PN' WHERE `nama_pipeline` = 'Proposal & Negotiation' AND (`singkatan` IS NULL OR `singkatan` = '');
UPDATE `rmaster_pipeline` SET `singkatan` = 'CCF' WHERE `nama_pipeline` = 'Closing & Contract Finalization' AND (`singkatan` IS NULL OR `singkatan` = '');
-- 24/04/2026 - Template Deskripsi Invoice (Editable)
-- ===========================================
CREATE TABLE IF NOT EXISTS `rmaster_template_invoice` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_template` VARCHAR(100) NOT NULL DEFAULT 'Default',
    `konten_html` TEXT NOT NULL,
    `is_default` TINYINT(1) DEFAULT 0,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Default template 
INSERT IGNORE INTO `rmaster_template_invoice` (`id`, `nama_template`, `konten_html`, `is_default`) VALUES
(1, 'Payment Agreement', '<p><strong>Payment for [SCOPE PEKERJAAN]</strong></p><p>[DETAIL: persentase, nilai, keterangan tambahan]</p><p>for <strong>{{NAMA_KLIEN}}</strong></p><p><br></p><p>As agreement between :</p><p><strong>{{NAMA_KLIEN}}</strong> and</p><p><strong>PT. DUTA SOLUSI INFORMATIKA</strong></p><p>(No. [NOMOR AGREEMENT], [TANGGAL AGREEMENT])</p><p>Valid from [TGL MULAI] to [TGL AKHIR]</p>', 1);

-- 27/04/2026 
-- Tambah kolom Deskripsi Invoice (Internal)
ALTER TABLE tinvoice_new_items ADD COLUMN IF NOT EXISTS internal_description TEXT AFTER item_name;
-- Tambah kolom PPN Ditangguhkan
ALTER TABLE tinvoice_new ADD COLUMN IF NOT EXISTS is_ppn_suspended TINYINT(1) DEFAULT 0 AFTER ppn_amount;

-- tambah tipe fasilitas pada rcustomer
ALTER TABLE rcustomer ADD COLUMN IF NOT EXISTS tipe_fasilitas VARCHAR(50) DEFAULT NULL;

-- ===========================================
-- 29/04/2026 - Implementation Check Completed Status
-- ===========================================
ALTER TABLE `timpl_check` MODIFY COLUMN `status_project` ENUM('Starting','On Track','Almost Done','Completed') DEFAULT 'Starting';

-- ===========================================
-- 28/04/2026 - Supporting Task Status & Access Update
-- ===========================================
-- Tambah kolom created_by jika belum ada
ALTER TABLE `tsupporting_task` ADD COLUMN IF NOT EXISTS `created_by` VARCHAR(50) DEFAULT NULL AFTER `resource`;

-- 2. Update status ENUM
ALTER TABLE `tsupporting_task` MODIFY COLUMN `status` ENUM('Dijadwalkan', 'Dalam Proses', 'Selesai', 'Ditunda', 'Terlambat') NOT NULL DEFAULT 'Dijadwalkan';

-- 3. Tambah kolom nilai_manager di tdaftar_content
ALTER TABLE `tdaftar_content` ADD COLUMN IF NOT EXISTS `nilai_manager` INT(1) DEFAULT NULL;

-- ===========================================
-- 30/04/2026 - Nullable Due Date for Invoice Import
-- ===========================================
ALTER TABLE `tinvoice_new` MODIFY COLUMN `due_date` DATE DEFAULT NULL;

-- 05/05/2026 - Tabel Jadwal Angsuran
-- ===========================================
CREATE TABLE IF NOT EXISTS `tangsuran` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `id_pinjaman` INT(11) UNSIGNED NOT NULL,
  `cicilan_ke` INT(11) NOT NULL,
  `bulan` INT(2) NOT NULL,
  `tahun` INT(4) NOT NULL,
  `nominal` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `status_bayar` ENUM('Belum','Lunas') NOT NULL DEFAULT 'Belum',
  `tgl_bayar` DATETIME NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_pinjaman` (`id_pinjaman`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===========================================
-- 06/05/2026 - Sistem Kantong Kas
-- ===========================================

-- 1. Tabel Master Kantong (dinamis, user bisa tambah/edit sendiri)
CREATE TABLE IF NOT EXISTS `tkantong` (
    `id_kantong`    INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `nama_kantong`  VARCHAR(100) NOT NULL,
    `deskripsi`     VARCHAR(255) DEFAULT NULL,
    `saldo_awal`    DECIMAL(15,2) NOT NULL DEFAULT 0,
    `urutan`        INT(11) NOT NULL DEFAULT 0,
    `is_active`     TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tambah kolom id_kantong pada tabel tkas
--    (NULL = transaksi lama sebelum fitur kantong, tidak dipaksakan)
ALTER TABLE `tkas`
    ADD COLUMN IF NOT EXISTS `id_kantong` INT(11) UNSIGNED DEFAULT NULL AFTER `notransaksi`;

-- 3. Index untuk performa query laporan per kantong
ALTER TABLE `tkas`
    ADD INDEX IF NOT EXISTS `idx_kantong` (`id_kantong`);

-- ===========================================
-- 09/05/2026 - Client Portal Database
-- ===========================================

-- 1. Tabel User Client (Akun Login Khusus Klien)
CREATE TABLE IF NOT EXISTS `r_user_client` (
    `id_user_client` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `iduser` VARCHAR(50) NOT NULL UNIQUE COMMENT 'Username untuk login client',
    `nama` VARCHAR(100) NOT NULL,
    `passwd` VARCHAR(255) NOT NULL,
    `kodcustomer` VARCHAR(50) NOT NULL COMMENT 'FK ke rcustomer, client milik perusahaan mana',
    `stsaktif` TINYINT(1) NOT NULL DEFAULT 1,
    `tgl_dibuat` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_kodcustomer` (`kodcustomer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 2. Tabel Hak Akses Client (Relasi user client dengan data yang boleh dilihat)
CREATE TABLE IF NOT EXISTS `r_akses_client` (
    `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_user_client` INT(11) UNSIGNED NOT NULL,
    `kodcustomer` VARCHAR(50) NOT NULL COMMENT 'Akses ke data perusahaan ini',
    `stsaktif` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_client` (`id_user_client`),
    INDEX `idx_kodcustomer` (`kodcustomer`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Tabel Follow-up / Pesan dari Klien
CREATE TABLE IF NOT EXISTS `t_followup_client` (
    `id_followup` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `id_log` INT(11) NOT NULL COMMENT 'FK ke tlog, log mana yang di-follow up',
    `id_user_client` INT(11) UNSIGNED NOT NULL COMMENT 'Siapa client yang mengirim pesan',
    `pesan` TEXT NOT NULL,
    `tgl_kirim` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `status_baca` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = Belum dibaca, 1 = Sudah dibaca tim internal',
    INDEX `idx_id_log` (`id_log`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 4. Tabel Notifikasi Internal
CREATE TABLE IF NOT EXISTS `t_notif_internal` (
    `id_notif` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `jenis_notif` VARCHAR(50) NOT NULL COMMENT 'Misal: Followup Client',
    `id_referensi` INT(11) UNSIGNED NOT NULL COMMENT 'ID dari tabel referensi (misal id_followup)',
    `pesan_notif` VARCHAR(255) NOT NULL,
    `status_baca` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '0 = Unread, 1 = Read',
    `tgl_notif` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 13/05/2026 - Sprint: Tambah PIC Sprint
-- ===========================================
ALTER TABLE `tsprint` ADD COLUMN IF NOT EXISTS `pic_sprint` VARCHAR(50) DEFAULT NULL AFTER `iduser_create`;

-- ===========================================
-- 14/05/2026 - Sistem Insentif Divisi Produksi
-- ===========================================

-- Tabel Pengaturan Insentif (Dinamis)
CREATE TABLE IF NOT EXISTS `tsetting_poin` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category` ENUM('Tingkat Kesulitan', 'Kecepatan', 'Kemandirian', 'Keterlambatan', 'Kualitas', 'Skema Bonus') NOT NULL,
  `setting_key` VARCHAR(50) NOT NULL,
  `setting_label` VARCHAR(100) NOT NULL,
  `point_value` INT(11) NOT NULL DEFAULT 0,
  `difficulty_ref` ENUM('Mudah', 'Medium', 'Sulit') DEFAULT NULL,
  UNIQUE KEY `unik_setting` (`category`, `setting_key`, `difficulty_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Update ENUM category jika tabel sudah ada sebelumnya
ALTER TABLE `tsetting_poin` MODIFY COLUMN `category` ENUM('Tingkat Kesulitan', 'Kecepatan', 'Kemandirian', 'Keterlambatan', 'Kualitas', 'Skema Bonus') NOT NULL;

-- Isi Default Tingkat Kesulitan (pakai WHERE NOT EXISTS karena difficulty_ref NULL)
INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Tingkat Kesulitan', 'Sulit', 'Sulit', 200 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Tingkat Kesulitan' AND setting_key='Sulit');

INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Tingkat Kesulitan', 'Medium', 'Medium', 70 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Tingkat Kesulitan' AND setting_key='Medium');

INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Tingkat Kesulitan', 'Mudah', 'Mudah', 10 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Tingkat Kesulitan' AND setting_key='Mudah');

-- Isi Default Kecepatan (Ketepatan Waktu) - INSERT IGNORE aman karena difficulty_ref tidak NULL
INSERT IGNORE INTO `tsetting_poin` (category, setting_key, setting_label, point_value, difficulty_ref) VALUES
-- Sulit
('Kecepatan', 'tepat_waktu', 'Tepat Waktu (Deadline)', 10, 'Sulit'),
('Kecepatan', 'h-1', 'Selesai H-1', 20, 'Sulit'),
('Kecepatan', 'h-2', 'Selesai ≥ H-2', 30, 'Sulit'),
-- Medium
('Kecepatan', 'tepat_waktu', 'Tepat Waktu (Deadline)', 5, 'Medium'),
('Kecepatan', 'h-1', 'Selesai H-1', 10, 'Medium'),
('Kecepatan', 'h-2', 'Selesai ≥ H-2', 15, 'Medium'),
-- Mudah
('Kecepatan', 'tepat_waktu', 'Tepat Waktu (Deadline)', 2, 'Mudah'),
('Kecepatan', 'h-1', 'Selesai H-1', 5, 'Mudah'),
('Kecepatan', 'h-2', 'Selesai ≥ H-2', 10, 'Mudah');

-- Isi Default Kemandirian (pakai WHERE NOT EXISTS karena difficulty_ref NULL)
INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Kemandirian', 'Mandiri', 'Analisa Mandiri', 100 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Kemandirian' AND setting_key='Mandiri');

INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Kemandirian', 'Arahan', 'Butuh Arahan / Bertanya', 5 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Kemandirian' AND setting_key='Arahan');

-- Isi Default Keterlambatan (pakai WHERE NOT EXISTS karena difficulty_ref NULL)
INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Keterlambatan', 'hari_sama', 'Terlambat di Hari yang Sama', -10 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Keterlambatan' AND setting_key='hari_sama');

INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Keterlambatan', 'h+1', 'Terlambat H+1 dari Deadline', -100 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Keterlambatan' AND setting_key='h+1');

INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Keterlambatan', 'h+2', 'Terlambat ≥ H+2 dari Deadline', -200 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Keterlambatan' AND setting_key='h+2');

-- Isi Default Kualitas (pakai WHERE NOT EXISTS karena difficulty_ref NULL)
INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Kualitas', 'tidak_sesuai', 'Tidak Sesuai Requirement', -50 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Kualitas' AND setting_key='tidak_sesuai');

INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Kualitas', 'bug_terulang', 'Bug yang Sama Terulang', -100 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Kualitas' AND setting_key='bug_terulang');

INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Kualitas', 'sesuai_requirement', 'Sesuai Requirement', 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Kualitas' AND setting_key='sesuai_requirement');

INSERT INTO `tsetting_poin` (category, setting_key, setting_label, point_value)
SELECT 'Kualitas', 'bug_hangus', 'Bug Terulang > 2x (Poin Hangus)', 0 FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `tsetting_poin` WHERE category='Kualitas' AND setting_key='bug_hangus');

-- Tambah Kolom Kesulitan ke tlog
ALTER TABLE `tlog` ADD COLUMN IF NOT EXISTS `difficulty` ENUM('Mudah', 'Medium', 'Sulit') DEFAULT NULL AFTER `idsprint`;

-- 16/05/2026 - Penyesuaian Penamaan Kolom 'nilai' 
ALTER TABLE `tlog` DROP COLUMN IF EXISTS `poin_total`; -- Hapus kolom lama yang sempat dibuat
ALTER TABLE `tlog` ADD COLUMN IF NOT EXISTS `independence` VARCHAR(50) DEFAULT NULL AFTER `difficulty`;
ALTER TABLE `tlog` ADD COLUMN IF NOT EXISTS `quality` VARCHAR(50) DEFAULT NULL AFTER `independence`;
ALTER TABLE `tlog` MODIFY COLUMN `nilai` INT(11) DEFAULT NULL;
ALTER TABLE `tlog` ADD COLUMN IF NOT EXISTS `poin_dasar` INT(11) DEFAULT NULL AFTER `nilai`;

-- Isi poin_dasar dari nilai yang sudah ada agar tidak terhitung ulang menggunakan setting baru
UPDATE `tlog` SET `poin_dasar` = `nilai` WHERE `isselesai` = 1 AND `nilai` IS NOT NULL AND `poin_dasar` IS NULL;

-- Penyesuaian Jabatan: Tambah SPV Produksi (5) dan Geser Staff (6)
INSERT INTO `tbl_jabatan` (kodjab, nama_jabatan) VALUES (6, 'Staff') ON DUPLICATE KEY UPDATE nama_jabatan = 'Staff';
-- Update user yang tadinya Staff (5) menjadi 6
UPDATE `ruser` SET `kodjab` = 6 WHERE `kodjab` = 5;
-- Set kodjab 5 menjadi SPV Produksi
UPDATE `tbl_jabatan` SET `nama_jabatan` = 'SPV Produksi' WHERE `kodjab` = 5;
INSERT INTO `tbl_jabatan` (kodjab, nama_jabatan) SELECT 5, 'SPV Produksi' FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `tbl_jabatan` WHERE kodjab = 5);
