# SKTT PPPK - Generator Kartu Ujian & Absensi Barcode (CI4)

## Fitur
- Verifikasi peserta tanpa login (Nomor Peserta + Jabatan + Tanggal Lahir)
- Generate Kartu Ujian PDF A4 (DomPDF)
- Barcode `Code128` dengan payload terenkripsi (`v1:<ciphertext>`)
- Admin login
- Import data peserta dari file `.xlsx`
- Scan kehadiran peserta
  - Scan pertama valid, simpan waktu registrasi
  - Scan kedua dan seterusnya notifikasi duplikat, waktu registrasi pertama tidak berubah
- Laporan hadir/tidak hadir + export PDF/CSV

## Dependency
Sudah dipasang di `composer.json`:
- `dompdf/dompdf`
- `picqer/php-barcode-generator`
- `phpoffice/phpspreadsheet`

## Setup
1. Konfigurasi database di file `.env`:
- `database.default.hostname`
- `database.default.database`
- `database.default.username`
- `database.default.password`
- `database.default.DBDriver = MySQLi`

2. Ganti secret enkripsi barcode di `.env`:
- `barcode.secret = ganti-dengan-secret-panjang-acak`

3. Buat tabel database (pilih salah satu):
- Jalankan migration CI4 jika lingkungan Anda menyediakan `spark`, atau
- Import SQL manual: `database/sktt_schema.sql`

4. Akses aplikasi:
- Portal peserta: `/`
- Admin login: `/admin/login`

## Akun Admin Default
- Username: `admin`
- Password: `Admin123!`
- Role: `super_admin`

Segera ganti password admin di tabel `admins` setelah login pertama.

## Role Admin
- `super_admin`: dapat melihat, scan barcode, import data, dan generate laporan seluruh peserta.
- `admin_unit`: dapat melihat, scan barcode, dan generate laporan hanya untuk peserta pada `work_unit` yang sama dengan akun admin.

## Log Aktivitas Admin Unit Kerja
- Login `admin_unit` dicatat ke tabel `admin_login_logs`.
- Aktivitas scan barcode `admin_unit` dicatat ke tabel `attendance_scan_events`.
- Halaman log hanya dapat diakses oleh `super_admin`:
  - `/admin/logs/login`
  - `/admin/logs/scan`
