# Security Documentation - Data Management System

## 🔒 Ringkasan Keamanan

Aplikasi ini menggunakan **hybrid architecture**:
- **Google Sheets API** (Cloud Database) - ✅ Aman dari SQL Injection
- **Local Database** (SQLite/MySQL untuk cache) - ⚠️ Perlu proteksi

---

## ❓ Apakah Perlu Proteksi SQL Injection?

### **JAWABAN: YA, TETAP PERLU!**

Meskipun database utama menggunakan Google Sheets (yang TIDAK rentan SQL Injection), aplikasi ini tetap perlu proteksi karena:

1. **Ada Local Database (`api.php`)**
   - File `includes/api.php` menggunakan SQL queries
   - Digunakan untuk cache data guru
   - **VULNERABLE** jika tidak di-protect

2. **Defense in Depth Principle**
   - Proteksi berlapis lebih aman
   - Antisipasi jika ada fitur baru dengan SQL database

3. **XSS + Session Hijacking**
   - Bisa bypass Google OAuth
   - Perlu sanitize semua user input

---

## 🛡️ Proteksi yang Sudah Diterapkan

### 1. **SQL Injection Protection**

✅ **Prepared Statements di semua query:**

```php
// ❌ SEBELUM (Vulnerable)
$stmt = $db->query("SELECT * FROM guru WHERE status = 'aktif'");

// ✅ SESUDAH (Aman)
$stmt = $db->prepare("SELECT * FROM guru WHERE status = ?");
$stmt->execute(['aktif']);
```

✅ **Parameterized Queries untuk Search:**

```php
$stmt = $db->prepare("SELECT * FROM guru WHERE nama LIKE ? OR nip LIKE ?");
$searchTerm = "%$keyword%";
$stmt->execute([$searchTerm, $searchTerm]);
```

### 2. **Session Management**

✅ **Fitur keamanan session:**
- Session timeout: 30 menit inaktivity
- Regenerate session ID setelah login
- Secure session cookie (httponly, secure flag recommended)
- Session validation di setiap request

```php
// includes/config.php
if (!isLoggedIn()) {
    header('Location: auth/login.php');
    exit();
}

// Auto logout setelah 30 menit
if (time() - $_SESSION['last_activity'] > 1800) {
    ````markdown
    > NOTE: This file has been moved to `/setup/SECURITY.md` to tidy repository structure.
    > Please open `setup/SECURITY.md` for the full content.

    ````
---

**Last Updated**: October 26, 2025
**Security Audit**: ✅ PASSED (dengan catatan untuk implement HTTPS di production)
