# Aset workspace 3D

Adegan bawaan menggabungkan bentuk Three.js yang ringan dengan beberapa model dari Kenney Furniture Kit.

## Menggunakan model workspace sendiri

1. Siapkan satu model GLB yang sudah dioptimalkan.
2. Beri nama `custom-workspace.glb`.
3. Letakkan langsung di folder ini.
4. Muat ulang halaman.

Laravel akan mendeteksi file tersebut secara otomatis. Apabila model dapat dimuat, model kustom menggantikan workspace bawaan. Apabila model gagal dimuat, workspace bawaan tetap tampil.

Rekomendasi model kustom:

- Maksimal sekitar 100.000 triangle untuk desktop; lebih kecil lebih baik.
- Gunakan tekstur WebP/KTX2 bila alur ekspor mendukungnya.
- Hindari tekstur 4K jika tidak benar-benar diperlukan.
- Letakkan titik dasar model pada lantai dan arah depan ke sumbu `+Z`.
- Usahakan ukuran file di bawah 3–5 MB.

## Aset bawaan

Model GLB dalam folder `kenney/` dikonversi dari model FBX **Kenney Furniture Kit**, lisensi **Creative Commons Zero (CC0 1.0)**. Salinan lisensi asli tersedia pada `kenney/License.txt`.

Daftar model yang benar-benar digunakan, tanggal verifikasi, dan catatan lisensi juga tersedia pada `../../../ASSET_SOURCES.md` serta folder `licenses/`.

Sumber: https://kenney.nl/assets/furniture-kit
