# Testing API — CV. Amanah Elektronik

Base URL: `http://localhost:8000/api`

Semua request protected wajib pakai header:
```
Authorization: Bearer {token}
Accept: application/json
```

---

## 1. AUTH

### Login
**POST** `/api/auth/login`

Request:
```json
{
  "admin_username": "admin",
  "admin_password": "password123"
}
```

Response 200:
```json
{
  "success": true,
  "message": "Login berhasil.",
  "data": {
    "admin": { "admin_id": 1, "admin_username": "admin" },
    "access_token": "eyJ...",
    "token_type": "bearer",
    "expires_in": 3600
  }
}
```

---

### Me / Profile
**GET** `/api/auth/me` *(butuh token)*

Response 200:
```json
{
  "success": true,
  "message": "Successfully get profile",
  "data": { "admin_id": 1, "admin_username": "admin" }
}
```

---

### Logout
**POST** `/api/auth/logout` *(butuh token)*

Response 200:
```json
{ "success": true, "message": "Logout berhasil.", "data": null }
```

---

### Unauthorized (tanpa token)
**GET** `/api/kategori` (tanpa header Authorization)

Response 401:
```json
{
  "success": false,
  "message": "Unauthorized. Token not provided",
  "data": null,
  "errors": null
}
```

---

## 2. KATEGORI

### Get All
**GET** `/api/kategori`

Response 200:
```json
{
  "success": true,
  "message": "Successfully get data",
  "data": [
    { "kategori_id": 1, "kategori_name": "Laptop", "alat_count": 2 }
  ]
}
```

### Create
**POST** `/api/kategori`
```json
{ "kategori_name": "Kamera" }
```

### Show
**GET** `/api/kategori/1`

### Update
**PATCH** `/api/kategori/1`
```json
{ "kategori_name": "Kamera DSLR" }
```

### Delete
**DELETE** `/api/kategori/1`

### Validation Error
**POST** `/api/kategori` dengan body kosong:
Response 422:
```json
{
  "success": false,
  "message": "Validation Error",
  "data": null,
  "errors": { "kategori_name": ["The kategori name field is required."] }
}
```

---

## 3. ALAT

### Create
**POST** `/api/alat`
```json
{
  "alat_kategori_id": 1,
  "alat_nama": "Lenovo ThinkPad X1",
  "alat_deskripsi": "Laptop bisnis premium",
  "alat_hargaperhari": 150000,
  "alat_stok": 5
}
```

### Get All
**GET** `/api/alat`

### Show
**GET** `/api/alat/1`

### Update
**PATCH** `/api/alat/1`
```json
{ "alat_stok": 10 }
```

### Delete
**DELETE** `/api/alat/1`

---

## 4. PELANGGAN

### Create
**POST** `/api/pelanggan`
```json
{
  "pelanggan_nama": "Budi Santoso",
  "pelanggan_alamat": "Jl. Merdeka No.10 Surabaya",
  "pelanggan_notelp": "081234567890",
  "pelanggan_email": "budi@example.com"
}
```

### Get All
**GET** `/api/pelanggan`

### Show
**GET** `/api/pelanggan/1`

### Update
**PATCH** `/api/pelanggan/1`
```json
{ "pelanggan_alamat": "Jl. Sudirman No. 5 Jakarta" }
```

### Delete
**DELETE** `/api/pelanggan/1`

---

## 5. UPLOAD IDENTITAS PELANGGAN

### Upload KTP/SIM
**POST** `/api/pelanggan-data`

Form Data (multipart/form-data):
```
pelanggan_data_pelanggan_id = 1
pelanggan_data_jenis        = KTP
pelanggan_data_file         = [file: ktp.jpg]
```

Response 201:
```json
{
  "success": true,
  "message": "Identitas pelanggan berhasil diunggah.",
  "data": {
    "pelanggan_data_id": 1,
    "pelanggan_data_pelanggan_id": 1,
    "pelanggan_data_jenis": "KTP",
    "pelanggan_data_file": "identitas/xxxx.jpg",
    "pelanggan": { "pelanggan_id": 1, "pelanggan_nama": "Budi Santoso" }
  }
}
```

### Validation Error — File Bukan Gambar
Upload file PDF:
Response 422:
```json
{
  "success": false,
  "message": "Validation Error",
  "data": null,
  "errors": {
    "pelanggan_data_file": ["The pelanggan data file field must be an image."]
  }
}
```

---

## 6. PENYEWAAN (Transaksi)

### Buat Transaksi
**POST** `/api/penyewaan`
```json
{
  "penyewaan_pelanggan_id": 1,
  "penyewaan_tglsewa": "2026-06-05",
  "penyewaan_tglkembali": "2026-06-08",
  "penyewaan_sttspembayaran": "Lunas",
  "details": [
    { "alat_id": 1, "jumlah": 2 }
  ]
}
```

Response 201 (durasi 3 hari, harga 150.000, jumlah 2):
```json
{
  "success": true,
  "message": "Transaksi penyewaan berhasil dibuat.",
  "data": {
    "penyewaan_id": 1,
    "penyewaan_totalharga": 900000,
    "penyewaan_sttskembali": "Belum Kembali",
    "detail": [
      {
        "penyewaan_detail_jumlah": 2,
        "penyewaan_detail_subharga": 900000,
        "alat": { "alat_nama": "Lenovo ThinkPad X1", "alat_stok": 3 }
      }
    ]
  }
}
```

### Stok Habis
Request dengan jumlah melebihi stok:
Response 422:
```json
{
  "success": false,
  "message": "Stok alat 'Lenovo ThinkPad X1' tidak mencukupi. Tersedia: 3, diminta: 10.",
  "data": null,
  "errors": null
}
```

### Update Status Pembayaran
**PATCH** `/api/penyewaan/1`
```json
{
  "penyewaan_sttspembayaran": "Lunas",
  "penyewaan_sttskembali": "Sudah Kembali"
}
```

### Get All
**GET** `/api/penyewaan`

### Show
**GET** `/api/penyewaan/1`

### Delete
**DELETE** `/api/penyewaan/1`

---

## 7. PENYEWAAN DETAIL

### Get All
**GET** `/api/penyewaan-detail`

### Show
**GET** `/api/penyewaan-detail/1`

---

## Rumus Perhitungan

```
Durasi    = tglkembali - tglsewa        → 3 hari
Subharga  = hargaperhari × jumlah × durasi → 150.000 × 2 × 3 = 900.000
Total     = sum(semua subharga)
```
