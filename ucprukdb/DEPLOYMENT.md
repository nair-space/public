# Panduan Deployment UCPRUK DB (Windows)

Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini di laptop baru:

## 1. Persiapan Software (Prerequisites)
Pastikan laptop baru sudah terinstal:
- **XAMPP**: Gunakan versi yang mendukung PHP 8.2 ke atas.
- **Composer**: Untuk instalasi library PHP.
- **Node.js & npm**: Untuk kompilasi aset frontend.
- **Git**: Untuk menarik kode dari GitHub.

## 2. Persiapan Database
1. Buka XAMPP Control Panel dan jalankan **Apache** & **MySQL**.
2. Buka `http://localhost/phpmyadmin`.
3. Buat database baru dengan nama `ucprukdb`.

## 3. Clone Proyek
Buka Terminal/PowerShell di folder tujuan (misal: `C:\xampp\htdocs`) dan jalankan:
```bash
git clone https://github.com/nair-space/ucpruk_db.git
cd ucpruk_db
```

## 4. Instalasi Dependensi
Jalankan perintah berikut secara berurutan:
```bash
# Instal library backend
composer install

# Instal library frontend
npm install
```

## 5. Konfigurasi Environment
1. Salin file `.env.example` menjadi `.env`:
   ```bash
   copy .env.example .env
   ```
2. Buka file `.env` dan pastikan pengaturan database sesuai:
   ```ini
   DB_CONNECTION=mariadb
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ucprukdb
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Generate kunci aplikasi:
   ```bash
   php artisan key:generate
   ```

## 6. Migrasi & Seeding Data
Isi database dengan struktur dan data awal:
```bash
php artisan migrate --seed
```

## 7. Build Aset & Jalankan
```bash
# Kompilasi CSS/JS
npm run build

# Jalankan server
php artisan serve --port=8080
```

Aplikasi sekarang dapat diakses di `http://localhost:8080`.
