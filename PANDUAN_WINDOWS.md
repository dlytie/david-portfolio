# Panduan menjalankan di Windows dan Laragon

## 1. Buka terminal Laragon

Gunakan menu **Laragon → Terminal** agar PHP dan Composer Laragon lebih mudah dikenali.

Masuk ke folder proyek, misalnya:

```powershell
cd C:\laragon\www\david-3d-portfolio
```

Periksa program yang digunakan:

```powershell
php -v
composer --version
node -v
npm -v
```

Laravel pada proyek ini membutuhkan PHP 8.3 atau yang lebih baru dan kompatibel. Apabila `php` tidak dikenali di PowerShell biasa, gunakan Terminal Laragon atau aktifkan versi PHP yang benar melalui menu Laragon.

## 2. Instal proyek

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
npm run build
```

Proyek tidak membutuhkan migrasi database untuk menampilkan halaman portofolio.

## 3. Jalankan

Terminal pertama:

```powershell
php artisan serve
```

Untuk pengembangan dengan pembaruan CSS/JavaScript otomatis, buka terminal kedua:

```powershell
npm run dev
```

Kemudian buka:

```text
http://127.0.0.1:8000
```

## 4. Tambahkan file pribadi

```text
Foto       public\images\profile\david.webp
CV         public\documents\cv-david.pdf
Screenshot public\images\projects\
Model GLB  public\models\workspace\custom-workspace.glb
```

Foto, CV, screenshot, dan model GLB dideteksi saat halaman dimuat. Tidak perlu mengedit komponen Blade.

## 5. Tambahkan email dan media sosial

Edit `.env`, lalu isi variabel `PORTFOLIO_*`. Setelah selesai:

```powershell
php artisan optimize:clear
```

## 6. Periksa sebelum dipublikasikan

```powershell
php artisan test
npm run build
```

Pastikan tidak ada error, semua tautan benar, dan file `.env` tidak pernah diunggah ke GitHub atau hosting publik.
