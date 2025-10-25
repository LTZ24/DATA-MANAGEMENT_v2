# Data Management System v2

Sistem Manajemen Data dengan integrasi Google Sheets & Google Drive API.

## 📋 Fitur Utama

- ✅ Manajemen Links (URL Shortener dengan QR Code)
- ✅ Manajemen Forms (Google Forms Integration)
- ✅ File Manager (Google Drive Integration)
- ✅ Sistem Login & Autentikasi dengan Google OAuth 2.0
- ✅ Dashboard dengan statistik real-time
- ✅ Multi-bahasa (Indonesia & English) dengan i18n
- ✅ Auto Logout setelah 30 menit inaktif
- ✅ Responsive Design dengan sidebar collapsible
- ✅ Dark Mode Support (Coming Soon)

## 🛠️ Teknologi

- **Backend**: PHP 8.0+
- **Database**: Google Sheets API (Cloud-based)
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla JS)
- **API Integration**: 
  - Google API Client PHP
  - Google Sheets API v4
  - Google Drive API v3
  - Google OAuth 2.0
  - Font Awesome 6.0 Icons
- **Features**:
  - i18n (Internationalization)
  - Auto Logout System
  - QR Code Generation
  - AJAX Operations

## 📦 Instalasi

### 1. Clone atau Download Project

```bash
git clone https://github.com/LTZ24/DATA-MANAGEMENT_v2.git
cd DATA-MANAGEMENT_v2
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Konfigurasi Google API

1. Buat project di [Google Cloud Console](https://console.cloud.google.com/)
2. Enable Google Sheets API & Google Drive API
3. Buat OAuth 2.0 credentials
4. Download `credentials.json` dan simpan di folder `data/`
5. Copy file `includes/config_ex.php` menjadi `includes/config.php`:

```bash
cp includes/config_ex.php includes/config.php
```

6. Edit `includes/config.php` dan isi kredensial Google API:

```php
// Google API Configuration
define('GOOGLE_CLIENT_ID', 'YOUR_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI', 'http://localhost/Data-Base-Guru-v2/auth/google-callback.php');

// Google Sheets ID
define('LINKS_SPREADSHEET_ID', 'YOUR_LINKS_SHEET_ID');
define('FORMS_SPREADSHEET_ID', 'YOUR_FORMS_SHEET_ID');
```

### 4. Set Permission Folder

Pastikan folder `data/` memiliki permission write:

**Linux/Mac:**
```bash
chmod -R 755 data/
```

**Windows:**
- Klik kanan folder `data/` → Properties → Security → Edit → Berikan Full Control

## 🚀 Menjalankan Aplikasi

1. Jalankan XAMPP/WAMP atau PHP Built-in Server:

**XAMPP/WAMP:**
```
http://localhost/Data-Base-Guru-v2
```

**PHP Built-in Server:**
```bash
php -S localhost:8000
```
Akses: `http://localhost:8000`

2. Login dengan Google Account Anda
3. Berikan izin akses ke Google Sheets & Drive
4. Selesai! Dashboard akan muncul

## 🌐 Fitur Multi-Bahasa (i18n)

Aplikasi mendukung 2 bahasa:
- 🇮🇩 Bahasa Indonesia (Default)
- 🇬🇧 English

**Cara Mengganti Bahasa:**
1. Klik menu **Settings** di sidebar
2. Pilih bahasa dari dropdown "Bahasa / Language"
3. Halaman akan reload otomatis dengan bahasa yang dipilih
4. Preferensi bahasa tersimpan di localStorage + cookies

## 📁 Struktur Folder

```
Data-Base-Guru-v2/
├── assets/
│   ├── css/
│   │   └── style.css        # Main stylesheet
│   ├── js/
│   │   ├── ajax.js          # AJAX operations
│   │   ├── i18n.js          # Internationalization
│   │   └── main.js          # Main JavaScript
│   └── images/              # Gambar/Icon
├── auth/
│   ├── login.php            # Halaman login
│   ├── logout.php           # Proses logout
│   └── google-callback.php  # OAuth callback
├── data/
│   ├── credentials.json     # Google API credentials (gitignored)
│   └── token.json           # Google API token (gitignored)
├── includes/
│   ├── config_ex.php        # Template konfigurasi
│   ├── config.php           # Konfigurasi (gitignored)
│   ├── i18n.php             # i18n engine
│   ├── lang/
│   │   ├── id.php           # Indonesian translations
│   │   └── en.php           # English translations
│   ├── header.php           # Header template
│   ├── footer.php           # Footer template
│   ├── sidebar.php          # Sidebar navigation
│   └── api.php              # API endpoints
├── pages/
│   ├── links/               # Halaman Links Management
│   │   ├── index.php        # List links
│   │   ├── add.php          # Tambah link
│   │   └── edit.php         # Edit link
│   ├── forms/               # Halaman Forms Management
│   │   ├── index.php        # List forms
│   │   ├── add.php          # Tambah form
│   │   └── edit.php         # Edit form
│   ├── files/               # File Manager
│   │   └── upload.php       # Upload file
│   ├── settings.php         # Pengaturan
│   └── profile.php          # Profil user
├── vendor/                  # Composer dependencies (gitignored)
├── .gitignore               # Git ignore rules
├── composer.json            # Composer config
├── composer.lock            # Composer lock file
├── index.php                # Dashboard
└── README.md                # Dokumentasi
```

## 📚 Dokumentasi Penggunaan

### Mengelola Links

1. Login ke sistem
2. Pilih menu **"Kelola Links"** / **"Manage Links"** di sidebar
3. Klik **"Tambah Link Baru"** / **"Add New Link"**
4. Isi form (Nama, URL, Kategori)
5. Klik **"Simpan"** / **"Save"**
6. Link akan tersimpan di Google Sheets dan mendapatkan QR Code otomatis

### Mengelola Forms

1. Pilih menu **"Kelola Forms"** / **"Manage Forms"** di sidebar
2. Klik **"Tambah Form Baru"** / **"Add New Form"**
3. Isi form (Nama Form, Google Form URL, Kategori)
4. Klik **"Simpan"** / **"Save"**
5. Form akan tersimpan di Google Sheets

### Upload File ke Google Drive

1. Pilih menu **"Upload File"** di sidebar
2. Klik area upload atau drag & drop file
3. Pilih kategori file
4. Klik **"Upload"**
5. File akan tersimpan di Google Drive

### Mengganti Bahasa

1. Pilih menu **"Settings"** / **"Pengaturan"**
2. Di section "Preferensi" / "Preferences", pilih bahasa dari dropdown
3. Halaman akan reload dengan bahasa yang dipilih

## 🔒 Keamanan

- Google OAuth 2.0 untuk autentikasi
- Session management dengan auto-logout (30 menit inaktif)
- Token refresh otomatis
- Credentials dan token di-gitignore
- Input validation & sanitization
- AJAX CSRF protection
- Secure file upload handling

## ⚙️ Auto Logout System

Aplikasi memiliki fitur auto-logout otomatis untuk keamanan:
- **Timeout**: 30 menit (1800 detik) tanpa aktivitas
- **Warning**: Muncul 2 menit sebelum logout
- **Reset**: Timer reset otomatis saat ada aktivitas (click, keypress, scroll)
- **Countdown**: Menampilkan countdown di warning modal

## 🐛 Troubleshooting

### Error: "Invalid credentials"
- Pastikan file `includes/config.php` sudah dibuat dari `config_ex.php`
- Cek GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET sudah benar
- Pastikan `data/credentials.json` sudah ada

### Error: "Permission denied" / "Access denied"
- Berikan write permission pada folder `data/`
- Pastikan Google Sheets ID sudah benar
- Cek permission sharing Google Sheets (minimal "Editor")

### Error: "Token expired"
- Hapus file `data/token.json`
- Login ulang untuk generate token baru

### Bahasa tidak berubah
- Clear browser cache dan cookies
- Pastikan JavaScript tidak di-block
- Cek browser console untuk error

### Google API Error
- Pastikan Google Sheets API & Drive API sudah di-enable
- Cek quota limit di Google Cloud Console
- Pastikan redirect URI sudah benar

## 📝 Changelog

### Version 2.1 (2025-10-25)
- ✅ Implementasi sistem i18n (Multi-bahasa: Indonesia & English)
- ✅ Tambah language switcher di Settings
- ✅ Refactor dari dark mode ke i18n system
- ✅ Cleanup kode dan dokumentasi
- ✅ Tambah .gitignore untuk keamanan credentials
- ✅ Buat config_ex.php sebagai template

### Version 2.0 (2025-10-23)
- ✅ Rebuild dengan struktur yang lebih baik
- ✅ Integrasi Google Sheets API untuk data storage
- ✅ Integrasi Google Drive API untuk file management
- ✅ Implementasi auto-logout system (30 menit)
- ✅ Modern UI dengan sidebar collapsible
- ✅ AJAX operations untuk UX lebih baik
- ✅ QR Code generation untuk links
- ✅ Responsive design untuk semua devices

### Version 1.0 (Previous)
- Basic CRUD operations
- Simple authentication

## 👥 Kontributor

- Developer: LTZ24
- GitHub: [@LTZ24](https://github.com/LTZ24)
- Repository: [DATA-MANAGEMENT_v2](https://github.com/LTZ24/DATA-MANAGEMENT_v2)

## 📄 Lisensi

MIT License - Copyright © 2025 LTZ24

## 📞 Support

Untuk bantuan atau pertanyaan:
- GitHub Issues: [Create Issue](https://github.com/LTZ24/DATA-MANAGEMENT_v2/issues)
- Email: [Contact via GitHub](https://github.com/LTZ24)

## 🙏 Acknowledgments

- Google Cloud Platform untuk API services
- Font Awesome untuk icons
- Composer untuk dependency management
- PHP Google API Client Library

---

**Dibuat dengan ❤️ oleh LTZ24**

⭐ Jika project ini membantu, berikan star di GitHub!
