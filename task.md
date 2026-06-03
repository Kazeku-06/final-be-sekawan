# RESTful API Laravel - CV. Amanah Elektronik

## Deskripsi Project

Buat sebuah RESTful API Backend menggunakan Laravel untuk sistem penyewaan alat elektronik bernama **CV. Amanah Elektronik**.

Sistem digunakan untuk manajemen rental alat elektronik seperti:

* Smartphone
* Laptop
* Kamera
* dan alat elektronik lainnya.

Backend harus menyediakan API untuk:

* autentikasi admin
* manajemen kategori
* manajemen alat
* manajemen pelanggan
* upload identitas pelanggan
* transaksi penyewaan
* detail penyewaan
* pengurangan stok otomatis
* validasi request
* response JSON standar

---

# Teknologi yang Digunakan

Gunakan:

* Laravel versi terbaru
* MySQL
* JWT Authentication (`tymon/jwt-auth`)
* RESTful API
* Form Request Validation
* Eloquent Relationship
* Migration
* Seeder (opsional)
* API Resource (opsional)
* DB Transaction untuk transaksi penyewaan

Semua endpoint wajib menggunakan response JSON.

Gunakan HTTP status code yang sesuai.

---

# Struktur Database

## 1. Tabel `admin`

Digunakan untuk autentikasi administrator.

### Kolom

| Nama Kolom     | Tipe        |
| -------------- | ----------- |
| admin_id       | Primary Key |
| admin_username | String      |
| admin_password | String      |

---

## 2. Tabel `kategori`

Digunakan untuk menyimpan kategori alat.

Contoh:

* Kamera
* Laptop
* Smartphone

### Kolom

| Nama Kolom    | Tipe        |
| ------------- | ----------- |
| kategori_id   | Primary Key |
| kategori_name | String      |

### Relasi

* satu kategori memiliki banyak alat

---

## 3. Tabel `alat`

Digunakan untuk menyimpan data alat elektronik yang disewakan.

### Kolom

| Nama Kolom        | Tipe        |
| ----------------- | ----------- |
| alat_id           | Primary Key |
| alat_kategori_id  | Foreign Key |
| alat_nama         | String      |
| alat_deskripsi    | Text        |
| alat_hargaperhari | Integer     |
| alat_stok         | Integer     |

### Relasi

* satu alat dimiliki satu kategori
* satu alat dapat muncul di banyak penyewaan_detail

---

## 4. Tabel `pelanggan`

Digunakan untuk menyimpan data pelanggan.

### Kolom

| Nama Kolom       | Tipe        |
| ---------------- | ----------- |
| pelanggan_id     | Primary Key |
| pelanggan_nama   | String      |
| pelanggan_alamat | Text        |
| pelanggan_notelp | String      |
| pelanggan_email  | String      |

### Relasi

* satu pelanggan memiliki banyak pelanggan_data
* satu pelanggan memiliki banyak penyewaan

---

## 5. Tabel `pelanggan_data`

Digunakan untuk menyimpan file identitas pelanggan.

### Kolom

| Nama Kolom                  | Tipe              |
| --------------------------- | ----------------- |
| pelanggan_data_id           | Primary Key       |
| pelanggan_data_pelanggan_id | Foreign Key       |
| pelanggan_data_jenis        | ENUM('KTP','SIM') |
| pelanggan_data_file         | String            |

### Relasi

* satu pelanggan_data dimiliki satu pelanggan

### Ketentuan Upload File

Format file yang diperbolehkan:

* jpg
* jpeg
* png

File harus disimpan ke:

```bash
storage/app/public
```

Path file disimpan ke database.

---

## 6. Tabel `penyewaan`

Digunakan sebagai header transaksi penyewaan.

### Kolom

| Nama Kolom               | Tipe        |
| ------------------------ | ----------- |
| penyewaan_id             | Primary Key |
| penyewaan_pelanggan_id   | Foreign Key |
| penyewaan_tglsewa        | Date        |
| penyewaan_tglkembali     | Date        |
| penyewaan_sttspembayaran | ENUM        |
| penyewaan_sttskembali    | ENUM        |
| penyewaan_totalharga     | Integer     |

### ENUM Pembayaran

* Lunas
* Belum Dibayar
* DP

### ENUM Pengembalian

* Sudah Kembali
* Belum Kembali

### Default Value

```text
penyewaan_sttspembayaran = Belum Dibayar
penyewaan_sttskembali = Belum Kembali
```

### Relasi

* satu penyewaan dimiliki satu pelanggan
* satu penyewaan memiliki banyak penyewaan_detail

---

## 7. Tabel `penyewaan_detail`

Digunakan untuk menyimpan detail alat yang disewa pada transaksi.

### Kolom

| Nama Kolom                    | Tipe        |
| ----------------------------- | ----------- |
| penyewaan_detail_id           | Primary Key |
| penyewaan_detail_penyewaan_id | Foreign Key |
| penyewaan_detail_alat_id      | Foreign Key |
| penyewaan_detail_jumlah       | Integer     |
| penyewaan_detail_subharga     | Integer     |

### Relasi

* satu detail dimiliki satu penyewaan
* satu detail dimiliki satu alat

---

# Relasi Database

## Relasi Eloquent

```text
kategori hasMany alat
alat belongsTo kategori

pelanggan hasMany pelanggan_data
pelanggan hasMany penyewaan

penyewaan belongsTo pelanggan
penyewaan hasMany penyewaan_detail

penyewaan_detail belongsTo penyewaan
penyewaan_detail belongsTo alat
```

Gunakan foreign key dan cascade delete jika diperlukan.

Karena hidup manusia sudah cukup rumit, database jangan ikut-ikutan bikin dosa referential integrity.

---

# JWT Authentication

Gunakan package:

```bash
tymon/jwt-auth
```

Buat fitur:

* login admin
* logout admin
* profile admin (`/me`)

Semua endpoint selain login wajib menggunakan middleware JWT Authentication.

---

# Struktur Endpoint REST API

Gunakan format RESTful API.

Contoh endpoint:

```http
GET    /api/alat
GET    /api/alat/{id}
POST   /api/alat
PATCH  /api/alat/{id}
DELETE /api/alat/{id}
```

Buat CRUD lengkap untuk:

* kategori
* alat
* pelanggan
* pelanggan_data
* penyewaan
* penyewaan_detail

---

# Validasi Request

Gunakan Form Request Validation.

## Contoh Validasi

```text
required
email
numeric
integer
exists
image
mimes:jpg,jpeg,png
min
max
```

---

# Response Validasi Error

HTTP Code:

```http
422 Unprocessable Entity
```

Response:

```json
{
  "success": false,
  "message": "Validation Error",
  "data": null,
  "errors": {
    "field": [
      "pesan error"
    ]
  }
}
```

Karena user itu makhluk kreatif. Kalau tidak divalidasi mereka bakal upload PDF skripsi ke field foto KTP.

---

# Logic Penyewaan

Saat membuat transaksi penyewaan:

1. cek pelanggan tersedia
2. cek alat tersedia
3. cek stok alat mencukupi
4. hitung durasi sewa
5. hitung subharga tiap alat
6. hitung total harga
7. simpan data penyewaan
8. simpan penyewaan_detail
9. kurangi stok alat otomatis

Gunakan:

```php
DB::transaction()
```

Jika salah satu proses gagal:

* rollback semua query

---

# Rumus Perhitungan

## Durasi

```text
tanggal_kembali - tanggal_sewa
```

## Subharga

```text
harga_perhari × jumlah × durasi
```

## Total Harga

```text
total seluruh subharga
```

---

# Response JSON Standar

## Success Response

```json
{
  "success": true,
  "message": "Successfully get data",
  "data": []
}
```

---

## Error Response

```json
{
  "success": false,
  "message": "There error in Internal Server",
  "data": null,
  "errors": "error message"
}
```

---

# HTTP Status Code

Gunakan:

| Code | Keterangan            |
| ---- | --------------------- |
| 200  | OK                    |
| 201  | Created               |
| 401  | Unauthorized          |
| 404  | Not Found             |
| 422  | Validation Error      |
| 500  | Internal Server Error |

---

# Struktur Project Laravel

Pisahkan:

* Controller
* Model
* Form Request Validation
* Service Class (opsional)
* Resource/API Resource (opsional)

## Ketentuan

### Controller

Hanya menangani:

* request
* response

### Business Logic

Pisahkan ke:

* Service Class

Agar controller tidak berubah menjadi skripsi 900 baris yang membuat siapa pun kehilangan keinginan hidup saat debugging.

---

# Upload File

Gunakan:

```php
Storage::put()
```

Atau:

```php
store()
```

Simpan file ke:

```bash
storage/app/public
```

Jalankan:

```bash
php artisan storage:link
```

---

# Testing API

Gunakan:

* Postman
* atau Cypress

## Testing yang Harus Dibuat

* login admin
* unauthorized access
* CRUD semua entity
* upload file identitas
* validasi error
* transaksi penyewaan
* stok habis
* gagal transaksi
* response JSON
* HTTP status code

---

# Output yang Harus Digenerate

Project harus menghasilkan:

1. migration lengkap
2. model + relasi
3. controller lengkap
4. form request validation
5. route api
6. JWT auth setup
7. logic transaksi penyewaan
8. upload file logic
9. response JSON standar
10. contoh request dan response
11. struktur folder project
12. contoh testing Postman
13. clean code dan komentar penjelasan

---

# Ketentuan Tambahan

* Gunakan clean code
* Gunakan naming yang konsisten
* Gunakan best practice Laravel
* Hindari query berulang
* Gunakan eager loading jika diperlukan
* Gunakan try catch untuk error handling
* Semua endpoint wajib return JSON
* Semua endpoint selain login wajib memakai JWT middleware

---
