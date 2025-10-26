# Session Management - Data Management System

## 📋 Overview

Aplikasi ini menggunakan **Session Activity Tracking** untuk menjaga keamanan dan mencegah akses tidak sah. Session akan otomatis berakhir jika user tidak aktif selama **30 menit**.

---

## 🔄 Cara Kerja

### 1. **Session Lifecycle**

```
User Login → Session Created → Activity Tracking → Auto Renewal → Timeout/Logout
```

### 2. **Activity Detection**

Session akan **diperpanjang otomatis** jika user melakukan aktivitas:
- ✅ Mouse movement
- ✅ Keyboard input  
- ✅ Scroll
- ✅ Click/Touch
- ✅ Any page interaction

### 3. **Timeout Mechanism**

```php
// Server Side (includes/config.php)
if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    
    if ($inactive_time > 1800) { // 30 minutes = 1800 seconds
        session_unset();
        session_destroy();
        redirect('/auth/login.php?session_timeout=1');
    }
}

// Update activity on each request
$_SESSION['last_activity'] = time();
```

---

## ⚙️ Implementasi

### **Server Side (PHP)**

File: `includes/config.php`

**Fitur:**
1. ✅ Session timeout check on every page load
2. ✅ Auto update `last_activity` timestamp
3. ✅ Session regeneration setiap 30 menit (security)
4. ✅ Redirect ke login page dengan pesan timeout

**Konfigurasi:**
```php
// Session timeout: 30 minutes (1800 seconds)
$timeout = 1800;

// Session regeneration: 30 minutes
if (!isset($_SESSION['created']) || time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
```

### **Client Side (JavaScript)**

File: `assets/js/session-keepalive.js`

**Fitur:**
1. ✅ **Auto Ping** setiap 5 menit ke server
2. ✅ **Activity Detection** (mouse, keyboard, scroll, click)
3. ✅ **Warning Alert** 2 menit sebelum timeout
4. ✅ **Auto Redirect** saat timeout tercapai

**Cara Kerja:**
```javascript
// Ping server every 5 minutes
setInterval(pingServer, 5 * 60 * 1000);

// Detect user activity
['mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach(event => {
    document.addEventListener(event, updateActivity);
});

// Check idle time every minute
setInterval(checkIdleTime, 60 * 1000);
```

---

## 📊 Timeline

```
┌─────────────────────────────────────────────────────────────────┐
│                     Session Timeline (30 min)                    │
├─────────────────────────────────────────────────────────────────┤
│ 0min                 5min                28min            30min  │
│  │                    │                    │                │    │
│  ├─ Login            ├─ Auto Ping         ├─ Warning       ├─ Timeout
│  │                    │                    │                │    │
│  ▼                    ▼                    ▼                ▼    │
│ START ──────────► ACTIVE ─────────► WARNING ────────► LOGOUT   │
│                                                                   │
│ ✅ Any activity resets timer back to 0min                        │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🎯 Skenario Penggunaan

### **Skenario 1: User Aktif Terus Menerus**

```
09:00 - Login ✅
09:05 - Auto ping (reset timer) ✅
09:10 - User klik menu (reset timer) ✅
09:15 - Auto ping (reset timer) ✅
09:20 - User scroll (reset timer) ✅
...
12:00 - Session masih aktif ✅
```

**Kesimpulan:** Session **TIDAK AKAN** timeout selama user aktif.

---

### **Skenario 2: User Idle (Tidak Ada Aktivitas)**

```
09:00 - Login ✅
09:05 - Auto ping ✅
09:10 - Auto ping ✅
09:15 - Auto ping ✅
...
09:28 - Warning muncul ⚠️ "Sesi akan berakhir dalam 2 menit"
09:30 - Session timeout ❌ Auto redirect ke login
```

**Kesimpulan:** Session **AKAN** timeout setelah 30 menit tanpa aktivitas.

---

### **Skenario 3: User Idle Lalu Aktif Lagi**

```
09:00 - Login ✅
09:05 - Auto ping ✅
...
09:25 - User idle (25 menit) ⏳
09:28 - Warning muncul ⚠️
09:29 - User klik mouse 🖱️ (timer reset ke 0) ✅
09:34 - Auto ping ✅
...
10:00 - Session masih aktif ✅
```

**Kesimpulan:** Aktivitas **APAPUN** akan reset timer kembali ke 0.

---

## 🔧 Instalasi

### **1. Include Script di Semua Halaman**

Tambahkan di `<head>` atau sebelum `</body>`:

```html
<!-- Session Keep-Alive -->
<script src="<?php echo BASE_URL; ?>/assets/js/session-keepalive.js"></script>
```

### **2. Pastikan Server-Side Handler Aktif**

File `includes/config.php` sudah include handler:

```php
// Handle AJAX session update requests
if (isset($_SERVER['HTTP_X_SESSION_UPDATE'])) {
    $_SESSION['last_activity'] = time();
    http_response_code(204);
    exit();
}
```

### **3. Test Session Timeout**

**Testing Manual:**
1. Login ke aplikasi
2. Buka browser console (F12)
3. Jalankan command:
   ```javascript
   // Lihat last activity
   SessionKeepAlive.getLastActivity();
   
   // Lihat idle time (dalam ms)
   SessionKeepAlive.getIdleTime();
   
   // Paksa ping server
   SessionKeepAlive.pingNow();
   
   // Tampilkan warning
   SessionKeepAlive.showWarning();
   ```

**Testing Otomatis:**
1. Ubah timeout menjadi 2 menit (120 detik) untuk testing
2. Login dan idle selama 2 menit
3. Session harus timeout dan redirect ke login

---

## 📝 Konfigurasi

### **Mengubah Timeout Duration**

**Server Side (`includes/config.php`):**
```php
// Change from 1800 (30 min) to 300 (5 min)
if ($inactive_time > 300) {
    // timeout logic
}
```

**Client Side (`assets/js/session-keepalive.js`):**
```javascript
const CONFIG = {
    PING_INTERVAL: 2 * 60 * 1000,      // 2 menit
    SESSION_TIMEOUT: 5 * 60 * 1000,    // 5 menit
    WARNING_TIME: 4 * 60 * 1000,       // 4 menit (warning 1 menit sebelum)
};
```

⚠️ **Penting:** Nilai di server dan client **HARUS SAMA**!

---

## 🐛 Troubleshooting

### **Problem 1: Session Timeout Terlalu Cepat**

**Solusi:**
- Cek apakah script `session-keepalive.js` di-load
- Buka console, lihat apakah ada error
- Pastikan `X-Session-Update` header diterima server

### **Problem 2: Warning Tidak Muncul**

**Solusi:**
- Pastikan Font Awesome loaded (untuk icon warning)
- Cek browser console untuk JavaScript errors
- Test manual: `SessionKeepAlive.showWarning()`

### **Problem 3: Auto Ping Tidak Berfungsi**

**Solusi:**
- Cek network tab di browser devtools
- Pastikan request dengan header `X-Session-Update` terkirim
- Verify server handler di `includes/config.php` aktif

---

## 📈 Monitoring & Debugging

### **Console Commands**

```javascript
// Check last activity timestamp
SessionKeepAlive.getLastActivity();
// Output: 1730000000000 (Unix timestamp in ms)

// Check idle time in milliseconds
SessionKeepAlive.getIdleTime();
// Output: 120000 (2 minutes)

// Manual ping
SessionKeepAlive.pingNow();
// Output: [Session] Keep-alive ping successful

// Show/hide warning manually
SessionKeepAlive.showWarning();
SessionKeepAlive.hideWarning();
```

### **Server-Side Logging**

Tambahkan logging di `includes/config.php`:

```php
// Log session activity
if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    error_log("[Session] User: {$_SESSION['email']}, Idle: {$inactive_time}s");
}
```

---

## 🔐 Security Features

1. ✅ **Session Regeneration** - ID session di-regenerate setiap 30 menit
2. ✅ **HTTP Only Cookies** - Cookie session tidak bisa diakses JavaScript
3. ✅ **Secure Flag** - Cookie hanya dikirim via HTTPS (production)
4. ✅ **Activity Tracking** - Mencegah session hijacking
5. ✅ **Auto Timeout** - Mencegah akses tidak sah jika user lupa logout

---

## 📌 Best Practices

### **DO's ✅**

- ✅ Selalu include `session-keepalive.js` di semua halaman protected
- ✅ Set timeout sesuai kebutuhan (30 min untuk aplikasi umum)
- ✅ Tampilkan warning sebelum timeout
- ✅ Log activity untuk debugging
- ✅ Test session timeout secara berkala

### **DON'Ts ❌**

- ❌ Jangan set timeout terlalu pendek (< 5 menit)
- ❌ Jangan lupakan server-side validation
- ❌ Jangan gunakan session untuk menyimpan data sensitif
- ❌ Jangan skip session regeneration
- ❌ Jangan expose session ID di URL

---

## 📚 References

- PHP Session Management: https://www.php.net/manual/en/book.session.php
- OWASP Session Management: https://owasp.org/www-community/Session_Management_Cheat_Sheet
- Google OAuth Session: https://developers.google.com/identity/protocols/oauth2

---

**Last Updated:** October 26, 2025  
**Version:** 1.0  
**Maintained by:** LTZ24
