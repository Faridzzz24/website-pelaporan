# Panduan Deploy Web Laravel Gratis, 24/7, dan Mudah

Untuk mendeploy website pelaporan (Laravel) ini agar bisa diakses secara online, gratis selamanya (24/7 tanpa *sleep*), dan tidak ribet, ada beberapa opsi yang bisa digunakan. 

Berikut adalah **2 opsi terbaik** yang disesuaikan dengan tingkat kemudahan yang Anda inginkan.

---

## Opsi 1: Paling Mudah (Shared Hosting Tradisional)
Opsi ini sangat cocok jika Anda tidak ingin repot berurusan dengan *command line* atau terminal saat proses deployment. Kita akan menggunakan **InfinityFree** yang menyediakan Hosting + Database MySQL gratis yang aktif 24/7.

**Kelebihan:** Sangat mudah, sudah termasuk database MySQL, gratis selamanya.
**Kekurangan:** Upload file manual (harus di-zip dulu).

### Langkah-langkah:
1. **Siapkan File Proyek Anda**
   - Pastikan Anda sudah menjalankan perintah `npm run build` di terminal lokal Anda agar tampilan (CSS/JS) sudah ter-compile.
   - Hapus folder `vendor` dan `node_modules`.
   - Jadikan seluruh folder proyek Anda menjadi file `.zip` (misal: `website-pelaporan.zip`).

2. **Daftar Akun Hosting**
   - Kunjungi [InfinityFree](https://www.infinityfree.com/) dan buat akun gratis.
   - Buat *Hosting Account* baru, lalu pilih subdomain gratis yang disediakan (misal: `pelaporan-k3.epizy.com`).

3. **Upload File ke Server**
   - Masuk ke menu **Control Panel** (cPanel) di InfinityFree, lalu buka **Online File Manager** (htdocs).
   - Upload file `website-pelaporan.zip` ke dalam folder `htdocs` dan *Extract* (ekstrak) file tersebut.
   - **PENTING UNTUK LARAVEL:** Pindahkan semua isi dari dalam folder `public` milik Laravel langsung ke root folder `htdocs`. 
   - Edit file `index.php` yang baru dipindah ke htdocs:
     - Ubah `require __DIR__.'/../vendor/autoload.php';` menjadi `require __DIR__.'/vendor/autoload.php';`
     - Ubah `require_once __DIR__.'/../bootstrap/app.php';` menjadi `require_once __DIR__.'/bootstrap/app.php';`

4. **Siapkan Database MySQL**
   - Kembali ke cPanel InfinityFree, cari menu **MySQL Databases**.
   - Buat database baru. Anda akan mendapatkan info `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD`.
   - Buka file `.env` di file manager htdocs, lalu ubah pengaturan databasenya:
     ```env
     DB_CONNECTION=mysql
     DB_HOST=sqlxxx.epizy.com
     DB_PORT=3306
     DB_DATABASE=epiz_xxxx_nama_db
     DB_USERNAME=epiz_xxxx
     DB_PASSWORD=password_anda
     ```

5. **Install Dependencies (Vendor)**
   - Karena InfinityFree tidak memiliki akses terminal (SSH), Anda harus meng-upload folder `vendor` dari laptop Anda secara manual (bisa via aplikasi FTP seperti FileZilla agar lebih stabil). 

6. **Selesai!** Website Anda sudah bisa diakses melalui link subdomain Anda.

---

## Opsi 2: Modern & Otomatis (GitHub + Koyeb + Aiven)
Opsi ini cocok jika Anda ingin sistem yang otomatis. Setiap kali Anda melakukan perubahan *code* dan melakukan *Push* ke GitHub, website akan otomatis terupdate. Sistem ini menggunakan arsitektur cloud modern yang dijamin aktif 24/7 tanpa batas waktu.

**Kelebihan:** Deploy otomatis (CI/CD), performa lebih cepat, cocok untuk portofolio profesional.
**Kekurangan:** Setup awal membutuhkan koneksi ke GitHub.

### Langkah 1: Siapkan Database Gratis 24/7 (Aiven)
1. Kunjungi [Aiven.io](https://aiven.io/) dan buat akun gratis.
2. Buat layanan baru, pilih **MySQL** dan pilih paket **Free Plan**.
3. Setelah database dibuat, Anda akan mendapatkan URL koneksi database (Connection URI).
4. Catat Host, Port, User, Password, dan Nama Database tersebut.

### Langkah 2: Upload Kode ke GitHub
1. Buat akun dan repository kosong (Private/Public) di [GitHub](https://github.com/).
2. Push *source code* website pelaporan Anda ke repository tersebut. (Ingat, jangan upload file `.env`).

### Langkah 3: Deploy Website ke Koyeb (Hosting Gratis 24/7)
1. Kunjungi [Koyeb](https://www.koyeb.com/) dan buat akun. Koyeb memiliki *Free Tier* (Eco instance) yang hidup 24/7 tanpa *sleep*.
2. Klik **Create App** atau **Deploy**.
3. Pilih metode **GitHub** dan hubungkan akun GitHub Anda.
4. Pilih repository `website-pelaporan` yang baru saja Anda buat.
5. Pada bagian **Build settings**, pilih tipe **Buildpack**. Koyeb akan otomatis mendeteksi bahwa ini adalah aplikasi PHP/Laravel.
6. Pada bagian **Environment Variables**, tambahkan konfigurasi dari file `.env` lokal Anda, dan jangan lupa masukkan data koneksi Database dari Aiven:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://nama-app-anda.koyeb.app
   APP_KEY=isi_dengan_app_key_dari_file_.env_lokal_anda

   DB_CONNECTION=mysql
   DB_HOST=mysql-xxxx-aiven.aivencloud.com
   DB_PORT=25060
   DB_DATABASE=defaultdb
   DB_USERNAME=avnadmin
   DB_PASSWORD=password_dari_aiven
   ```
7. Pada bagian **Run Command** (perintah yang dijalankan saat web hidup), masukkan:
   ```bash
   php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000
   ```
   *(Catatan: Port di Koyeb harus di-set ke 8000 agar sesuai).*
8. Pilih ukuran *Instance* **Free / Eco** dan klik **Deploy**.
9. Tunggu sekitar 2-3 menit. Website Anda akan online dan terhubung dengan database secara real-time!

---

## Opsi 3: Paling Cepat & Spesifikasi Tertinggi (Oracle Cloud "Always Free" VPS)
Jika Anda memiliki Kartu Debit/Kredit (hanya untuk verifikasi, tidak akan dipotong), Anda bisa mendapatkan server VPS (Virtual Private Server) yang spesifikasinya sangat tinggi (hingga 4 Core ARM, 24GB RAM) **secara gratis seumur hidup** dari Oracle.

**Kelebihan:** 24/7 mutlak, gratis selamanya, spesifikasi dewa, server milik sendiri (bebas install apapun).
**Kekurangan:** Perlu daftar menggunakan kartu verifikasi, setup awal lumayan teknis (seperti merakit komputer kosong).

### Langkah-langkah:
1. Daftar akun di [Oracle Cloud Free Tier](https://www.oracle.com/cloud/free/).
2. Buat instance (Compute) baru dengan *image* Ubuntu.
3. Akses server menggunakan SSH dari terminal komputer Anda.
4. Agar tidak ribet mengatur web server secara manual, Anda bisa menginstall panel kontrol gratis seperti **aaPanel** atau **CloudPanel** hanya dengan satu baris kode di terminal server.
5. Setelah panel terinstall, Anda akan mendapatkan antarmuka yang sangat mirip dengan CPanel (seperti di Opsi 1). Anda bisa membuat database MySQL dan mengupload *source code* Laravel Anda dengan mudah.

---

## Opsi 4: Alternatif Serverless Populer (Vercel + Neon.tech)
Vercel sangat populer untuk deploy gratis 24/7. Walaupun aslinya untuk Node.js/Next.js, Anda tetap bisa mendeploy Laravel ke Vercel menggunakan ekstensi komunitas.

**Kelebihan:** Setup sangat cepat via GitHub, 24/7, otomatis update.
**Kekurangan:** Karena sistemnya *Serverless*, terkadang butuh waktu loading 2-3 detik pada *request* pertama jika web sedang sepi (disebut *Cold Start*).

### Langkah-langkah:
1. Buat database PostgreSQL gratis 24/7 di [Neon.tech](https://neon.tech) atau [Supabase](https://supabase.com) (karena layanan gratis MySQL yang andal semakin langka).
2. Install package **vercel-php** di proyek Laravel Anda menggunakan *Composer*.
3. Buat file bernama `vercel.json` di proyek Anda untuk mengarahkan request ke `public/index.php`.
4. Push kode ke **GitHub**.
5. Login ke [Vercel](https://vercel.com), klik **Add New Project**, dan pilih repository GitHub Anda.
6. Masukkan konfigurasi database dari Neon/Supabase ke bagian *Environment Variables* di dashboard Vercel.
7. Klik **Deploy** dan selesai!

---

### Kesimpulan
- Jika Anda ingin cara lama yang sekadar **Upload-lalu-jalan** (Drag & Drop): Gunakan **Opsi 1 (InfinityFree)**.
- Jika Anda ingin cara yang lebih **Profesional, Otomatis Update, dan Stabil**: Gunakan **Opsi 2 (Koyeb + Aiven)**.
- Jika Anda ingin punya **Server Pribadi super kencang & gratis selamanya**: Gunakan **Opsi 3 (Oracle Cloud)**.
- Jika Anda terbiasa dengan **Ekosistem Modern (Serverless)**: Gunakan **Opsi 4 (Vercel + Neon)**.
