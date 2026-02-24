-- Create tenants table
CREATE TABLE IF NOT EXISTS `tenants` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `domain` VARCHAR(255) NOT NULL UNIQUE,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `domain_idx` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create migrations table
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create users table (modified for multi-tenancy)
CREATE TABLE IF NOT EXISTS `users` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `email_verified_at` TIMESTAMP NULL,
  `role` ENUM('admin', 'timbangan', 'keuangan') NOT NULL DEFAULT 'admin',
  `password` VARCHAR(255) NOT NULL,
  `remember_token` VARCHAR(100),
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  INDEX `tenant_id_idx` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create password_reset_tokens table
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create sessions table
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` BIGINT UNSIGNED,
  `ip_address` VARCHAR(45),
  `user_agent` LONGTEXT,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `sessions_user_id_index` (`user_id`),
  INDEX `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create cache table
CREATE TABLE IF NOT EXISTS `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` LONGTEXT NOT NULL,
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create cache_locks table
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255),
  `expiration` INT NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create jobs table
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL DEFAULT 0,
  `reserved_at` INT UNSIGNED,
  `available_at` INT UNSIGNED NOT NULL,
  `created_at` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create job_batches table
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` VARCHAR(255) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `failed_job_ids` LONGTEXT NOT NULL,
  `options` MEDIUMTEXT,
  `cancelled_at` INT,
  `created_at` INT NOT NULL,
  `finished_at` INT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create failed_jobs table
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL UNIQUE,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create klien table
CREATE TABLE IF NOT EXISTS `klien` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `nama_klien` VARCHAR(255) NOT NULL,
  `jenis` ENUM('DLH', 'Swasta', 'Offtaker') NOT NULL DEFAULT 'Swasta',
  `kontak` VARCHAR(255),
  `alamat` VARCHAR(255),
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  INDEX `tenant_id_idx` (`tenant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create armada table
CREATE TABLE IF NOT EXISTS `armada` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `klien_id` BIGINT UNSIGNED NOT NULL,
  `plat_nomor` VARCHAR(255) NOT NULL UNIQUE,
  `kapasitas_maksimal` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`klien_id`) REFERENCES `klien` (`id`) ON DELETE CASCADE,
  INDEX `tenant_id_idx` (`tenant_id`),
  INDEX `klien_id_idx` (`klien_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create ritase table
CREATE TABLE IF NOT EXISTS `ritase` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `armada_id` BIGINT UNSIGNED NOT NULL,
  `klien_id` BIGINT UNSIGNED NOT NULL,
  `nomor_tiket` VARCHAR(255) NOT NULL UNIQUE,
  `waktu_masuk` DATETIME NOT NULL,
  `waktu_keluar` DATETIME,
  `berat_bruto` DECIMAL(12, 2),
  `berat_tarra` DECIMAL(12, 2),
  `berat_netto` DECIMAL(12, 2),
  `jenis_sampah` VARCHAR(255),
  `biaya_tipping` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `status` ENUM('masuk', 'timbang', 'keluar', 'selesai') NOT NULL DEFAULT 'masuk',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`armada_id`) REFERENCES `armada` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`klien_id`) REFERENCES `klien` (`id`) ON DELETE CASCADE,
  INDEX `tenant_id_idx` (`tenant_id`),
  INDEX `armada_id_idx` (`armada_id`),
  INDEX `klien_id_idx` (`klien_id`),
  INDEX `waktu_masuk_idx` (`waktu_masuk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create produksi_harian table
CREATE TABLE IF NOT EXISTS `produksi_harian` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `tanggal` DATE NOT NULL,
  `total_input_sampah` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `hasil_rdf` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `hasil_plastik` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `hasil_kompos` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `residu_tpa` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `tenant_tanggal_unique` (`tenant_id`, `tanggal`),
  INDEX `tenant_id_idx` (`tenant_id`),
  INDEX `tanggal_idx` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create penjualan table
CREATE TABLE IF NOT EXISTS `penjualan` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `klien_id` BIGINT UNSIGNED NOT NULL,
  `tanggal` DATE NOT NULL,
  `jenis_produk` VARCHAR(255) NOT NULL,
  `berat_kg` DECIMAL(12, 2) NOT NULL,
  `harga_satuan` DECIMAL(12, 2) NOT NULL,
  `total_harga` DECIMAL(12, 2) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`klien_id`) REFERENCES `klien` (`id`) ON DELETE CASCADE,
  INDEX `tenant_id_idx` (`tenant_id`),
  INDEX `klien_id_idx` (`klien_id`),
  INDEX `tanggal_idx` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create coa (Chart of Accounts) table
CREATE TABLE IF NOT EXISTS `coa` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `kode_akun` VARCHAR(255) NOT NULL,
  `nama_akun` VARCHAR(255) NOT NULL,
  `tipe` ENUM('Asset', 'Liability', 'Equity', 'Revenue', 'Expense') NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_akun_unique` (`kode_akun`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  INDEX `tenant_id_idx` (`tenant_id`),
  INDEX `kode_akun_idx` (`kode_akun`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create jurnal_header table
CREATE TABLE IF NOT EXISTS `jurnal_header` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `tenant_id` BIGINT UNSIGNED NOT NULL,
  `tanggal` DATE NOT NULL,
  `nomor_referensi` VARCHAR(255),
  `deskripsi` VARCHAR(255),
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  INDEX `tenant_id_idx` (`tenant_id`),
  INDEX `tanggal_idx` (`tanggal`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create jurnal_detail table
CREATE TABLE IF NOT EXISTS `jurnal_detail` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `jurnal_header_id` BIGINT UNSIGNED NOT NULL,
  `coa_id` BIGINT UNSIGNED NOT NULL,
  `debit` DECIMAL(14, 2) NOT NULL DEFAULT 0,
  `kredit` DECIMAL(14, 2) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`jurnal_header_id`) REFERENCES `jurnal_header` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`coa_id`) REFERENCES `coa` (`id`) ON DELETE CASCADE,
  INDEX `jurnal_header_id_idx` (`jurnal_header_id`),
  INDEX `coa_id_idx` (`coa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert migrations record
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2026_02_24_000001_create_tenants_table', 1),
('2026_02_24_000002_modify_users_table_for_multi_tenancy', 1),
('2026_02_24_000003_create_klien_table', 1),
('2026_02_24_000004_create_armada_table', 1),
('2026_02_24_000005_create_ritase_table', 1),
('2026_02_24_000006_create_produksi_harian_table', 1),
('2026_02_24_000007_create_penjualan_table', 1),
('2026_02_24_000008_create_coa_table', 1),
('2026_02_24_000009_create_jurnal_header_table', 1),
('2026_02_24_000010_create_jurnal_detail_table', 1);
