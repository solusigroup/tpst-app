-- Insert test data

-- Create test tenant
INSERT INTO `tenants` (`id`, `name`, `domain`) VALUES 
(1, 'PT Sampah Jaya', 'sampahj aya.test');

-- Create test user (password: password)
INSERT INTO `users` (`id`, `tenant_id`, `name`, `email`, `password`, `role`, `email_verified_at`) VALUES 
(1, 1, 'Admin User', 'admin@tpst.test', '$2y$12$9TXg/BnG1CtVvqyqrYXDCu8/D1xQSMpnA7Q3K7l2X5Y1z0W3e4/8m', 'admin', NOW());

-- Create test klien
INSERT INTO `klien` (`id`, `tenant_id`, `nama_klien`, `jenis`, `kontak`, `alamat`) VALUES 
(1, 1, 'Dinas Lingkungan Hidup Jakarta', 'DLH', '021-123-4567', 'Jl. Gatot Subroto No. 1'),
(2, 1, 'PT Tri Bulan Sakti', 'Swasta', '021-789-0123', 'Jl. Sudirman No. 50'),
(3, 1, 'PT Recycling Indonesia', 'Offtaker', '021-456-7890', 'Jl. Jend. Sudirman');

-- Create test armada
INSERT INTO `armada` (`id`, `tenant_id`, `klien_id`, `plat_nomor`, `kapasitas_maksimal`) VALUES 
(1, 1, 1, 'B-1001-DLH', 10000.00),
(2, 1, 2, 'B-2002-TBS', 8000.00),
(3, 1, 3, 'B-3003-REC', 12000.00);

-- Create Chart of Accounts
INSERT INTO `coa` (`id`, `tenant_id`, `kode_akun`, `nama_akun`, `tipe`) VALUES 
(1, 1, '1100', 'Kas', 'Asset'),
(2, 1, '1200', 'Piutang Usaha', 'Asset'),
(3, 1, '1300', 'Peralatan', 'Asset'),
(4, 1, '2100', 'Utang Usaha', 'Liability'),
(5, 1, '3100', 'Modal', 'Equity'),
(6, 1, '4100', 'Pendapatan Tipping', 'Revenue'),
(7, 1, '4200', 'Pendapatan Penjualan', 'Revenue'),
(8, 1, '5100', 'Biaya Operasional', 'Expense');

-- Create sample ritase
INSERT INTO `ritase` (`id`, `tenant_id`, `armada_id`, `klien_id`, `nomor_tiket`, `waktu_masuk`, `waktu_keluar`, `berat_bruto`, `berat_tarra`, `berat_netto`, `jenis_sampah`, `biaya_tipping`, `status`) VALUES 
(1, 1, 1, 1, 'TIK-20260224-001', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY) + INTERVAL 30 MINUTE, 9500, 2500, 7000, 'Mixed Waste', 500000, 'selesai'),
(2, 1, 2, 2, 'TIK-20260224-002', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY) + INTERVAL 25 MINUTE, 7800, 2000, 5800, 'Organic', 450000, 'selesai'),
(3, 1, 3, 3, 'TIK-20260224-003', NOW(), NULL, 11000, 1000, NULL, 'Recyclables', 0, 'masuk');

-- Create sample penjualan
INSERT INTO `penjualan` (`id`, `tenant_id`, `klien_id`, `tanggal`, `jenis_produk`, `berat_kg`, `harga_satuan`, `total_harga`) VALUES 
(1, 1, 3, DATE_SUB(CURDATE(), INTERVAL 5 DAY), 'RDF', 2000, 2500, 5000000),
(2, 1, 3, DATE_SUB(CURDATE(), INTERVAL 3 DAY), 'Plastik', 500, 5000, 2500000),
(3, 1, 3, CURDATE(), 'Kompos', 1500, 1500, 2250000);

-- Create daily production
INSERT INTO `produksi_harian` (`id`, `tenant_id`, `tanggal`, `total_input_sampah`, `hasil_rdf`, `hasil_plastik`, `hasil_kompos`, `residu_tpa`) VALUES 
(1, 1, DATE_SUB(CURDATE(), INTERVAL 2 DAY), 12800, 5000, 2000, 3000, 2800),
(2, 1, DATE_SUB(CURDATE(), INTERVAL 1 DAY), 5800, 2500, 1000, 1500, 800),
(3, 1, CURDATE(), 0, 0, 0, 0, 0);
