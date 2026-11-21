# Food Store — Panduan Setup & Menjalankan Project

Project **Food Store** ini dibangun menggunakan **Laravel 12**, **Filament v3** (Admin Panel), **JWT Auth**, **RajaOngkir API**, dan **Midtrans Payment Gateway**.

---

## 1. Persiapan Tools
Pastikan di komputer sudah terpasang:
- **PHP** `^8.2` (aktifkan ekstensi: `intl`, `iconv`, `sodium`, `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `bcmath`, `exif`)
- **Composer**
- **MySQL / MariaDB** (bisa menggunakan XAMPP, Laragon, atau MariaDB)

---

## 2. Instalasi Project

Masuk ke folder project, lalu jalankan perintah berikut:

```bash
# 1. Install dependencies PHP
composer install

# 2. Salin file konfigurasi .env (jika belum ada)
cp .env.example .env

# 3. Generate Application Key & JWT Secret
php artisan key:generate
php artisan jwt:secret

# 4. Hubungkan folder storage ke public (agar gambar tampil)
php artisan storage:link
```

---

## 3. Konfigurasi File `.env`

Buka file `.env` dan sesuaikan pengaturan database serta API Key:

```env
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_food_store
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public

# RajaOngkir (Komerce)
RAJAONGKIR_API_KEY=isi_api_key_rajaongkir

# Midtrans
MIDTRANS_CLIENT_KEY=isi_client_key_midtrans
MIDTRANS_SERVER_KEY=isi_server_key_midtrans
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_IS_SANITIZED=true
MIDTRANS_IS_3DS=true

JWT_SHOW_BLACKLIST_EXCEPTION=true
```

> **Catatan Penting untuk `.env`:**
> 1. **`APP_URL=http://localhost:8000`** — Harus menyertakan `:8000` (jika menjalankan pakai `php artisan serve`) agar gambar upload di Filament bisa tampil.
> 2. **`FILESYSTEM_DISK=public`** — Wajib `public` (bukan `local`) supaya gambar produk/kategori/slider bisa diakses publik.
> 3. **`RAJAONGKIR_API_KEY`** — Diisi dengan API Key *Shipping Cost* dari [Komerce/RajaOngkir](https://collaborator.komerce.id/registration).
> 4. **`MIDTRANS_CLIENT_KEY` & `MIDTRANS_SERVER_KEY`** — Diisi dengan Access Keys (mode Sandbox) dari [Dashboard Midtrans](https://dashboard.midtrans.com/register).
> 5. **`APP_KEY` & `JWT_SECRET`** — Jangan diisi manual dengan teks biasa; wajib di-generate melalui perintah `php artisan key:generate` dan `php artisan jwt:secret`.

---

## 4. Setup Database & Akun Admin

1. Buat database baru di MySQL/MariaDB dengan nama **`db_food_store`** (atau sesuaikan dengan `DB_DATABASE` di `.env`).
2. Jalankan migrasi tabel sekaligus seeder:
   ```bash
   php artisan migrate --seed
   ```
   *(Atau buat akun admin baru secara manual dengan `php artisan make:filament-user`).*

---

## 5. Menjalankan Server

```bash
php artisan serve
```

- **Admin Panel Filament:** `http://127.0.0.1:8000/admin`
- **Base URL RESTful API:** `http://127.0.0.1:8000/api`

---

## 6. Troubleshooting (Jika Terjadi Error)

1. **`Unsupported cipher or incorrect key length`**
   - **Penyebab:** `APP_KEY` di `.env` masih kosong atau masih berisi teks placeholder.
   - **Solusi:** Jalankan `php artisan key:generate` lalu restart `php artisan serve`.
2. **`Unknown database 'db_food_store'` atau `Table 'db_food_store.users' doesn't exist`**
   - **Penyebab:** Database belum dibuat di MySQL/MariaDB atau tabel belum dimigrasi.
   - **Solusi:** Pastikan service MySQL/MariaDB menyala, buat database `db_food_store`, lalu jalankan `php artisan migrate --seed`.
3. **Error ekstensi PHP (`ext-intl`, `ext-iconv`, `ext-sodium`) saat `composer install`**
   - **Penyebab:** Ekstensi PHP tersebut belum aktif di konfigurasi `php.ini`.
   - **Solusi:** Aktifkan ekstensi yang diminta di `php.ini`, lalu jalankan ulang `composer install`.
