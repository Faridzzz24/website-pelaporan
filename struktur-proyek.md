# Dokumentasi Struktur File Sistem Pelaporan K3

Dokumen ini menjelaskan secara singkat fungsi dari file-file utama yang telah kita buat dari awal pengembangan website Pelaporan Insiden K3 PT Cabot.

---

## 1. Controllers (Pengendali Logika)
File-file ini berada di dalam folder `app/Http/Controllers/`. Controller bertugas menerima *request* dari pengguna dan menentukan apa yang harus dilakukan oleh sistem.

* **`ReportController.php`**
  Mengendalikan sisi publik website. Fungsinya meliputi menampilkan halaman form pengisian laporan awal, memvalidasi inputan (foto, deskripsi, dll), men-*generate* Tracking Code otomatis, dan menyimpannya ke database.
* **`DashboardController.php`**
  Inti dari sistem operasional internal. Berfungsi menampilkan halaman Dashboard HSE, mengambil data laporan untuk ditampilkan di tabel, memperbarui status/urgensi/petugas pada suatu laporan, mencatat *audit trail*, serta menangani fitur *Export* ke CSV/PDF/Word.
* **`UserController.php`**
  Mengurus manajemen akses. Digunakan khusus oleh peran Administrator untuk menambah, mengubah *password*, atau menghapus akun HSE Officer / Supervisor.
* **`Auth/LoginController.php`**
  Menangani alur masuk (Login) dan keluar (Logout) ke dalam sistem.

## 2. Models (Penghubung Database)
File-file ini berada di dalam folder `app/Models/`. Model bertugas berkomunikasi langsung dengan tabel-tabel di database.

* **`IncidentReport.php`**
  Mewakili tabel `incident_reports`. Menampung semua definisi data insiden, relasi pelapor, serta warna/label otomatis (misalnya: merah untuk "Kritis", hijau untuk "Selesai").
* **`User.php`**
  Mewakili tabel `users`. Menyimpan sistem keamanan peran (*role*), mengecek apakah user adalah seorang Admin, HSE, atau Supervisor.
* **`AuditLog.php`**
  Mewakili tabel `audit_logs`. Menyimpan riwayat perubahan/aktivitas untuk keamanan (jejak digital siapa melakukan apa pada jam berapa).

## 3. Views (Tampilan / Antarmuka Pengguna)
File-file ini berada di folder `resources/views/`. Bertugas sebagai wajah (*User Interface*) yang dilihat langsung oleh pelapor dan petugas.

**a. Layout Utama**
* **`layouts/public.blade.php`** : Kerangka halaman publik (header, footer merah khas Cabot).
* **`layouts/dashboard.blade.php`** : Kerangka halaman internal (sidebar, profil login, menu ekspor).

**b. Halaman Publik (Tanpa Login)**
* **`reports/create.blade.php`** : Halaman utama tempat karyawan/kontraktor mengisi form insiden K3 secara langsung.
* **`reports/track.blade.php` & `track-result.blade.php`** : Halaman tempat pelapor melacak sejauh mana laporannya telah diproses hanya bermodalkan *Tracking Code*.

**c. Halaman Internal (Harus Login)**
* **`dashboard/index.blade.php`** : Halaman tabel daftar laporan masuk, lengkap dengan filter, pencarian, dan rangkuman metrik data.
* **`dashboard/show.blade.php`** : Halaman sangat penting; menampilkan detail dari 1 laporan spesifik. Di sinilah HSE bisa melihat foto bukti, mengganti status penanganan, menugaskan orang lain, dan membaca riwayat audit laporan tersebut.
* **`exports/reports.blade.php`** : *Template* khusus HTML polos yang digunakan mesin di balik layar untuk merancang tabel sebelum dikonversi *(export)* menjadi file PDF dan Word.

## 4. Konfigurasi Pendukung
* **`routes/web.php`**
  Buku peta sistem. Di sinilah semua URL didaftarkan. Memisahkan rute mana saja yang bebas diakses publik (seperti halaman utama) dan rute mana yang dilindungi gembok *login* (harus login).
* **`app/Http/Middleware/CheckRole.php`**
  Penjaga gerbang keamanan. File ini menendang keluar *user* biasa jika mereka mencoba mengakses URL rahasia (misal: HSE biasa tidak akan bisa membuka URL pengaturan Admin).
* **`database/seeders/UserSeeder.php`**
  Skrip bawaan yang kita gunakan di awal untuk menyuntikkan *username* dan *password* default (admin/hse/supervisor) ke dalam database secara otomatis.
