# Cara Setup Google Sheets per Kategori

## 🎯 Tujuan
Membuat Google Sheets terpisah untuk setiap kategori (Kesiswaan, Kurikulum, Sapras & Humas, Tata Usaha) agar data Links dan Forms terorganisir dengan baik.

---

## 📋 Langkah-langkah

### 1️⃣ Buat 4 Google Sheets Baru

Buat Google Sheets baru untuk setiap kategori:

#### a) Sheets Kesiswaan
1. Buka: https://sheets.google.com
2. Klik **+ Blank** (Spreadsheet baru)
3. Rename menjadi: **"Data Kesiswaan - SMKN 62"**
4. Buat 2 tabs/sheet:
   - `Links-Kesiswaan` 
   - `Forms-Kesiswaan`
5. **Copy ID dari URL** (bagian setelah `/d/` dan sebelum `/edit`)
   ```
   https://docs.google.com/spreadsheets/d/[INI_ID_NYA]/edit
   ```
   Contoh: `1abc123xyz456def789ghi012jkl345mno678pqr901`

#### b) Sheets Kurikulum
1. Buat spreadsheet baru
2. Rename: **"Data Kurikulum - SMKN 62"**
3. Buat 2 tabs:
   - `Links-Kurikulum`
   - `Forms-Kurikulum`
4. Copy ID-nya

#### c) Sheets Sapras & Humas
1. Buat spreadsheet baru
2. Rename: **"Data Sapras & Humas - SMKN 62"**
3. Buat 2 tabs:
   - `Links-Sapras_humas`
   - `Forms-Sapras_humas`
4. Copy ID-nya

#### d) Sheets Tata Usaha
1. Buat spreadsheet baru
2. Rename: **"Data Tata Usaha - SMKN 62"**
3. Buat 2 tabs:
   - `Links-Tata_usaha`
   - `Forms-Tata_usaha`
4. Copy ID-nya

---

### 2️⃣ Setup Header di Setiap Tab

Untuk setiap tab (Links dan Forms), tambahkan header di baris pertama:

| A | B | C | D | E |
|---|---|---|---|---|
| Title | URL | Created At | Updated At | Category |

**Cara setup:**
1. Buka setiap tab
2. Di baris 1, isi:
   - A1: `Title`
   - B1: `URL`
   - C1: `Created At`
   - D1: `Updated At`
   - E1: `Category`
3. Bold header (Ctrl+B)
4. Opsional: Beri warna background header (misalnya abu-abu muda)

---

### 3️⃣ Update includes/config.php

Setelah mendapat 4 Sheets ID, update file `includes/config.php`:

```php
// Google Sheets ID for Data Guru database (untuk data guru)
define('GOOGLE_SHEETS_ID', '17r-YC2USgTCu-DiK647L0ZbYJSoexWdGofrfVkw8Puw');

// Google Sheets IDs per Category (untuk Links & Forms)
define('SHEETS_KESISWAAN', 'PASTE_ID_SHEETS_KESISWAAN_DISINI');
define('SHEETS_KURIKULUM', 'PASTE_ID_SHEETS_KURIKULUM_DISINI');
define('SHEETS_SAPRAS_HUMAS', 'PASTE_ID_SHEETS_SAPRAS_HUMAS_DISINI');
define('SHEETS_TATA_USAHA', 'PASTE_ID_SHEETS_TATA_USAHA_DISINI');
```

**Contoh setelah diisi:**
```php
// Google Sheets ID for Data Guru database
define('GOOGLE_SHEETS_ID', '17r-YC2USgTCu-DiK647L0ZbYJSoexWdGofrfVkw8Puw');

// Google Sheets IDs per Category
define('SHEETS_KESISWAAN', '1abc123xyz456def789ghi012jkl345mno678pqr901');
define('SHEETS_KURIKULUM', '1def456abc789ghi012jkl345mno678pqr901stu234');
define('SHEETS_SAPRAS_HUMAS', '1ghi789def012jkl345mno678pqr901stu234vwx567');
define('SHEETS_TATA_USAHA', '1jkl012ghi345mno678pqr901stu234vwx567yz0890');
```

---

### 4️⃣ Share Sheets dengan Aplikasi

**PENTING:** Agar aplikasi bisa akses, share setiap sheets:

1. Buka setiap Google Sheets
2. Klik tombol **Share** (kanan atas)
3. **Pilih salah satu:**
   
   **Opsi A: Share dengan Email Aplikasi (Recommended)**
   - Masukkan email dari Google Service Account
   - Set permission: **Editor**
   - Klik **Done**
   
   **Opsi B: Share dengan Siapa Saja (Anyone with Link)**
   - Klik "Change to anyone with the link"
   - Set permission: **Editor**
   - Klik **Done**

4. Ulangi untuk ke-4 sheets

---

## 🔄 Alternatif: Gunakan 1 Sheets dengan Multiple Tabs

Jika tidak ingin membuat 4 sheets terpisah, Anda bisa gunakan **1 sheets dengan 8 tabs**:

### Setup 1 Sheets Multi-Tabs:

1. Buat 1 Google Sheets baru: **"Data Management - SMKN 62"**
2. Buat 8 tabs:
   - `Links-Kesiswaan`
   - `Forms-Kesiswaan`
   - `Links-Kurikulum`
   - `Forms-Kurikulum`
   - `Links-Sapras_humas`
   - `Forms-Sapras_humas`
   - `Links-Tata_usaha`
   - `Forms-Tata_usaha`
3. Copy 1 ID sheets ini
4. Paste ke SEMUA define di config.php:

```php
// Gunakan ID yang sama untuk semua kategori
define('SHEETS_KESISWAAN', 'ID_SHEETS_SAMA');
define('SHEETS_KURIKULUM', 'ID_SHEETS_SAMA');
define('SHEETS_SAPRAS_HUMAS', 'ID_SHEETS_SAMA');
define('SHEETS_TATA_USAHA', 'ID_SHEETS_SAMA');
```

**Keuntungan:**
- ✅ Lebih mudah manage (1 file)
- ✅ Tidak perlu share berkali-kali
- ✅ Bisa lihat semua data di 1 tempat

**Kekurangan:**
- ❌ File bisa jadi besar kalau data banyak
- ❌ Agak rumit kalau banyak tab

---

## 🧪 Testing

Setelah setup selesai, test aplikasi:

### 1. Test Tambah Link
1. Login ke aplikasi
2. Menu **Links** → **Tambah Link**
3. Pilih kategori: **Kesiswaan**
4. Isi title dan URL
5. Klik **Simpan**
6. Buka Google Sheets Kesiswaan → tab `Links-Kesiswaan`
7. Data harus muncul di baris 2

### 2. Test Filter Kategori
1. Menu **Links** → Klik filter **Kesiswaan**
2. Hanya link dari kategori Kesiswaan yang muncul
3. Ganti filter ke **Kurikulum**
4. Link berubah sesuai kategori

### 3. Test Tambah Form
1. Menu **Forms** → **Tambah Form**
2. Pilih kategori: **Tata Usaha**
3. Isi title dan URL
4. Klik **Simpan**
5. Buka Google Sheets Tata Usaha → tab `Forms-Tata_usaha`
6. Data harus muncul di baris 2

---

## ❗ Troubleshooting

### Error: "Unable to parse range"
**Penyebab:** Nama tab tidak sesuai
**Solusi:** Pastikan nama tab PERSIS seperti ini:
- `Links-Kesiswaan` (huruf K besar, sisanya kecil)
- `Forms-Kurikulum` (huruf K besar, sisanya kecil)
- `Links-Sapras_humas` (pakai underscore, bukan spasi)
- `Forms-Tata_usaha` (pakai underscore, bukan spasi)

### Error: "The caller does not have permission"
**Penyebab:** Sheets belum di-share
**Solusi:** Share sheets dengan email aplikasi atau set "Anyone with link"

### Error: "Requested entity was not found"
**Penyebab:** Sheets ID salah atau sheet tidak ada
**Solusi:** 
1. Cek ulang Sheets ID di config.php
2. Pastikan Sheets masih ada di Google Drive

### Data tidak muncul saat filter
**Penyebab:** Data ada di tab yang salah
**Solusi:** 
1. Cek tab di Google Sheets
2. Pastikan data ada di tab yang benar (misal: Links-Kesiswaan untuk kategori kesiswaan)

---

## 📊 Struktur Akhir

Setelah setup selesai, struktur Anda akan seperti ini:

```
Google Sheets:
│
├─ Data Kesiswaan - SMKN 62 (ID: xxx111)
│  ├─ Links-Kesiswaan
│  └─ Forms-Kesiswaan
│
├─ Data Kurikulum - SMKN 62 (ID: xxx222)
│  ├─ Links-Kurikulum
│  └─ Forms-Kurikulum
│
├─ Data Sapras & Humas - SMKN 62 (ID: xxx333)
│  ├─ Links-Sapras_humas
│  └─ Forms-Sapras_humas
│
└─ Data Tata Usaha - SMKN 62 (ID: xxx444)
   ├─ Links-Tata_usaha
   └─ Forms-Tata_usaha
```

**ATAU** (jika pakai 1 sheets):

```
Google Sheets:
│
└─ Data Management - SMKN 62 (ID: xxx000)
   ├─ Links-Kesiswaan
   ├─ Forms-Kesiswaan
   ├─ Links-Kurikulum
   ├─ Forms-Kurikulum
   ├─ Links-Sapras_humas
   ├─ Forms-Sapras_humas
   ├─ Links-Tata_usaha
   └─ Forms-Tata_usaha
```

---

## 📝 Catatan Penting

1. **Nama Tab Harus Persis:**
   - Format: `Links-Namaкатегori` atau `Forms-Namaкатегori`
   - Kategori: `Kesiswaan`, `Kurikulum`, `Sapras_humas`, `Tata_usaha`
   - Gunakan underscore `_` untuk spasi, bukan spasi biasa

2. **Header Wajib Ada:**
   - Baris 1 harus berisi: `Title | URL | Created At | Updated At | Category`
   - Data mulai dari baris 2

3. **Backup Berkala:**
   - Google Sheets otomatis save
   - Tapi tetap export backup ke Excel/CSV secara berkala
   - Menu: File → Download → Microsoft Excel (.xlsx)

4. **Permission:**
   - Pastikan semua sheets bisa diakses oleh aplikasi
   - Jangan ubah permission ke "View Only"

---

## 🚀 Ready to Use!

Setelah semua langkah selesai:
- ✅ 4 Google Sheets siap (atau 1 sheets multi-tab)
- ✅ Semua tab sudah dibuat dengan header yang benar
- ✅ Sheets sudah di-share dengan aplikasi
- ✅ Config.php sudah diupdate dengan Sheets ID yang benar

Aplikasi Anda siap digunakan dengan sistem kategori! 🎉

---

**Butuh Bantuan?**
- Buka issue di GitHub: https://github.com/LTZ24/DATA-MANAGEMENT_v2/issues
- Atau hubungi developer

---

**Last Updated:** November 3, 2025
