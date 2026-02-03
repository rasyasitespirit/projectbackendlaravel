# CV. Amanah Elektronik - Backend API

Backend API untuk sistem manajemen penyewaan alat elektronik CV. Amanah Elektronik. Dibangun menggunakan Laravel 11 dengan JWT Authentication.

## Tech Stack

- **Framework**: Laravel 11
- **Database**: MySQL / SQLite
- **Authentication**: JWT (tymon/jwt-auth)
- **PHP Version**: 8.2+

## Fitur

- Authentication (Login Admin dengan JWT)
- CRUD Pelanggan
- CRUD Data Pelanggan (KTP/SIM)
- CRUD Kategori Alat
- CRUD Alat
- CRUD Penyewaan
- CRUD Detail Penyewaan
- Upload file (KTP/SIM)

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/rasyasitespirit/projectbackendlaravel.git
cd projectbackendlaravel
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Setup Environment

Copy file `.env.example` menjadi `.env`:

```bash
copy .env.example .env
```

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=CV_AmanahElektronik
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Generate JWT Secret

```bash
php artisan jwt:secret
```

### 6. Jalankan Migration

```bash
php artisan migrate
```

### 7. Jalankan Seeder

```bash
php artisan db:seed
```

Seeder akan membuat:
- 1 Admin (email: admin@example.com, password: password)
- 10 Pelanggan dengan data lengkap
- 6 Kategori alat
- 18 Alat
- 8 Penyewaan dengan detail

### 8. Create Storage Link

```bash
php artisan storage:link
```

### 9. Jalankan Server

```bash
php artisan serve
```

Server akan berjalan di `http://localhost:8000`

## Testing API dengan Postman

### 1. Login Admin

**Endpoint**: `POST http://localhost:8000/api/auth/login`

**Body** (JSON):
```json
{
    "admin_email": "admin@example.com",
    "admin_password": "password"
}
```

**Response**:
```json
{
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
    "token_type": "bearer",
    "expires_in": 3600
}
```

Copy `access_token` untuk digunakan di request selanjutnya.

### 2. Setup Authorization di Postman

Untuk semua endpoint yang memerlukan authentication:
1. Buka tab **Authorization**
2. Pilih Type: **Bearer Token**
3. Paste token yang didapat dari login

### 3. Test Endpoint Pelanggan

**Get All Pelanggan**:
```
GET http://localhost:8000/api/pelanggan
```

**Get Single Pelanggan**:
```
GET http://localhost:8000/api/pelanggan/1
```

**Create Pelanggan**:
```
POST http://localhost:8000/api/pelanggan
```
Body (JSON):
```json
{
    "pelanggan_nama": "John Doe",
    "pelanggan_alamat": "Jl. Contoh No. 123",
    "pelanggan_notelp": "081234567890",
    "pelanggan_email": "john@example.com"
}
```

**Update Pelanggan**:
```
PUT http://localhost:8000/api/pelanggan/1
```
Body (JSON):
```json
{
    "pelanggan_nama": "John Doe Updated",
    "pelanggan_alamat": "Jl. Contoh No. 456",
    "pelanggan_notelp": "081234567890",
    "pelanggan_email": "john@example.com"
}
```

**Delete Pelanggan**:
```
DELETE http://localhost:8000/api/pelanggan/1
```

### 4. Test Endpoint Kategori

**Get All Kategori**:
```
GET http://localhost:8000/api/kategori
```

**Create Kategori**:
```
POST http://localhost:8000/api/kategori
```
Body (JSON):
```json
{
    "kategori_nama": "Alat Elektronik"
}
```

### 5. Test Endpoint Alat

**Get All Alat**:
```
GET http://localhost:8000/api/alat
```

**Create Alat**:
```
POST http://localhost:8000/api/alat
```
Body (JSON):
```json
{
    "alat_kategori_id": 1,
    "alat_nama": "Bor Listrik Makita",
    "alat_deskripsi": "Bor listrik 13mm dengan kecepatan variabel",
    "alat_hargaperhari": 50000,
    "alat_stok": 5
}
```

### 6. Test Endpoint Penyewaan

**Get All Penyewaan**:
```
GET http://localhost:8000/api/penyewaan
```

**Create Penyewaan**:
```
POST http://localhost:8000/api/penyewaan
```
Body (JSON):
```json
{
    "penyewaan_pelanggan_id": 1,
    "penyewaan_tglsewa": "2024-02-01",
    "penyewaan_tglkembali": "2024-02-05",
    "penyewaan_sttspembayaran": "DP",
    "penyewaan_sttskembali": "Belum Kembali",
    "penyewaan_totalharga": 200000
}
```

### 7. Test Upload File (Pelanggan Data)

**Create Pelanggan Data dengan Upload**:
```
POST http://localhost:8000/api/pelanggan-data
```

Di Postman:
1. Pilih tab **Body**
2. Pilih **form-data**
3. Tambahkan fields:
   - `pelanggan_data_pelanggan_id`: 1
   - `pelanggan_data_jenis`: KTP
   - `pelanggan_data_file`: [pilih file gambar]

## Struktur Database

### Tabel Admin
- admin_id (PK)
- admin_nama
- admin_email (unique)
- admin_password

### Tabel Pelanggan
- pelanggan_id (PK)
- pelanggan_nama
- pelanggan_alamat
- pelanggan_notelp
- pelanggan_email

### Tabel Pelanggan Data
- pelanggan_data_id (PK)
- pelanggan_data_pelanggan_id (FK)
- pelanggan_data_jenis (KTP/SIM)
- pelanggan_data_file

### Tabel Kategori
- kategori_id (PK)
- kategori_nama

### Tabel Alat
- alat_id (PK)
- alat_kategori_id (FK)
- alat_nama
- alat_deskripsi
- alat_hargaperhari
- alat_stok

### Tabel Penyewaan
- penyewaan_id (PK)
- penyewaan_pelanggan_id (FK)
- penyewaan_tglsewa
- penyewaan_tglkembali
- penyewaan_sttspembayaran (Lunas/Belum Dibayar/DP)
- penyewaan_sttskembali (Sudah Kembali/Belum Kembali)
- penyewaan_totalharga

### Tabel Penyewaan Detail
- penyewaan_detail_id (PK)
- penyewaan_detail_penyewaan_id (FK)
- penyewaan_detail_alat_id (FK)
- penyewaan_detail_jumlah
- penyewaan_detail_subharga

## API Documentation

Dokumentasi lengkap API tersedia di file `API_DOCUMENTATION.md`

## Troubleshooting

### Error: "Unauthenticated"
- Pastikan token JWT sudah di-set di Authorization header
- Pastikan token belum expired (default 60 menit)
- Login ulang untuk mendapatkan token baru

### Error: "SQLSTATE[42S22]: Column not found"
- Jalankan `php artisan migrate:fresh --seed` untuk reset database

### Error: "The file could not be uploaded"
- Pastikan folder `storage/app/public` ada
- Jalankan `php artisan storage:link`
- Cek permission folder storage

## License

MIT License
