# Token Update Guide - Google OAuth

## 📌 Problem: Insufficient Authentication Scopes

Jika Anda mendapat error:
```
{
  "error": {
    "code": 403,
    "message": "Request had insufficient authentication scopes.",
    "status": "PERMISSION_DENIED"
  }
}
```

Ini berarti **scope OAuth kurang lengkap**, bukan token habis.

---

## ✅ Solusi 1: Logout & Login Ulang (Recommended)

Cara tercepat untuk mendapatkan token baru dengan scope yang benar:

### Langkah-langkah:

1. **Logout dari aplikasi**
   - Klik tombol Logout di dashboard
   - Atau akses: `http://localhost/Data-Base-Guru-v2/auth/logout.php`

2. **Hapus token lama** (opsional, tapi disarankan)
   ```
   Hapus file: data/token.json
   ```

3. **Login kembali**
   - Akses: `http://localhost/Data-Base-Guru-v2/auth/login.php`
   - Klik "Masuk dengan Google"
   - **PENTING**: Anda akan melihat permintaan izin BARU:
     - ✅ "See, edit, create, and delete all of your Google Drive files"
     - ✅ "See, edit, create, and delete all your Google Sheets spreadsheets"
     - ✅ "See your personal info, including email and profile"

4. **Klik "Allow"**
   - Token baru dengan scope lengkap akan tersimpan
   - Sekarang Anda bisa upload file ke Google Drive

---

## ✅ Solusi 2: Revoke Access & Re-authorize

Jika Solusi 1 tidak berhasil, lakukan ini:

### 1. Revoke akses aplikasi di Google

- Buka: https://myaccount.google.com/permissions
- Cari aplikasi "Database Guru SMKN 62 Jakarta" (atau nama app Anda)
- Klik **"Remove Access"** / **"Hapus Akses"**

### 2. Hapus token lokal

Hapus file token di server:
```bash
# Di Windows
del data\token.json

# Di Linux/Mac
rm data/token.json
```

### 3. Login ulang

- Akses aplikasi lagi
- Login dengan Google
- Izinkan semua permission yang diminta

---

## 🔍 Perubahan Scope

Sebelumnya aplikasi menggunakan scope terbatas:
```php
$client->addScope(Google_Service_Drive::DRIVE_FILE); // ❌ Hanya file yang dibuat app
```

Sekarang menggunakan scope penuh:
```php
$client->addScope(Google_Service_Drive::DRIVE); // ✅ Akses penuh Google Drive
$client->addScope(Google_Service_Sheets::SPREADSHEETS); // ✅ Akses penuh Sheets
```

---

## 📊 Perbedaan Scope

| Scope | Akses | Keterangan |
|-------|-------|------------|
| `DRIVE_FILE` | Terbatas | Hanya bisa akses file yang dibuat oleh aplikasi ini |
| `DRIVE` | Penuh | Bisa akses, create, edit, delete semua file di Drive |
| `SPREADSHEETS` | Penuh | Bisa read/write semua Google Sheets |

---

## ⚙️ Update Google Cloud Console (Opsional)

Jika Anda ingin update OAuth Consent Screen:

1. Buka: https://console.cloud.google.com/
2. Pilih project Anda
3. Ke **APIs & Services** > **OAuth consent screen**
4. Pastikan **Scopes** include:
   - `https://www.googleapis.com/auth/drive`
   - `https://www.googleapis.com/auth/spreadsheets`
   - `https://www.googleapis.com/auth/userinfo.email`
   - `https://www.googleapis.com/auth/userinfo.profile`

---

## 🔐 Keamanan Token

Token disimpan di:
- **Session**: `$_SESSION['access_token']`
- **File**: `data/token.json` (jika menggunakan file storage)

**Penting**:
- Token ter-encrypt otomatis oleh Google OAuth
- Token refresh otomatis jika expired (dalam 1 jam)
- Jika refresh token tidak ada, user harus login ulang

---

## 🛠️ Troubleshooting

### Problem: Masih error setelah login ulang

**Solusi**:
1. Clear browser cache & cookies
2. Buka aplikasi dalam Incognito/Private mode
3. Login ulang

### Problem: "Error retrieving access token"

**Solusi**:
1. Cek `GOOGLE_CLIENT_ID` dan `GOOGLE_CLIENT_SECRET` di `includes/config.php`
2. Pastikan `GOOGLE_REDIRECT_URI` sesuai dengan yang di Google Cloud Console
3. Pastikan callback.php tidak ada error

### Problem: Token expired terus-menerus

**Solusi**:
1. Pastikan `setAccessType('offline')` di config.php
2. Pastikan `setPrompt('select_account consent')` aktif
3. Ini akan meminta refresh token yang tidak expired

---

## 📝 Cara Cek Token Saat Ini

Buat file `check_token.php` di root:

```php
<?php
session_start();

if (isset($_SESSION['access_token'])) {
    echo "<h2>Token Info:</h2>";
    echo "<pre>";
    print_r($_SESSION['access_token']);
    echo "</pre>";
    
    // Check expiry
    $token = $_SESSION['access_token'];
    if (isset($token['expires_in'])) {
        $expires = time() + $token['expires_in'];
        echo "<p>Token expires: " . date('Y-m-d H:i:s', $expires) . "</p>";
    }
} else {
    echo "<p>No token found. Please login.</p>";
}
?>
```

Akses: `http://localhost/Data-Base-Guru-v2/check_token.php`

**JANGAN lupa hapus file ini setelah testing!**

---

## 🔄 Auto Refresh Token

Code di `includes/config.php` sudah handle auto refresh:

```php
// Refresh the token if it's expired
if ($client->isAccessTokenExpired()) {
    if ($client->getRefreshToken()) {
        $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
        $_SESSION['access_token'] = $client->getAccessToken();
    }
}
```

Jika refresh token tidak ada, user akan otomatis logout dan diminta login ulang.

---

## ✅ Checklist

Setelah update, pastikan:

- [ ] Sudah logout dari aplikasi
- [ ] Hapus `data/token.json` (jika ada)
- [ ] Login ulang dengan Google
- [ ] Melihat permintaan permission baru (Drive + Sheets)
- [ ] Klik "Allow" untuk semua permission
- [ ] Test upload file ke Google Drive
- [ ] Test read/write Google Sheets
- [ ] Tidak ada error 403 lagi

---

## 📚 References

- [Google OAuth 2.0 Scopes](https://developers.google.com/identity/protocols/oauth2/scopes)
- [Google Drive API](https://developers.google.com/drive/api/v3/about-sdk)
- [Google Sheets API](https://developers.google.com/sheets/api)

---

**Last Updated**: November 3, 2025
