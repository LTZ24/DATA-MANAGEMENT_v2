# 📱 Tutorial Testing PWA di HP - Database Guru SMKN 62

## ✅ Prerequisite
- HP Android sudah terkoneksi via ADB (`adb devices` menunjukkan status `device`)
- XAMPP Apache running di port 80
- USB Debugging sudah enabled di HP

---

## 🚀 Step-by-Step Testing

### **Step 1: Setup Port Forwarding**

Buka PowerShell di PC, jalankan:

```powershell
# Masuk ke direktori project
cd c:\xampp\htdocs\Data-Base-Guru-v2

# Clear port forwarding yang lama (jika ada)
adb reverse --remove-all

# Setup port forwarding Apache (port 80)
adb reverse tcp:80 tcp:80

# Verify port forwarding aktif
adb reverse --list
```

**Expected Output:**
```
tcp:80 tcp:80
```

✅ Sekarang HP bisa akses localhost PC via port 80!

---

### **Step 2: Akses PWA dari HP**

1. **Buka Chrome di HP**
2. **Ketik di address bar:**
   ```
   http://localhost/Data-Base-Guru-v2
   ```
3. **Enter** - Halaman login Database Guru akan terbuka

✅ Jika berhasil, Anda akan lihat halaman login dengan gradient turquoise!

---

### **Step 3: Chrome Remote Debugging (PC)**

Di PC, buka Chrome dan akses:

```
chrome://inspect/#devices
```

**Pastikan:**
- ✅ "Discover USB devices" dalam keadaan **checked**
- ✅ HP Anda muncul di list devices
- ✅ Tab "localhost/Data-Base-Guru-v2" terlihat

**Klik tombol "Inspect"** di bawah tab tersebut

✅ DevTools akan terbuka untuk remote debugging HP!

---

### **Step 4: Verify Service Worker**

Di Chrome DevTools (yang tadi dibuka):

1. **Tab "Application"** (pojok kiri)
2. **Klik "Service Workers"** di sidebar kiri
3. **Verify:**
   - ✅ Status: **activated and is running**
   - ✅ Source: `/Data-Base-Guru-v2/sw.js`
   - ✅ Scope: `/Data-Base-Guru-v2/`

**Klik "Update"** untuk force update service worker (optional)

---

### **Step 5: Check Manifest**

Masih di DevTools tab "Application":

1. **Klik "Manifest"** di sidebar kiri
2. **Verify:**
   - ✅ Name: **Database Guru SMKN 62 Jakarta**
   - ✅ Short name: **DB Guru 62**
   - ✅ Theme color: **#50e3c2** (turquoise)
   - ✅ Display: **standalone**
   - ✅ Icons: 10 icons loaded (72x72 sampai 512x512)

**Scroll ke bawah** → Lihat preview icon yang akan muncul di home screen

---

### **Step 6: Check Cache Storage**

Masih di DevTools tab "Application":

1. **Expand "Cache Storage"** di sidebar kiri
2. **Klik cache "db-guru-62-v1.0.0"**
3. **Verify ada file cached:**
   - ✅ `/Data-Base-Guru-v2/index.php`
   - ✅ `/Data-Base-Guru-v2/assets/css/style.css`
   - ✅ `/Data-Base-Guru-v2/assets/js/main.js`
   - ✅ `/Data-Base-Guru-v2/assets/js/pwa.js`
   - ✅ Font Awesome CSS
   - ✅ Icon files

**Klik salah satu file** → Preview akan muncul di panel kanan

---

### **Step 7: Test Install PWA**

**Option A: Via Install Button (Automatic)**

Di HP, tunggu 3-5 detik:
- Button **"Install App"** akan muncul di **pojok kanan bawah**
- Icon: ⬇️ Download dengan text "Install App"
- **Tap button tersebut**
- Dialog "Add to Home Screen" akan muncul
- **Tap "Add"**

**Option B: Via Chrome Menu (Manual)**

1. Di HP, tap **menu Chrome** (3 titik vertikal di pojok kanan atas)
2. Cari opsi **"Add to Home Screen"** atau **"Install app"**
3. **Tap opsi tersebut**
4. Dialog konfirmasi muncul
5. **Tap "Add"**

✅ Icon **DB Guru 62** akan muncul di home screen HP!

---

### **Step 8: Test Standalone Mode**

1. **Tutup Chrome** di HP
2. **Tap icon "DB Guru 62"** di home screen
3. **App akan buka dalam mode fullscreen** (tanpa browser UI)
4. **Verify:**
   - ✅ No address bar
   - ✅ No browser menu
   - ✅ Status bar themed turquoise (#50e3c2)
   - ✅ Seperti native app

---

### **Step 9: Test Offline Mode**

**Via Chrome DevTools (Recommended):**

1. Di PC DevTools yang masih terbuka
2. **Tab "Network"**
3. **Klik dropdown** yang tulisannya "Online"
4. **Pilih "Offline"**
5. Di HP, **swipe down** untuk refresh halaman
6. **Halaman offline.html akan muncul:**
   - Icon gradient turquoise dengan animasi pulse
   - Text: "Anda Sedang Offline"
   - Button: "Coba Lagi"
   - Status: "Tidak Ada Koneksi Internet"

**Set kembali ke "Online":**
1. DevTools → Network → Pilih "Online"
2. Di HP, tap **button "Coba Lagi"**
3. Halaman normal akan load kembali

✅ Offline fallback working!

---

### **Step 10: Test Cache Functionality**

1. **Login** ke aplikasi (dengan Google OAuth)
2. **Navigate** ke beberapa halaman:
   - Dashboard
   - Files
   - Links
   - Forms
   - Settings
3. **Set Network ke Offline** (via DevTools)
4. **Browse halaman-halaman yang sudah pernah dibuka:**
   - ✅ Halaman yang sudah di-cache akan tetap bisa dibuka
   - ✅ Loading cepat (dari cache)
   - ❌ Halaman yang belum pernah dibuka → offline.html

**Check cache bertambah:**
1. DevTools → Application → Cache Storage
2. Klik refresh cache list
3. Lihat file bertambah sesuai halaman yang dibuka

---

## 🔧 Advanced Testing

### **Test 1: Update Detection**

1. **Edit file** (misal: `assets/js/main.js`)
2. **Tambahkan comment** di paling atas: `// Test update`
3. **Save file**
4. **Edit sw.js**, ubah version:
   ```javascript
   const CACHE_VERSION = 'v1.0.1'; // dari v1.0.0
   ```
5. **Save sw.js**
6. Di HP, **refresh halaman**
7. **Banner update** akan muncul di atas:
   - Background: Purple gradient
   - Text: "Update tersedia!"
   - Button: "Update Sekarang"
8. **Tap "Update Sekarang"**
9. Halaman akan reload dengan versi baru

---

### **Test 2: Console Logs**

Di DevTools → Tab "Console", monitor logs:

```
[PWA] Running in standalone mode
[PWA] Service worker registered: /Data-Base-Guru-v2/
[SW] Installing service worker... v1.0.0
[SW] Caching static assets
[SW] Static assets cached successfully
[SW] Service worker activated
[Session] Keep-alive system initialized
```

✅ Semua logs menunjukkan PWA aktif dan berfungsi!

---

### **Test 3: Network Performance**

1. DevTools → **Tab "Network"**
2. **Refresh halaman**
3. **Lihat kolom "Size":**
   - File dari cache: **(from ServiceWorker)** atau **(disk cache)**
   - File baru: Size dalam KB/MB
4. **Performance:**
   - Cached files: < 10ms load time
   - Network files: Tergantung koneksi

---

### **Test 4: Application Shortcuts** (Android)

1. **Long press** icon "DB Guru 62" di home screen
2. **Shortcuts menu** akan muncul:
   - 📊 Dashboard
   - ⬆️ Upload File
   - 📁 Files
   - 🔗 Links
3. **Tap salah satu shortcut**
4. App langsung buka ke halaman tersebut

---

## 📊 Checklist Testing

### **✅ Installation:**
- [ ] Service Worker registered
- [ ] Manifest loaded correctly
- [ ] Install button muncul
- [ ] Install berhasil
- [ ] Icon di home screen
- [ ] Standalone mode works

### **✅ Offline Support:**
- [ ] Cache terbentuk
- [ ] Offline page muncul
- [ ] Cached pages accessible offline
- [ ] Online detection works
- [ ] Auto reload saat online kembali

### **✅ PWA Features:**
- [ ] Theme color applied (#50e3c2)
- [ ] Fullscreen mode
- [ ] No browser UI
- [ ] App shortcuts (Android)
- [ ] Update detection
- [ ] Install prompt

### **✅ Performance:**
- [ ] Fast load dari cache
- [ ] Network-first untuk PHP
- [ ] Cache-first untuk assets
- [ ] Session keepalive works

---

## 🐛 Troubleshooting

### **Problem: Install Button Tidak Muncul**

**Solutions:**
1. **Check manifest** di DevTools → Application → Manifest
2. **Verify Service Worker** status: activated
3. **Hard refresh** (Ctrl+Shift+R di Chrome PC, lalu refresh HP)
4. **Clear cache:**
   ```javascript
   // Di DevTools Console:
   window.pwaManager.clearCache()
   ```
5. **Unregister SW** dan register ulang:
   ```javascript
   navigator.serviceWorker.getRegistrations().then(regs => {
     regs.forEach(reg => reg.unregister())
   })
   ```
   Refresh halaman

---

### **Problem: Offline Page Tidak Muncul**

**Check:**
1. DevTools → Application → Cache Storage
2. Cari file: `/Data-Base-Guru-v2/offline.html`
3. Jika tidak ada, **force cache:**
   ```javascript
   // Di DevTools Console:
   caches.open('db-guru-62-v1.0.0').then(cache => {
     cache.add('/Data-Base-Guru-v2/offline.html')
   })
   ```

---

### **Problem: Cache Tidak Update**

**Force update:**
1. DevTools → Application → Service Workers
2. ✅ Check "Update on reload"
3. ✅ Check "Bypass for network"
4. Refresh halaman
5. Atau **clear all cache:**
   - Application → Cache Storage
   - Right click → Delete

---

### **Problem: Port Forwarding Disconnect**

```powershell
# Check status
adb reverse --list

# Jika kosong, setup ulang
adb reverse tcp:80 tcp:80

# Verify
adb reverse --list
```

---

## 🎯 Expected Results Summary

| Feature | Expected Result |
|---------|----------------|
| **Service Worker** | ✅ Activated and running |
| **Manifest** | ✅ Valid, all fields loaded |
| **Icons** | ✅ 13 sizes (72-512px) |
| **Cache** | ✅ Static assets cached |
| **Offline** | ✅ Fallback page shown |
| **Install** | ✅ Button appears, install works |
| **Standalone** | ✅ Fullscreen, no browser UI |
| **Update** | ✅ Banner shows, one-click update |
| **Theme** | ✅ Turquoise (#50e3c2) |
| **Shortcuts** | ✅ 4 shortcuts (Android) |

---

## 📱 Screenshots to Verify

### **1. Install Prompt:**
- Button di pojok kanan bawah
- Icon: Download + "Install App"
- Background: Turquoise gradient

### **2. Home Screen Icon:**
- Icon: Logo SMK 62
- Label: "DB Guru 62"
- Tap membuka fullscreen

### **3. Standalone Mode:**
- No address bar
- No browser controls
- Status bar: Turquoise
- Fullscreen content

### **4. Offline Page:**
- Gradient background: Turquoise → Light Blue
- Icon: Animated pulse
- Status badge: "Tidak Ada Koneksi"
- Button: "Coba Lagi"

### **5. Update Banner:**
- Position: Top of screen
- Background: Purple gradient
- Icon: Sync
- Button: "Update Sekarang"

---

## ✅ Testing Complete!

Jika semua checklist di atas ✅, maka PWA sudah **production-ready**!

**Next steps:**
- Deploy ke server dengan SSL certificate
- Setup Firebase Cloud Messaging untuk push notifications (optional)
- Add more offline-first features
- Optimize cache strategy

**Selamat! PWA Database Guru 62 berhasil dibuat!** 🎉
