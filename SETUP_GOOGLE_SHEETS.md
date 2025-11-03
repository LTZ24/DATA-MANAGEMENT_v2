# Setup Google Sheets - Category System

Panduan lengkap untuk membuat Google Sheets sesuai dengan sistem kategori.

---

## 📋 Struktur Sheets yang Dibutuhkan

Aplikasi ini menggunakan **4 kategori** dengan masing-masing punya Google Sheets sendiri:

1. **Kesiswaan** - Data terkait siswa
2. **Kurikulum** - Data pembelajaran dan kurikulum
3. **Sapras & Humas** - Sarana prasarana dan hubungan masyarakat
4. **Tata Usaha** - Administrasi dan tata usaha

---

## 🔧 Langkah 1: Buat 4 Google Sheets

### 1. Buat Google Sheets untuk KESISWAAN

1. Buka: https://sheets.google.com
2. Klik **+ Blank** (Buat spreadsheet baru)
3. Rename menjadi: **"Database Kesiswaan - SMKN 62"**
4. Copy **Spreadsheet ID** dari URL:
   ```
   https://docs.google.com/spreadsheets/d/[SPREADSHEET_ID]/edit
   ```
   Contoh: `1aBcDeFgHiJkLmNoPqRsTuVwXyZ123456789`

5. Buat **2 sheet** di dalam spreadsheet ini:
   - Sheet 1: Rename ke **"Links-Kesiswaan"**
   - Sheet 2: Rename ke **"Forms-Kesiswaan"**

6. **Setup Headers untuk Links-Kesiswaan**:
   - Cell A1: `Title`
   - Cell B1: `URL`
   - Cell C1: `Created At`
   - Cell D1: `Updated At`
   
7. **Setup Headers untuk Forms-Kesiswaan**:
   - Cell A1: `Title`
   - Cell B1: `URL`
   - Cell C1: `Created At`
   - Cell D1: `Updated At`

### 2. Buat Google Sheets untuk KURIKULUM

1. Buat spreadsheet baru: **"Database Kurikulum - SMKN 62"**
2. Copy Spreadsheet ID
3. Buat 2 sheet:
   - **"Links-Kurikulum"**
   - **"Forms-Kurikulum"**
4. Setup headers yang sama (Title, URL, Created At, Updated At)

### 3. Buat Google Sheets untuk SAPRAS & HUMAS

1. Buat spreadsheet baru: **"Database Sapras Humas - SMKN 62"**
2. Copy Spreadsheet ID
3. Buat 2 sheet:
   - **"Links-Sapras_humas"**
   - **"Forms-Sapras_humas"**
4. Setup headers yang sama

### 4. Buat Google Sheets untuk TATA USAHA

1. Buat spreadsheet baru: **"Database Tata Usaha - SMKN 62"**
2. Copy Spreadsheet ID
3. Buat 2 sheet:
   - **"Links-Tata_usaha"**
   - **"Forms-Tata_usaha"**
4. Setup headers yang sama

---

## 🔑 Langkah 2: Update Config dengan Sheets IDs

Edit file `includes/config.php` dan isi Sheets ID yang sudah Anda copy:

```php
// Google Sheets IDs per Category
define('SHEETS_ID_KESISWAAN', 'YOUR_KESISWAAN_SHEETS_ID');
define('SHEETS_ID_KURIKULUM', 'YOUR_KURIKULUM_SHEETS_ID');
define('SHEETS_ID_SAPRAS_HUMAS', 'YOUR_SAPRAS_HUMAS_SHEETS_ID');
define('SHEETS_ID_TATA_USAHA', 'YOUR_TATA_USAHA_SHEETS_ID');
```

**Contoh:**
```php
define('SHEETS_ID_KESISWAAN', '1aBcDeFgHiJkLmNoPqRsTuVwXyZ123456789');
define('SHEETS_ID_KURIKULUM', '1zYxWvUtSrQpOnMlKjIhGfEdCbA987654321');
define('SHEETS_ID_SAPRAS_HUMAS', '1qWeRtYuIoPaSdFgHjKlZxCvBnMqWeRtYuI');
define('SHEETS_ID_TATA_USAHA', '1AsDfGhJkLzXcVbNmQwErTyUiOpLkJhGfDs');
```

---

## 📊 Langkah 3: Template Quick Copy

Untuk mempermudah, Anda bisa **copy template** ini:

### Template 1: Copy Spreadsheet Structure

Buat 1 spreadsheet dulu dengan struktur lengkap, lalu:
1. Klik **File** > **Make a copy**
2. Rename sesuai kategori
3. Ulangi 4x untuk 4 kategori

### Template 2: Gunakan Google Apps Script

Buka Google Sheets > **Extensions** > **Apps Script**, paste code ini:

```javascript
function createAllSheets() {
  var categories = [
    'Kesiswaan',
    'Kurikulum', 
    'Sapras Humas',
    'Tata Usaha'
  ];
  
  var createdIds = {};
  
  categories.forEach(function(category) {
    // Create new spreadsheet
    var ss = SpreadsheetApp.create('Database ' + category + ' - SMKN 62');
    
    // Rename first sheet
    var sheet1 = ss.getSheets()[0];
    sheet1.setName('Links-' + category.replace(/ /g, '_').toLowerCase());
    
    // Add headers
    sheet1.getRange('A1:D1').setValues([['Title', 'URL', 'Created At', 'Updated At']]);
    sheet1.getRange('A1:D1').setFontWeight('bold');
    
    // Create second sheet
    var sheet2 = ss.insertSheet('Forms-' + category.replace(/ /g, '_').toLowerCase());
    sheet2.getRange('A1:D1').setValues([['Title', 'URL', 'Created At', 'Updated At']]);
    sheet2.getRange('A1:D1').setFontWeight('bold');
    
    // Save ID
    createdIds[category] = ss.getId();
    Logger.log(category + ': ' + ss.getId());
  });
  
  return createdIds;
}
```

Klik **Run** > `createAllSheets`

Script akan otomatis:
- Buat 4 spreadsheets
- Tambah 2 sheets di masing-masing
- Setup headers
- Print Sheets IDs di log

---

## ✅ Langkah 4: Test Connection

Setelah setup, test koneksi:

1. Login ke aplikasi
2. Buka menu **Links** atau **Forms**
3. Pilih kategori dari dropdown
4. Coba **Tambah Link** baru
5. Cek apakah data masuk ke Google Sheets yang sesuai

---

## 🎨 Langkah 5: Format Sheets (Opsional)

Untuk mempercantik Google Sheets:

### Format Headers
1. Select row 1 (headers)
2. **Format** > **Text** > **Bold**
3. **Format** > **Fill color** > Pilih warna (misal: biru muda)
4. **Format** > **Align** > **Center**

### Auto-resize Columns
1. Select all columns (A-D)
2. Double-click border antara column untuk auto-fit

### Freeze Header Row
1. Select row 1
2. **View** > **Freeze** > **1 row**

### Add Data Validation (Opsional)
Untuk kolom URL, tambahkan validation:
1. Select column B (URL column)
2. **Data** > **Data validation**
3. Criteria: **URL**
4. Reject input jika bukan URL valid

---

## 🔒 Langkah 6: Set Permissions

**PENTING**: Pastikan service account punya akses ke semua Sheets!

### Option 1: Share ke Email Service Account

1. Buka Google Sheets
2. Klik **Share**
3. Tambahkan email service account dari `credentials.json`
4. Set permission: **Editor**
5. Ulangi untuk 4 spreadsheets

### Option 2: Make Sheets Public (Not Recommended)

1. Klik **Share**
2. Change to **Anyone with the link**
3. Set permission: **Editor**

---

## 📝 Naming Convention

### Sheets Names (Harus sesuai!)

Format: `[Type]-[Category]`

**Links:**
- `Links-Kesiswaan`
- `Links-Kurikulum`
- `Links-Sapras_humas` ← underscore!
- `Links-Tata_usaha` ← underscore!

**Forms:**
- `Forms-Kesiswaan`
- `Forms-Kurikulum`
- `Forms-Sapras_humas` ← underscore!
- `Forms-Tata_usaha` ← underscore!

⚠️ **Perhatikan**: Sapras & Humas dan Tata Usaha menggunakan **underscore** `_` bukan spasi!

---

## 🧪 Test Checklist

Setelah setup selesai, test ini:

- [ ] Login ke aplikasi berhasil
- [ ] Menu Links bisa dibuka
- [ ] Dropdown kategori muncul dengan 4 pilihan
- [ ] Bisa tambah link di kategori Kesiswaan
- [ ] Data muncul di Google Sheets yang benar
- [ ] Bisa filter by kategori
- [ ] Bisa edit link
- [ ] Bisa delete link
- [ ] Ulangi test untuk Forms
- [ ] Ulangi test untuk semua 4 kategori

---

## ❌ Troubleshooting

### Error: "Sheet not found"

**Penyebab**: Nama sheet salah atau typo

**Solusi**: 
1. Cek nama sheet PERSIS sama dengan convention
2. Pastikan tidak ada spasi berlebih
3. Sapras & Humas harus `Sapras_humas` (underscore)

### Error: "Permission denied"

**Penyebab**: Service account tidak punya akses

**Solusi**:
1. Share spreadsheet ke email service account
2. Set permission: Editor
3. Atau gunakan OAuth dengan scope DRIVE penuh

### Error: "Spreadsheet not found"

**Penyebab**: Sheets ID salah di config.php

**Solusi**:
1. Buka Google Sheets
2. Copy ID dari URL lagi
3. Paste ke config.php dengan benar

### Data tidak muncul

**Penyebab**: Headers tidak sesuai atau data di row 1

**Solusi**:
1. Pastikan headers di row 1: Title | URL | Created At | Updated At
2. Data harus mulai dari row 2
3. Jangan ada row kosong

---

## 📚 Structure Summary

```
Google Drive
│
├── Database Kesiswaan - SMKN 62 (Spreadsheet)
│   ├── Links-Kesiswaan (Sheet)
│   │   └── Headers: Title | URL | Created At | Updated At
│   └── Forms-Kesiswaan (Sheet)
│       └── Headers: Title | URL | Created At | Updated At
│
├── Database Kurikulum - SMKN 62 (Spreadsheet)
│   ├── Links-Kurikulum (Sheet)
│   └── Forms-Kurikulum (Sheet)
│
├── Database Sapras Humas - SMKN 62 (Spreadsheet)
│   ├── Links-Sapras_humas (Sheet)
│   └── Forms-Sapras_humas (Sheet)
│
└── Database Tata Usaha - SMKN 62 (Spreadsheet)
    ├── Links-Tata_usaha (Sheet)
    └── Forms-Tata_usaha (Sheet)
```

---

## 🎯 Next Steps

Setelah setup Google Sheets selesai:

1. ✅ Test semua fitur CRUD (Create, Read, Update, Delete)
2. ✅ Import data existing (jika ada)
3. ✅ Setup backup otomatis (Google Drive Versioning)
4. ✅ Train admin untuk manage data
5. ✅ Deploy ke production (digitechid.me)

---

**Last Updated**: November 3, 2025
