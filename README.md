# David 3D Portfolio

Website portofolio satu halaman berbasis Laravel 13 dengan ruang kerja 3D interaktif. Konten profesional tetap dirender sebagai HTML semantik, sementara Three.js menjadi lapisan visual tambahan. Foto, CV, screenshot proyek, tautan kontak, dan model workspace kustom dapat ditambahkan kemudian tanpa membangun ulang struktur halaman.

## Fitur

- Hero modern dengan workspace 3D interaktif.
- Model kantor ringan dari Kenney Furniture Kit (CC0).
- Monitor 3D menuju proyek dan router 3D menuju keahlian.
- Animasi scroll menggunakan GSAP ScrollTrigger.
- Navigasi, modal proyek native, dan menu ponsel menggunakan Alpine.js.
- Empat proyek awal yang dikelola melalui satu file konfigurasi.
- Placeholder visual otomatis untuk foto dan screenshot yang belum tersedia.
- Model GLB kustom dapat menggantikan scene bawaan cukup dengan menambahkan satu file.
- Form kontak dengan validasi, honeypot, CSRF, dan rate limiting.
- Fallback WebGL, reduced motion, dan kualitas renderer adaptif.
- Metadata SEO dan JSON-LD.
- Detail studi kasus berisi masalah, solusi, kontribusi, fitur, tantangan, dan hasil.
- Pengujian Feature untuk halaman portofolio dan form kontak.

## Teknologi

- Laravel 13
- PHP 8.3 atau lebih baru yang kompatibel
- Blade Components
- Vite 8
- Tailwind CSS 4 sebagai fondasi build CSS
- Three.js
- GSAP + ScrollTrigger
- Alpine.js

## Instalasi cepat

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Untuk melihat snapshot antarmuka tanpa menjalankan PHP, sajikan folder `public` melalui web server lokal lalu buka `preview.html`. Snapshot ini hanya untuk pratinjau visual; konten dinamis, deteksi aset pribadi, dan pengiriman form tetap berjalan melalui Laravel.

Untuk pengembangan frontend, jalankan dua terminal:

```bash
php artisan serve
```

```bash
npm run dev
```

Proyek ini tidak membutuhkan database untuk menampilkan portofolio. Konfigurasi awal memakai session dan cache berbasis file.

## Mengisi konten nanti

### Profil, proyek, keahlian, dan pengalaman

Edit:

```text
config/portfolio.php
```

Semua kartu proyek berasal dari array `projects`. Untuk menambah proyek, salin satu item, ubah `slug`, isi konten, dan tambahkan nama gambar yang diinginkan.

### Foto profil

Masukkan:

```text
public/images/profile/david.webp
```

### CV

Masukkan:

```text
public/documents/cv-david.pdf
```

### Screenshot proyek

Masukkan:

```text
public/images/projects/david-sales.webp
public/images/projects/finance-panel.webp
public/images/projects/wedding-invitation.webp
public/images/projects/home-server.webp
```

### Model 3D kustom

Masukkan:

```text
public/models/workspace/custom-workspace.glb
```

Jika file belum ada atau gagal dimuat, adegan bawaan tetap digunakan.

## Mengaktifkan kontak

Isi `.env`:

```dotenv
PORTFOLIO_FULL_NAME="Nama lengkap"
PORTFOLIO_AVAILABLE=true
PORTFOLIO_CONTACT_ENABLED=true
PORTFOLIO_EMAIL="email@domain.com"
PORTFOLIO_WHATSAPP="https://wa.me/62xxxxxxxxxxx"
PORTFOLIO_GITHUB="https://github.com/username"
PORTFOLIO_LINKEDIN="https://www.linkedin.com/in/username"
```

Atur konfigurasi `MAIL_*` sesuai penyedia email. Selama `MAIL_MAILER=log`, email tidak benar-benar dikirim dan hanya ditulis ke log Laravel.

Setelah mengubah `.env` atau `config/portfolio.php`, jalankan:

```bash
php artisan optimize:clear
```

## Pemeriksaan

```bash
php artisan test
npm run build
```

## Struktur penting

```text
config/portfolio.php                 seluruh konten portofolio
resources/views/portfolio.blade.php halaman utama
resources/views/components/         komponen Blade
resources/js/three/                  scene dan loader 3D
resources/css/app.css                sistem visual responsif
public/models/workspace/             aset dan model GLB kustom
public/images/                       foto serta screenshot
public/documents/                    CV
```

## Lisensi aset

Source code proyek dapat digunakan dan dikembangkan untuk portofolio David. Model Kenney yang disertakan berlisensi CC0 1.0; lihat `ASSET_SOURCES.md` dan `public/models/workspace/kenney/License.txt`.
