# 🚀 Aplikasi Chat Real-Time (Laravel + Reverb)

Aplikasi *chatting* berbasis web yang mendukung obrolan pribadi (1-on-1) dan obrolan grup secara *real-time*.

## ✨ Fitur Utama

- **🔐 Sistem Keamanan:** Registrasi dan Login akun yang aman.
- **🟢 Radar Online/Offline:** Indikator status pengguna secara *real-time* (Presence Channel).
- **💬 Private Chat:** Kirim pesan pribadi antar pengguna tanpa perlu *refresh* halaman.
- **👥 Group Chat:** Buat grup baru, bergabung ke dalam grup, dan kirim obrolan massal secara *real-time*.
- **⚡ Super Cepat:** Komunikasi data instan menggunakan **Laravel Reverb** (WebSockets) dan **Axios**.

## 🛠️ Teknologi yang Digunakan

- **Backend:** Laravel 11, PHP
- **Frontend:** Blade Templating, Tailwind CSS, JavaScript (ES6+)
- **Database:** MySQL
- **WebSockets:** Laravel Reverb & Laravel Echo

## ⚙️ Persyaratan Sistem (Prerequisites)

Sebelum menjalankan aplikasi ini, pastikan komputer Anda sudah terinstal:
- PHP 8.2 atau lebih baru
- Composer
- Node.js & NPM
- MySQL (XAMPP / Laragon)

## 🚀 Panduan Instalasi & Menjalankan Aplikasi

Ikuti langkah-langkah berikut untuk menjalankan aplikasi di komputer lokal (Localhost):

**1. Buka Terminal di folder project, lalu instal semua dependency:**
```bash
composer install
npm install
```

**2. Siapkan file konfigurasi:**
Duplikat file `.env.example` menjadi `.env`.
Buka file `.env` dan sesuaikan koneksi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=web_chat
DB_USERNAME=root
DB_PASSWORD=
```

**3. Generate Application Key:**
```bash
php artisan key:generate
```

**4. Bangun struktur Database (Migration):**
Pastikan MySQL Anda sudah menyala, lalu jalankan:
```bash
php artisan migrate:fresh
```

**5. Nyalakan Mesin Aplikasi (PENTING!)**
Aplikasi ini membutuhkan **3 terminal** yang berjalan secara bersamaan agar fitur *real-time* berfungsi:

- **Terminal 1 (Server Laravel):**
  ```bash
  php artisan serve
  ```
- **Terminal 2 (Server WebSocket Reverb):**
  ```bash
  php artisan reverb:start
  ```
- **Terminal 3 (Kompilasi Frontend/Vite):**
  ```bash
  npm run dev
  ```

**6. Selesai! 🎉**
Buka browser dan akses: `http://localhost:8000`. 
Silakan buat 2 akun yang berbeda (bisa menggunakan mode *Incognito*) untuk menguji fitur *chat real-time*.

---