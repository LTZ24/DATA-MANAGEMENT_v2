# 🔐 Google OAuth Setup & Publishing Guide

Panduan lengkap untuk setup dan publish Google OAuth App tanpa warning "Aplikasi belum diverifikasi"

## 📋 Prerequisites

- Domain sendiri (tidak bisa pakai localhost untuk production)
- Google Cloud Platform account
- Email professional (@yourdomain.com)

---

## 🚀 Step-by-Step Setup

### 1. Buat Project di Google Cloud Console

1. Buka [Google Cloud Console](https://console.cloud.google.com/)
2. Klik dropdown project → **New Project**
3. Isi:
   - **Project Name**: `Data Management System`
   - **Organization**: (optional)
4. Klik **Create**

### 2. Enable APIs

1. Navigation Menu → **APIs & Services** → **Library**
2. Cari dan Enable:
   - ✅ **Google Sheets API**
   - ✅ **Google Drive API**
   - ✅ **Google+ API** (untuk OAuth)

### 3. Configure OAuth Consent Screen

#### A. Basic Information

1. Navigation Menu → **APIs & Services** → **OAuth consent screen**
2. Pilih **External** → **Create**
3. Isi informasi:

```
App Information:
├── App name: Data Management System v2
├── User support email: your-email@yourdomain.com
├── App logo: Upload logo (120x120 pixels, PNG/JPG)
└── Developer contact: your-email@yourdomain.com

App Domain:
├── Application home page: https://yourdomain.com
├── Application privacy policy: https://yourdomain.com/privacy.php
├── Application terms of service: https://yourdomain.com/terms.php
└── Authorized domains: yourdomain.com
```

4. Klik **Save and Continue**

#### B. Scopes (Pilih Yang Minimal!)

**Untuk MENGHINDARI Google Verification, gunakan HANYA Basic Scopes:**

```
✅ .../auth/userinfo.email        (View email address)
✅ .../auth/userinfo.profile      (View profile info)
✅ openid                          (Associate with identity)
```

**❌ JANGAN Tambahkan (butuh verification):**
```
❌ .../auth/drive                  (Full Drive access)
❌ .../auth/spreadsheets           (Full Sheets access)
```

💡 **Alternatif:** Gunakan Service Account untuk Sheets/Drive API (tidak perlu OAuth user)

5. Klik **Save and Continue**

#### C. Test Users (Saat Development)

Tambahkan email untuk testing:
```
Test User 1: user1@gmail.com
Test User 2: user2@gmail.com
```

6. Klik **Save and Continue**

#### D. Summary

Review semua dan klik **Back to Dashboard**

### 4. Create OAuth 2.0 Credentials

1. Navigation Menu → **APIs & Services** → **Credentials**
2. Klik **+ Create Credentials** → **OAuth client ID**
3. Pilih **Web application**
4. Isi:

```
Name: Data Management System Web Client

Authorized JavaScript origins:
├── https://yourdomain.com
└── http://localhost:8080 (untuk development)

Authorized redirect URIs:
├── https://yourdomain.com/auth/callback.php
└── http://localhost/Data-Base-Guru-v2/auth/callback.php
```

5. Klik **Create**
6. Copy **Client ID** dan **Client Secret**
7. Download JSON (optional backup)

### 5. Verifikasi Domain (WAJIB untuk Production)

#### A. Via Google Search Console

1. Buka [Google Search Console](https://search.google.com/search-console)
2. Klik **Add Property** → pilih **Domain**
3. Isi: `yourdomain.com`
4. Pilih metode verifikasi (DNS TXT Record recommended)
5. Tambahkan TXT record ke DNS domain Anda:

```
Type: TXT
Host: @
Value: google-site-verification=xxxxxxxxxxxxx
TTL: 3600
```

6. Klik **Verify**

#### B. Link ke OAuth Consent Screen

1. Kembali ke **OAuth consent screen**
2. Di bagian **Authorized domains**, tambahkan: `yourdomain.com`
3. Domain harus sudah terverifikasi di Search Console
4. Klik **Save**

---

## 🌍 Publishing ke Production

### Option 1: Basic Scopes (RECOMMENDED - No Verification Needed)

Jika hanya pakai scopes:
- `userinfo.email`
- `userinfo.profile`
- `openid`

**Langkah:**

1. OAuth consent screen → Status: **Testing**
2. Klik **Publish App**
3. Konfirmasi **Publish**
4. Status berubah → **In Production**
5. ✅ **WARNING HILANG!** (instant, tanpa review)

### Option 2: Sensitive Scopes (Butuh Verification)

Jika pakai scopes seperti:
- Drive API full access
- Sheets API full access
- Restricted scopes lainnya

**Langkah:**

1. Lengkapi **Verification Requirements**:
   - Privacy Policy URL (wajib)
   - Terms of Service URL (wajib)
   - App homepage (wajib)
   - Video demo aplikasi (wajib)
   - Justifikasi penggunaan scopes
2. Submit untuk **Google Verification**
3. Tunggu review: **2-6 minggu**
4. Bayar security assessment (jika diminta)
5. Setelah approved → Publish

---

## 🔒 Solusi Alternatif: Service Account

**Untuk menghindari OAuth verification, gunakan Service Account!**

### Keuntungan:
- ✅ Tidak perlu OAuth consent screen
- ✅ Tidak ada warning "unverified app"
- ✅ Akses langsung ke Sheets/Drive
- ✅ Tidak perlu user login
- ✅ Setup lebih simple

### Setup Service Account:

1. **Create Service Account:**
   - APIs & Services → Credentials
   - Create Credentials → Service Account
   - Name: `data-management-sa`
   - Role: Editor
   - Download JSON key

2. **Share Google Sheets dengan Service Account:**
   - Buka Google Sheets Anda
   - Share → Paste email service account
   - Format: `xxx@xxx.iam.gserviceaccount.com`
   - Permission: **Editor**

3. **Update Code:**
```php
// Ganti OAuth dengan Service Account
$client = new Google_Client();
$client->setAuthConfig('/path/to/service-account.json');
$client->addScope(Google_Service_Sheets::SPREADSHEETS);
```

---

## 📄 Privacy Policy & Terms of Service

**Wajib untuk production!** File sudah dibuat:
- `privacy.php` - Privacy Policy
- `terms.php` - Terms of Service

Upload ke root domain Anda:
```
https://yourdomain.com/privacy.php
https://yourdomain.com/terms.php
```

---

## ✅ Checklist Final

Sebelum publish, pastikan:

- [ ] Domain sudah diverifikasi di Search Console
- [ ] Authorized domains sudah ditambahkan
- [ ] Privacy Policy & Terms live di domain
- [ ] App logo sudah diupload (120x120px)
- [ ] Redirect URIs sudah benar (https://)
- [ ] Email support profesional (@yourdomain.com)
- [ ] Test login di mode Testing dulu
- [ ] Pilih scopes minimal (jika ingin instant publish)

---

## 🐛 Troubleshooting

### Error: "Unverified App"
**Solusi:**
1. Pastikan status **In Production** (bukan Testing)
2. Jika pakai sensitive scopes → Tunggu Google verification
3. Alternatif: Gunakan Service Account

### Error: "Redirect URI Mismatch"
**Solusi:**
1. Check spelling & case-sensitive
2. Harus exact match (include https://)
3. Jangan ada trailing slash

### Error: "Access Blocked: yourdomain.com has not completed verification"
**Solusi:**
1. Check domain sudah verified di Search Console
2. Domain harus ditambahkan di Authorized Domains
3. Tunggu propagasi DNS (24 jam)

### Error: "Invalid Client"
**Solusi:**
1. Check Client ID & Secret benar
2. Check config.php sudah diupdate
3. Clear browser cache & cookies

---

## 📞 Support

- Google OAuth Documentation: https://developers.google.com/identity/protocols/oauth2
- OAuth Consent Screen: https://support.google.com/cloud/answer/10311615
- Verification Process: https://support.google.com/cloud/answer/9110914

---

**Created by LTZ24** | v2.1 | 2025-10-25
