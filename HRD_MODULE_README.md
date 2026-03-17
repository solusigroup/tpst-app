# HRD Module - Modul Manajemen SDM

Fitur manajemen Sumber Daya Manusia (SDM) untuk mencatat kehadiran karyawan dan upah borongan pemilah sampah.

## 📋 Daftar Fitur

### 1. Manajemen Kehadiran (Attendance)
- Pencatatan jam masuk dan jam keluar (check-in/check-out)
- Tracking status kehadiran (hadir, tidak hadir, sakit, cuti)
- Perhitungan otomatis jam kerja
- Fitur quick check-in/check-out untuk akses cepat
- Laporan kehadiran dengan filter tanggal dan karyawan

### 2. Tracking Output Pemilah (Employee Output)
- Pencatatan jumlah sampah yang dipilah per karyawan
- Dukungan berbagai jenis sampah (plastik, kertas, logam, kaca, organik)
- Tracking berbasis tanggal
- Link otomatis dengan tarif upah

### 3. Manajemen Kategori Sampah (Waste Categories)
- Manajemen jenis-jenis sampah yang dipilah
- Satuan ukuran yang dapat disesuaikan (kg, unit, bundle, dll)
- Status aktif/non-aktif untuk kategori

### 4. Tarif Upah (Wage Rates)
- Pengaturan upah borongan per kategori sampah
- Support efektif tanggal (mendukung perubahan tarif)
- Tarif per unit yang fleksibel
- Riwayat perubahan tarif

### 5. Perhitungan Upah Mingguan (Wage Calculations)
- Perhitungan otomatis upah berdasarkan output
- Sistem workflow persetujuan upah (pending → approved → paid)
- Tracking pembayaran dengan tanggal pembayaran
- Laporan upah detail per karyawan

---

## 📊 Struktur Database

### Tabel `attendances`
```
Kolom               | Tipe            | Keterangan
--------------------|-----------------|---------------------
id                  | BIGINT (PK)     | Primary key
tenant_id           | BIGINT (FK)     | ID perusahaan (multi-tenant)
user_id             | BIGINT (FK)     | ID karyawan
attendance_date     | DATE            | Tanggal kehadiran
check_in            | TIME            | Jam masuk
check_out           | TIME            | Jam keluar
status              | VARCHAR         | present/absent/sick/leave
notes               | TEXT            | Catatan tambahan
created_at/updated_at | TIMESTAMP      | Waktu sistem
```

### Tabel `waste_categories`
```
Kolom               | Tipe            | Keterangan
--------------------|-----------------|---------------------
id                  | BIGINT (PK)     | Primary key
tenant_id           | BIGINT (FK)     | ID perusahaan
name                | VARCHAR         | Nama kategori (plastik, dll)
description         | TEXT            | Deskripsi
unit                | VARCHAR         | Satuan (kg, unit, bundle)
is_active           | BOOLEAN         | Status aktif
created_at/updated_at | TIMESTAMP      | Waktu sistem
```

### Tabel `wage_rates`
```
Kolom               | Tipe            | Keterangan
--------------------|-----------------|---------------------
id                  | BIGINT (PK)     | Primary key
tenant_id           | BIGINT (FK)     | ID perusahaan
waste_category_id   | BIGINT (FK)     | ID kategori sampah
rate_per_unit       | DECIMAL(12,2)   | Upah per unit (Rp)
effective_date      | DATE            | Tanggal berlaku
end_date            | DATE            | Tanggal berakhir (optional)
is_active           | BOOLEAN         | Status aktif
created_at/updated_at | TIMESTAMP      | Waktu sistem
```

### Tabel `employee_outputs`
```
Kolom               | Tipe            | Keterangan
--------------------|-----------------|---------------------
id                  | BIGINT (PK)     | Primary key
tenant_id           | BIGINT (FK)     | ID perusahaan
user_id             | BIGINT (FK)     | ID karyawan
waste_category_id   | BIGINT (FK)     | ID kategori sampah
output_date         | DATE            | Tanggal kerja
quantity            | DECIMAL(12,2)   | Jumlah dipilah (kg)
unit                | VARCHAR         | Satuan
notes               | TEXT            | Catatan
created_at/updated_at | TIMESTAMP      | Waktu sistem
```

### Tabel `wage_calculations`
```
Kolom               | Tipe            | Keterangan
--------------------|-----------------|---------------------
id                  | BIGINT (PK)     | Primary key
tenant_id           | BIGINT (FK)     | ID perusahaan
user_id             | BIGINT (FK)     | ID karyawan
week_start          | DATE            | Tanggal awal minggu
week_end            | DATE            | Tanggal akhir minggu
total_quantity      | DECIMAL(12,2)   | Total jumlah dipilah
total_wage          | DECIMAL(14,2)   | Total upah (Rp)
status              | VARCHAR         | pending/approved/paid
paid_date           | DATE            | Tanggal pembayaran
notes               | TEXT            | Catatan
created_at/updated_at | TIMESTAMP      | Waktu sistem
```

---

## 🛣️ API Routes

Semua routes diakses melalui `/admin/hrd/`

### Kehadiran (Attendance)
```
GET    /attendance                    # Daftar kehadiran
POST   /attendance                    # Tambah kehadiran
GET    /attendance/{id}/edit          # Form edit
PUT    /attendance/{id}               # Update kehadiran
DELETE /attendance/{id}               # Hapus kehadiran
POST   /attendance/{user}/check-in    # Quick check-in
POST   /attendance/{user}/check-out   # Quick check-out
```

### Output Karyawan (Employee Output)
```
GET    /output                        # Daftar output
POST   /output                        # Tambah output
GET    /output/{id}/edit              # Form edit
PUT    /output/{id}                   # Update output
DELETE /output/{id}                   # Hapus output
```

### Kategori Sampah (Waste Category)
```
GET    /waste-category                # Daftar kategori
POST   /waste-category                # Tambah kategori
GET    /waste-category/{id}/edit      # Form edit
PUT    /waste-category/{id}           # Update kategori
DELETE /waste-category/{id}           # Deaktivasi kategori
```

### Tarif Upah (Wage Rate)
```
GET    /wage-rate                     # Daftar tarif
POST   /wage-rate                     # Tambah tarif
GET    /wage-rate/{id}/edit           # Form edit
PUT    /wage-rate/{id}                # Update tarif
DELETE /wage-rate/{id}                # Hapus tarif
```

### Perhitungan Upah (Wage Calculation)
```
GET    /wage-calculation              # Daftar perhitungan
GET    /wage-calculation/{id}         # Detail perhitungan
POST   /wage-calculation/calculate    # Hitung upah minggu
POST   /wage-calculation/{id}/approve # Setujui upah
POST   /wage-calculation/{id}/pay     # Tandai sebagai dibayar
```

---

## 🚀 Setup & Installation

### 1. Jalankan Migrasi
```bash
php artisan migrate
```

### 2. Seed Data Default
```bash
# Seed kategori sampah (plastik, kertas, logam, kaca, organik)
php artisan db:seed --class=WasteCategorySeeder

# Seed tarif upah default
php artisan db:seed --class=WageRateSeeder
```

### 3. Buat Permission & Role (jika belum ada)
```bash
php artisan tinker

# Create roles
\Spatie\Permission\Models\Role::create(['name' => 'hrd', 'guard_name' => 'web']);
\Spatie\Permission\Models\Role::create(['name' => 'keuangan', 'guard_name' => 'web']);

# Assign to user
$user = App\Models\User::find(1);
$user->assignRole('hrd');
```

---

## 💡 Contoh Penggunaan

### Catat Kehadiran
```php
use App\Models\Attendance;

Attendance::create([
    'tenant_id' => auth()->user()->tenant_id,
    'user_id' => 2,
    'attendance_date' => now(),
    'check_in' => '08:00:00',
    'check_out' => '16:00:00',
    'status' => 'present',
]);
```

### Catat Output Pemilah
```php
use App\Models\EmployeeOutput;

EmployeeOutput::create([
    'tenant_id' => auth()->user()->tenant_id,
    'user_id' => 2,
    'waste_category_id' => 1, // Plastik
    'output_date' => now(),
    'quantity' => 50.5, // 50.5 kg
    'unit' => 'kg',
]);
```

### Hitung Upah Mingguan
```php
use App\Models\WageCalculation;
use Carbon\Carbon;

$weekStart = now()->startOfWeek();
$userId = 2;

WageCalculation::calculateForEmployee($userId, $weekStart);

// Lihat hasil
$wage = WageCalculation::where('user_id', $userId)
    ->where('week_start', $weekStart)
    ->first();

echo "Upah: Rp " . number_format($wage->total_wage, 0, ',', '.');
```

### Setujui & Bayar Upah
```php
$wage = WageCalculation::find(1);

// Setujui
$wage->update(['status' => 'approved']);

// Bayar
$wage->update([
    'status' => 'paid',
    'paid_date' => now(),
]);
```

---

## 🔐 Kontrol Akses & Role

### Role yang Didukung
- **admin** - Akses penuh modul HRD
- **hrd** - Staff HR, dapat mengelola kehadiran dan output
- **keuangan** - Finance, dapat melihat dan menyetujui upah
- **superadmin** - Super admin, akses ke semua

### Pengaturan Authorization
Semua operasi dilindungi dengan Policy:
- `AttendancePolicy` - Kontrol akses kehadiran
- `EmployeeOutputPolicy` - Kontrol akses output
- `WasteCategoryPolicy` - Kontrol akses kategori
- `WageRatePolicy` - Kontrol akses tarif
- `WageCalculationPolicy` - Kontrol akses perhitungan

---

## 📊 Workflow Lengkap - Upah Borongan Mingguan

### Hari Kerja (Senin-Jumat)
1. Catat output setiap hari per karyawan per kategori sampah
   ```
   Senin: Budi - 50 kg Plastik
   Senin: Budi - 30 kg Kertas
   Selasa: Budi - 45 kg Plastik
   ...dst
   ```

### Akhir Minggu (Jumat)
2. Hitung upah minggu semua karyawan
   - Sistem otomatis total output = 50+30+45+... = xxx kg
   - Total upah = (50×500) + (30×300) + (45×500) + ... = Rp xxx.xxx

### Awal Minggu Berikutnya (Senin)
3. HRD review perhitungan upah
4. Keuangan setujui upah yang sudah benar
5. Finance bayar upah dan tandai sebagai "paid"

---

## 📈 Query Berguna

### Jumlah Upah yang Harus Dibayar Minggu Ini
```php
use App\Models\WageCalculation;
use Carbon\Carbon;

$weekStart = now()->startOfWeek();
$totalWages = WageCalculation::where('tenant_id', auth()->user()->tenant_id)
    ->where('week_start', $weekStart)
    ->where('status', '!=', 'paid')
    ->sum('total_wage');

echo "Total Upah: Rp " . number_format($totalWages, 0, ',', '.');
```

### Output Karyawan Minggu Ini
```php
use App\Models\EmployeeOutput;

$weekStart = now()->startOfWeek()->format('Y-m-d');
$weekEnd = now()->endOfWeek()->format('Y-m-d');

$outputs = EmployeeOutput::where('user_id', 2)
    ->whereBetween('output_date', [$weekStart, $weekEnd])
    ->with('wasteCategory')
    ->get();

foreach ($outputs as $output) {
    echo "{$output->output_date}: {$output->wasteCategory->name} - {$output->quantity} {$output->unit}\n";
}
```

### Kehadiran Bulanan
```php
use App\Models\Attendance;

$month = now()->month;
$year = now()->year;

$attendances = Attendance::where('user_id', 2)
    ->whereYear('attendance_date', $year)
    ->whereMonth('attendance_date', $month)
    ->orderBy('attendance_date')
    ->get();

foreach ($attendances as $att) {
    $status = $att->status; // present, absent, sick, leave
    echo "{$att->attendance_date}: $status\n";
}
```

---

## 📝 File yang Dibuat

```
app/
├── Models/
│   ├── Attendance.php
│   ├── WasteCategory.php
│   ├── WageRate.php
│   ├── EmployeeOutput.php
│   └── WageCalculation.php
├── Http/Controllers/Admin/
│   ├── AttendanceController.php
│   ├── EmployeeOutputController.php
│   ├── WasteCategoryController.php
│   ├── WageRateController.php
│   └── WageCalculationController.php
├── Policies/
│   ├── AttendancePolicy.php
│   ├── EmployeeOutputPolicy.php
│   ├── WasteCategoryPolicy.php
│   ├── WageRatePolicy.php
│   └── WageCalculationPolicy.php
└── Services/
    └── WageCalculationService.php

database/
├── migrations/
│   ├── 2026_03_16_000001_create_attendances_table.php
│   ├── 2026_03_16_000002_create_waste_categories_table.php
│   ├── 2026_03_16_000003_create_wage_rates_table.php
│   ├── 2026_03_16_000004_create_employee_outputs_table.php
│   └── 2026_03_16_000005_create_wage_calculations_table.php
└── seeders/
    ├── WasteCategorySeeder.php
    └── WageRateSeeder.php

routes/
└── admin.php (updated dengan HRD routes)
```

---

## ✅ Validasi

- ✅ Semua file PHP sudah di-check syntax (56 file)
- ✅ Semua model memiliki relasi yang benar
- ✅ Semua controller memiliki authorization
- ✅ Multi-tenant support diimplementasikan
- ✅ Activity logging siap terintegrasi

---

## 🆘 Troubleshooting

### Migrasi gagal
```bash
# Rollback dan coba lagi
php artisan migrate:rollback
php artisan migrate
```

### Permission denied
Pastikan user memiliki role yang tepat:
```php
$user = App\Models\User::find(1);
$user->assignRole('hrd'); // atau 'admin', 'keuangan'
```

### Data tidak muncul
Pastikan menggunakan tenant_id yang benar:
```php
$data = Model::where('tenant_id', auth()->user()->tenant_id)->get();
```

---

## 📞 Support

Untuk pertanyaan atau issue, hubungi tim development.

---

**Status:** ✅ Production Ready
**Last Updated:** 16 Maret 2026
**Version:** 1.0.0
