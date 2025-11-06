# Category System - Links & Forms Management

## 📋 Overview

Sistem manajemen Links dan Forms telah diupgrade dengan **Category System** yang memisahkan data berdasarkan kategori organisasi sekolah. Setiap kategori memiliki Google Sheets ID sendiri untuk memudahkan pengelolaan.

---

## 🗂️ Kategori

### 1. **Kesiswaan** (Students Affairs)
- **Icon**: `fa-users`
- **Color**: `#50e3c2` (Tosca/Green)
- **Sheet ID**: Didefinisikan di `LINK_SHEET_KESISWAAN` & `FORM_SHEET_KESISWAAN`
- **Untuk**: Data terkait siswa, absensi, prestasi, dll

### 2. **Kurikulum** (Curriculum)
- **Icon**: `fa-book`
- **Color**: `#4a90e2` (Blue)
- **Sheet ID**: Didefinisikan di `LINK_SHEET_KURIKULUM` & `FORM_SHEET_KURIKULUM`
- **Untuk**: Materi pelajaran, RPP, silabus, jadwal, dll

### 3. **Sapras & Humas** (Facilities & Public Relations)
- **Icon**: `fa-building`
- **Color**: `#f39c12` (Orange)
- **Sheet ID**: Didefinisikan di `LINK_SHEET_SAPRAS_HUMAS` & `FORM_SHEET_SAPRAS_HUMAS`
- **Untuk**: Fasilitas sekolah, hubungan masyarakat, dll

### 4. **Tata Usaha** (Administration)
- **Icon**: `fa-file-invoice`
- **Color**: `#e74c3c` (Red)
- **Sheet ID**: Didefinisikan di `LINK_SHEET_TATA_USAHA` & `FORM_SHEET_TATA_USAHA`
- **Untuk**: Administrasi, keuangan, surat-menyurat, dll

---

## ⚙️ Konfigurasi

### File: `includes/config.php`

```php
// Google Sheets IDs for Link Categories
define('LINK_SHEET_KESISWAAN', 'your-kesiswaan-sheet-id');
define('LINK_SHEET_KURIKULUM', 'your-kurikulum-sheet-id');
define('LINK_SHEET_SAPRAS_HUMAS', 'your-sapras-humas-sheet-id');
define('LINK_SHEET_TATA_USAHA', 'your-tata-usaha-sheet-id');

// Google Sheets IDs for Form Categories
define('FORM_SHEET_KESISWAAN', 'your-kesiswaan-form-sheet-id');
define('FORM_SHEET_KURIKULUM', 'your-kurikulum-form-sheet-id');
define('FORM_SHEET_SAPRAS_HUMAS', 'your-sapras-humas-form-sheet-id');
define('FORM_SHEET_TATA_USAHA', 'your-tata-usaha-form-sheet-id');
```

### Template: `includes/config_ex.php`

File template sudah diupdate. Copy ke `config.php` dan isi dengan Sheet ID yang sesuai.

---

## 🔧 Cara Setup Google Sheets

### 1. Buat Google Sheets untuk Links

Untuk setiap kategori, buat 1 Google Sheet:

**A. Links - Kesiswaan**
- Buka: https://sheets.google.com/
- Create New Spreadsheet
- Rename: "Links - Kesiswaan"
- Setup header (baris pertama):
  ```
  A1: Title
  B1: URL
  C1: Created At
  D1: Updated At
  ```
- Copy Sheet ID dari URL:
  ```
  https://docs.google.com/spreadsheets/d/[SHEET_ID]/edit
  ```
- Paste ke `LINK_SHEET_KESISWAAN` di config.php

**B. Ulangi untuk kategori lain:**
- Links - Kurikulum → `LINK_SHEET_KURIKULUM`
- Links - Sapras & Humas → `LINK_SHEET_SAPRAS_HUMAS`
- Links - Tata Usaha → `LINK_SHEET_TATA_USAHA`

### 2. Buat Google Sheets untuk Forms

Ulangi langkah yang sama untuk Forms:
- Forms - Kesiswaan → `FORM_SHEET_KESISWAAN`
- Forms - Kurikulum → `FORM_SHEET_KURIKULUM`
- Forms - Sapras & Humas → `FORM_SHEET_SAPRAS_HUMAS`
- Forms - Tata Usaha → `FORM_SHEET_TATA_USAHA`

### 3. Share Sheets dengan Service Account

Jika menggunakan Service Account:
1. Buka setiap Google Sheet
2. Klik **Share**
3. Tambahkan email Service Account
4. Berikan akses **Editor**

---

## 📝 Struktur Data Google Sheets

### Links Sheet Structure

| Column | Field | Type | Description |
|--------|-------|------|-------------|
| A | Title | String | Nama/judul link |
| B | URL | URL | Link lengkap dengan http/https |
| C | Created At | DateTime | Tanggal dibuat (YYYY-MM-DD HH:MM:SS) |
| D | Updated At | DateTime | Tanggal diupdate (YYYY-MM-DD HH:MM:SS) |

**Contoh Data:**
```
Title                 | URL                                    | Created At          | Updated At
Google Drive Siswa    | https://drive.google.com/...          | 2025-11-03 10:30:00 | 
Absensi Online        | https://forms.gle/...                 | 2025-11-03 10:35:00 |
```

### Forms Sheet Structure

Sama dengan Links Sheet:

| Column | Field | Type | Description |
|--------|-------|------|-------------|
| A | Title | String | Nama form |
| B | URL | URL | Link Google Form |
| C | Created At | DateTime | Tanggal dibuat |
| D | Updated At | DateTime | Tanggal diupdate |

---

## 🎨 UI Features

### 1. Category Filter

**Tampilan:**
- Tombol filter horizontal di atas tabel
- Button "Semua Kategori" untuk menampilkan semua
- Button per kategori dengan icon dan warna khas

**Fungsi:**
- Klik kategori → Hanya tampilkan data kategori tersebut
- Klik "Semua Kategori" → Tampilkan semua data dari semua kategori

### 2. Category Badge

**Tampilan:**
- Badge berwarna di atas setiap item
- Menampilkan icon dan nama kategori
- Warna sesuai dengan kategori

**Contoh:**
```
[👥 Kesiswaan]  → Hijau #50e3c2
[📚 Kurikulum]  → Biru #4a90e2
[🏢 Sapras & Humas] → Orange #f39c12
[📄 Tata Usaha] → Merah #e74c3c
```

### 3. Form Input

**Tampilan:**
- Dropdown select kategori di form tambah/edit
- Wajib pilih kategori sebelum submit
- Dropdown menampilkan icon dan nama kategori

---

## 🔄 Workflow

### A. Tambah Link/Form Baru

1. User klik "Tambah Link" / "Tambah Form"
2. Pilih **Kategori** dari dropdown
3. Isi Title dan URL
4. Klik "Simpan"
5. Data tersimpan di Google Sheet sesuai kategori
6. User di-redirect ke halaman index dengan filter kategori aktif

### B. Edit Link/Form

1. User klik tombol Edit
2. System load data dari Sheet sesuai kategori
3. User bisa ubah **Kategori** (akan pindah sheet)
4. Jika kategori berubah:
   - Hapus dari sheet lama
   - Tambah ke sheet baru
5. Jika kategori sama:
   - Update data di sheet yang sama

### C. Hapus Link/Form

1. User klik tombol Hapus
2. Konfirmasi hapus
3. Data dihapus dari Google Sheet sesuai kategori
4. User tetap di filter kategori yang sama

### D. Filter by Kategori

1. User klik button kategori
2. URL berubah: `index.php?category=kesiswaan`
3. System hanya load data dari Sheet kategori tersebut
4. Tampilkan hasil filter

---

## 🛠️ Helper Functions

### File: `includes/config.php`

#### 1. `getLinkCategories()`

```php
$categories = getLinkCategories();
// Returns:
// [
//   'kesiswaan' => [
//     'name' => 'Kesiswaan',
//     'icon' => 'fa-users',
//     'color' => '#50e3c2',
//     'sheet_id' => 'xxx'
//   ],
//   ...
// ]
```

#### 2. `getFormCategories()`

Sama seperti `getLinkCategories()` tapi untuk Forms.

#### 3. `getLinksFromSheets($category)`

```php
// Get all links from Kesiswaan
$links = getLinksFromSheets('kesiswaan');

// Get all links (jika tidak ada parameter)
$allLinks = getLinksFromSheets();
```

#### 4. `addLinkToSheets($title, $url, $category)`

```php
$success = addLinkToSheets(
    'Google Drive Siswa',
    'https://drive.google.com/...',
    'kesiswaan'
);
```

#### 5. `updateLinkInSheets($id, $title, $url, $category)`

```php
$success = updateLinkInSheets(
    0,  // Row index
    'Google Drive Siswa Updated',
    'https://drive.google.com/new',
    'kesiswaan'
);
```

#### 6. `deleteLinkFromSheets($id, $category)`

```php
$success = deleteLinkFromSheets(0, 'kesiswaan');
```

---

## 📊 Database Schema (Google Sheets)

### Jumlah Sheets yang Diperlukan

**Total: 8 Google Sheets**

#### Links (4 Sheets):
1. Links - Kesiswaan
2. Links - Kurikulum
3. Links - Sapras & Humas
4. Links - Tata Usaha

#### Forms (4 Sheets):
1. Forms - Kesiswaan
2. Forms - Kurikulum
3. Forms - Sapras & Humas
4. Forms - Tata Usaha

### Permission

Semua sheets harus:
- ✅ Accessible oleh Service Account (jika pakai)
- ✅ Atau accessible oleh Google Account yang digunakan OAuth
- ✅ Permission minimal: **Editor**

---

## 🚀 Migration dari System Lama

Jika Anda sudah punya data di sistem lama (1 sheet untuk semua):

### Langkah Migration:

1. **Backup data lama**
   ```
   File → Download → CSV
   ```

2. **Buat 4 sheets baru** (untuk Links)

3. **Copy header** ke setiap sheet baru:
   ```
   Title | URL | Created At | Updated At
   ```

4. **Pisahkan data** berdasarkan kategori:
   - Filter/sort data lama by kategori
   - Copy-paste ke sheet baru sesuai kategori

5. **Update config.php** dengan Sheet ID baru

6. **Test** setiap kategori

---

## 🎯 Benefits

### 1. **Organized Data**
- Data terpisah per departemen
- Mudah maintain
- Tidak campur aduk

### 2. **Better Performance**
- Query lebih cepat (sheet lebih kecil)
- Load hanya data yang diperlukan
- Reduce API calls ke Google

### 3. **Access Control** (Future)
- Bisa set permission per sheet
- Kesiswaan hanya akses sheet Kesiswaan
- Admin akses semua

### 4. **Scalability**
- Mudah tambah kategori baru
- Tinggal tambah sheet + config
- Tidak perlu ubah banyak code

---

## 🔍 Troubleshooting

### Problem: "Kategori tidak valid"

**Solusi:**
1. Cek `config.php` - pastikan semua `LINK_SHEET_*` dan `FORM_SHEET_*` terisi
2. Cek Sheet ID valid (bukan URL lengkap)
3. Cek permission sheet (harus Editor)

### Problem: Data tidak muncul setelah filter

**Solusi:**
1. Cek Sheet ID kategori sudah benar
2. Cek ada data di sheet tersebut
3. Cek format header sheet: `Title | URL | Created At | Updated At`

### Problem: Error saat tambah/edit

**Solusi:**
1. Cek OAuth scope sudah `DRIVE` bukan `DRIVE_FILE`
2. Logout dan login ulang
3. Cek permission sheet (Editor)

### Problem: Edit kategori tidak pindah sheet

**Solusi:**
1. Cek function `deleteLinkFromSheets($id, $oldCategory)` dipanggil
2. Cek function `addLinkToSheets($title, $url, $newCategory)` dipanggil
3. Cek tidak ada error di kedua function

---

## 📚 References

- [Google Sheets API](https://developers.google.com/sheets/api)
- [PHP Google API Client](https://github.com/googleapis/google-api-php-client)

---

## ✅ Checklist Setup

- [ ] Buat 4 Google Sheets untuk Links
- [ ] Buat 4 Google Sheets untuk Forms
- [ ] Setup header di setiap sheet
- [ ] Copy Sheet IDs
- [ ] Update `config.php` dengan semua Sheet IDs
- [ ] Share sheets dengan Service Account / OAuth account
- [ ] Test tambah link/form per kategori
- [ ] Test filter by kategori
- [ ] Test edit link/form (same category)
- [ ] Test edit link/form (change category)
- [ ] Test delete link/form

---

**Last Updated**: November 3, 2025  
**Version**: 2.0
