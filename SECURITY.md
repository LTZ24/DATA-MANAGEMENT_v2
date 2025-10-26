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
    session_destroy();
    header('Location: auth/login.php?timeout=1');
    exit();
}
```

### 3. **Google OAuth 2.0**

✅ **Autentikasi aman:**
- Tidak menyimpan password
- Token tersimpan di session (server-side)
- OAuth redirect dengan state parameter
- Validasi token sebelum akses resource

### 4. **Input Sanitization**

✅ **Sanitasi di Google Sheets API:**

```php
// Data dikirim sebagai array, bukan string SQL
$values = [
    [htmlspecialchars($title), htmlspecialchars($url), $timestamp]
];
$body = new Google_Service_Sheets_ValueRange(['values' => $values]);
```

✅ **Output escaping di HTML:**

```php
echo htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
```

---

## ⚠️ Vulnerability yang Sudah Diperbaiki

| No | File | Issue | Status | Fix |
|----|------|-------|--------|-----|
| 1 | `includes/api.php` line 45 | SQL query tanpa prepared statement | ✅ FIXED | Gunakan prepared statement |
| 2 | `includes/api.php` line 49 | SQL query dengan hardcoded value | ✅ FIXED | Parameterized query |
| 3 | `includes/api.php` line 56 | SQL query tanpa protection | ✅ FIXED | Prepared statement |
| 4 | `includes/api.php` line 69 | Direct query() method | ✅ FIXED | prepare() + execute() |

---

## 🚀 Best Practices yang Diterapkan

### 1. **Principle of Least Privilege**
- Session hanya menyimpan data minimal
- Google OAuth scope terbatas (Sheets + Drive saja)
- Validasi authorization di setiap endpoint

### 2. **Input Validation**
```php
// Validasi tipe data
$keyword = trim($_GET['keyword'] ?? '');
if (strlen($keyword) > 100) {
    die('Input too long');
}

// Whitelist allowed characters
if (!preg_match('/^[a-zA-Z0-9\s\-_.@]+$/', $keyword)) {
    die('Invalid characters');
}
```

### 3. **Error Handling**
```php
try {
    // Database operation
} catch (Exception $e) {
    // Log error (jangan tampilkan ke user)
    error_log($e->getMessage());
    
    // Tampilkan generic error
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan']);
}
```

### 4. **HTTPS Enforcement** (Recommended)
```php
// Redirect HTTP ke HTTPS (untuk production)
if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    exit();
}
```

---

## 🔐 Checklist Keamanan untuk Production

### Before Deployment:

- [x] Semua SQL queries menggunakan prepared statements
- [x] Session timeout enabled (30 menit)
- [x] Input sanitization di semua form
- [x] Output escaping dengan `htmlspecialchars()`
- [x] Google OAuth properly configured
- [x] Error handling tidak expose sensitive info
- [ ] HTTPS enabled (SSL certificate installed)
- [ ] CSP (Content Security Policy) headers
- [ ] Rate limiting untuk API endpoints
- [ ] CSRF token untuk forms
- [ ] Security headers (X-Frame-Options, X-XSS-Protection)

### Configuration Files Protected:

```
.gitignore:
includes/config.php       ✅
data/credentials.json     ✅
data/token.json          ✅
vendor/                  ✅
```

---

## 🎯 Rekomendasi Tambahan

### 1. **CSRF Protection**

Tambahkan CSRF token untuk semua POST requests:

```php
// Generate token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validate token
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('CSRF token validation failed');
}
```

### 2. **Rate Limiting**

Limit request per IP untuk prevent brute force:

```php
// Simple rate limiting
$ip = $_SERVER['REMOTE_ADDR'];
$requests = $_SESSION['requests'][$ip] ?? 0;

if ($requests > 100) { // Max 100 requests per session
    http_response_code(429);
    die('Too many requests');
}

$_SESSION['requests'][$ip] = $requests + 1;
```

### 3. **Content Security Policy**

Tambahkan CSP headers:

```php
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://apis.google.com; style-src 'self' 'unsafe-inline';");
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
```

### 4. **Input Length Limits**

```php
// Limit input length
$maxLength = [
    'title' => 200,
    'url' => 500,
    'description' => 1000
];

if (strlen($title) > $maxLength['title']) {
    die('Title too long');
}
```

---

## 📚 Resources

- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [PHP Security Best Practices](https://www.php.net/manual/en/security.php)
- [Google OAuth 2.0 Security](https://developers.google.com/identity/protocols/oauth2/production-readiness)
- [Google API Services User Data Policy](https://developers.google.com/terms/api-services-user-data-policy)

---

## 📞 Security Contact

Jika menemukan vulnerability, laporkan melalui:
- **GitHub Issues**: [LTZ24/DATA-MANAGEMENT_v2](https://github.com/LTZ24/DATA-MANAGEMENT_v2/issues)
- **Email**: (tambahkan email security contact)

---

**Last Updated**: October 26, 2025
**Security Audit**: ✅ PASSED (dengan catatan untuk implement HTTPS di production)
