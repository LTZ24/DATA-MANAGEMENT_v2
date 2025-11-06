# Data Management System - Flowchart

## Alur Utama Aplikasi

```mermaid
flowchart TD
    Start([User Akses Aplikasi]) --> Login{Sudah Login?}
    
    Login -->|Belum| LoginPage[Login dengan Google]
    Login -->|Sudah| Dashboard[Dashboard]
    
    LoginPage --> OAuth[Google OAuth 2.0]
    OAuth --> Auth{Berhasil?}
    Auth -->|Tidak| LoginPage
    Auth -->|Ya| Dashboard
    
    Dashboard --> Menu{Pilih Fitur}
    
    Menu -->|Link Manager| Links[Kelola Links]
    Menu -->|Form Manager| Forms[Kelola Forms]
    Menu -->|File Manager| Files[Kelola Files]
    Menu -->|Logout| Logout[Keluar]
    
    Links --> CRUD1[Tambah/Edit/Hapus Links]
    Forms --> CRUD2[Buat/Edit/Hapus Forms]
    Files --> CRUD3[Upload/Download/Hapus Files]
    
    CRUD1 --> GSheets1[(Google Sheets)]
    CRUD2 --> GSheets2[(Google Sheets)]
    CRUD3 --> GDrive[(Google Drive)]
    CRUD3 --> GSheets3[(Google Sheets)]
    
    GSheets1 --> Dashboard
    GSheets2 --> Dashboard
    GDrive --> Dashboard
    GSheets3 --> Dashboard
    
    Logout --> LoginPage
    
    style Start fill:#50e3c2
    style Dashboard fill:#4dd0e1
    style LoginPage fill:#ffd54f
    style Logout fill:#ff6b6b
    style GSheets1 fill:#50e3c2
    style GSheets2 fill:#50e3c2
    style GSheets3 fill:#50e3c2
    style GDrive fill:#50e3c2
```

## Arsitektur Sistem

```mermaid
graph LR
    User[User/Browser] --> PHP[PHP Application]
    PHP --> OAuth[Google OAuth 2.0]
    PHP --> Sheets[Google Sheets API]
    PHP --> Drive[Google Drive API]
    
    Sheets --> Data[(Google Sheets<br/>Database)]
    Drive --> Storage[(Google Drive<br/>Storage)]
    
    style User fill:#4dd0e1
    style PHP fill:#ffd54f
    style Data fill:#50e3c2
    style Storage fill:#50e3c2
```

---

## Keterangan

- **Login**: Autentikasi menggunakan Google OAuth 2.0
- **Dashboard**: Halaman utama setelah login berhasil
- **Link Manager**: Mengelola links (URL) di Google Sheets
- **Form Manager**: Membuat dan mengelola forms + responses
- **File Manager**: Upload/download files ke Google Drive
- **Session**: Timeout otomatis 30 menit
- **i18n**: Support 2 bahasa (Indonesia & English)

---

**Legend:**
- 🟢 Hijau: Success / Storage
- 🔵 Biru: Active Process
- 🟡 Kuning: Login/Auth
- 🔴 Merah: Logout/Exit
