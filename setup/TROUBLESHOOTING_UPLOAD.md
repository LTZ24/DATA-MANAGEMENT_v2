````markdown
# Troubleshooting Upload Link/Form Error

## ❗ Error yang Mungkin Terjadi

### 1. "Unable to parse range: Links-xxx"

**Penyebab:** Nama tab di Google Sheets tidak sesuai

**Solusi:** Pastikan nama tab di Google Sheets **PERSIS** seperti ini:

| Kategori | Nama Tab Links | Nama Tab Forms |
|----------|----------------|----------------|
| Kesiswaan | `Links-Kesiswaan` | `Forms-Kesiswaan` |
| Kurikulum | `Links-Kurikulum` | `Forms-Kurikulum` |
| Sapras & Humas | `Links-Sapras_humas` | `Forms-Sapras_humas` |
| Tata Usaha | `Links-Tata_usaha` | `Forms-Tata_usaha` |

**Penting:**
- Huruf pertama **KAPITAL** (K, K, S, T)
- Gunakan **underscore `_`** untuk Sapras_humas dan Tata_usaha (BUKAN spasi)
- Huruf selain huruf pertama **lowercase** (kecil semua)

---

### 2. "The caller does not have permission"

**Penyebab:** Google Sheets belum di-share dengan aplikasi

**Solusi:**
1. Buka Google Sheets yang error
2. Klik tombol **Share** (kanan atas)
3. Pilih salah satu:
   - **Option A:** Masukkan email Google yang digunakan login aplikasi → Set ke **Editor**
   - **Option B:** Klik "Change to anyone with the link" → Set ke **Editor**
4. Klik **Done**
5. Coba upload lagi

---

### 3. "Requested entity was not found"

**Penyebab:** Sheets ID salah atau sheets tidak ada

**Solusi:**
1. Buka Google Sheets yang ingin digunakan
2. Copy ID dari URL (bagian antara `/d/` dan `/edit`)
   ```
   https://docs.google.com/spreadsheets/d/[INI_ID_NYA]/edit
   ```
3. Paste ke `includes/config.php`:
   ```php
   define('SHEETS_KESISWAAN', 'PASTE_ID_DISINI');
   ```
4. Save dan coba lagi

---

### 4. "Invalid category"

**Penyebab:** Kategori yang dipilih tidak valid

**Solusi:**
1. Pastikan sudah pilih kategori dari dropdown
2. Jangan submit form kosong
3. Kategori valid: kesiswaan, kurikulum, sapras_humas, tata_usaha

---

## 🔍 Cara Debug Error

### Step 1: Cek PHP Error Log

Buka file error log PHP di:
```
C:\xampp\apache\logs\error.log
```

Cari baris terakhir yang ada kata "Error" atau "Exception"

### Step 2: Enable Error Display

Edit `includes/config.php`, pastikan ada:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Step 3: Cek Browser Console

1. Tekan **F12** di browser
2. Klik tab **Console**
3. Coba upload lagi
4. Lihat apakah ada error merah

### Step 4: Test Manual di Google Sheets

1. Buka Google Sheets yang error
2. Pastikan ada tab dengan nama yang benar (misal: `Links-Kesiswaan`)
3. Cek header di baris 1: `Title | URL | Created At | Updated At | Category`
4. Coba tambah data manual di baris 2
5. Jika berhasil manual, berarti masalah di kode

---

## 🛠️ Quick Fix Checklist

Sebelum upload, pastikan:

- [ ] Sudah logout dan login ulang (agar token OAuth refresh)
- [ ] Sheets ID sudah benar di `includes/config.php`
- [ ] Nama tab di Google Sheets sudah benar (huruf besar/kecil, underscore)
- [ ] Google Sheets sudah di-share dengan email yang login
- [ ] Header sudah ada di baris 1 setiap tab
- [ ] Pilih kategori dari dropdown sebelum submit
- [ ] URL link/form dimulai dengan `http://` atau `https://`

---

## 📝 Contoh Format yang Benar

### Config.php
```php
define('SHEETS_KESISWAAN', '16pQfnsMBFJhX-3VIgXnTcQfANeMA9EH-RHL-LTS7UO4');
define('SHEETS_KURIKULUM', '1F5DM6-nr3ho8JgQJWbBUEc2SOPHYr2yl8FbyfG4Tyiw');
define('SHEETS_SAPRAS_HUMAS', '1nUyko17YIFsISNBZzJetHwVaeNzNfMNof-qavwqa7TQ');
define('SHEETS_TATA_USAHA', '1Uih6aeWp5Ex0M4MmUZ97Xw3GftI34v3wIOfgvyDehuI');
```

### Nama Tab di Google Sheets
```
✅ Links-Kesiswaan
✅ Forms-Kesiswaan
✅ Links-Kurikulum
✅ Forms-Kurikulum
✅ Links-Sapras_humas    ← pakai underscore
✅ Forms-Sapras_humas    ← pakai underscore
✅ Links-Tata_usaha      ← pakai underscore
✅ Forms-Tata_usaha      ← pakai underscore

❌ Links-kesiswaan       ← huruf k kecil (salah)
❌ Links-KESISWAAN       ← semua kapital (salah)
❌ Links-Sapras Humas    ← pakai spasi (salah)
❌ Links-sapras_humas    ← huruf s kecil (salah)
```

---

## 🚨 Error Paling Sering

**"Unable to parse range: Links-Sapras_humas"**

Ini biasanya karena nama tab salah. Pastikan:
1. Tab bernama **persis** `Links-Sapras_humas`
2. Huruf **S** besar, sisanya kecil
3. Gunakan **underscore `_`** bukan spasi atau dash `-`

---

Kalau masih error, screenshot dan share ke GitHub issue! 🙏

````
