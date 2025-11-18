# PWA Icon Generator Script

## Manual Generation (Recommended)

Gunakan online tool untuk generate icons dari `smk62.png`:

### Option 1: PWA Asset Generator (Recommended)
1. Visit: https://www.pwabuilder.com/imageGenerator
2. Upload file: `assets/images/smk62.png`
3. Download semua icon sizes
4. Extract ke folder: `assets/images/icons/`

### Option 2: RealFaviconGenerator
1. Visit: https://realfavicongenerator.net/
2. Upload `smk62.png`
3. Configure options:
   - iOS: Select "Add a solid, plain background"
   - Android: Select "Use a distinct icon for Android"
   - Theme Color: #50e3c2
4. Download icons package
5. Extract ke folder: `assets/images/icons/`

### Option 3: Using ImageMagick (Command Line)

Jika sudah install ImageMagick, jalankan commands berikut di PowerShell:

```powershell
cd c:\xampp\htdocs\Data-Base-Guru-v2\assets\images

# Generate all required sizes
magick smk62.png -resize 72x72 icons/icon-72x72.png
magick smk62.png -resize 96x96 icons/icon-96x96.png
magick smk62.png -resize 128x128 icons/icon-128x128.png
magick smk62.png -resize 144x144 icons/icon-144x144.png
magick smk62.png -resize 152x152 icons/icon-152x152.png
magick smk62.png -resize 192x192 icons/icon-192x192.png
magick smk62.png -resize 384x384 icons/icon-384x384.png
magick smk62.png -resize 512x512 icons/icon-512x512.png

# Apple Touch Icon
magick smk62.png -resize 180x180 icons/apple-touch-icon.png

# Maskable icons (with padding for safe area)
magick smk62.png -resize 144x144 -gravity center -extent 192x192 icons/icon-192x192-maskable.png
magick smk62.png -resize 384x384 -gravity center -extent 512x512 icons/icon-512x512-maskable.png

# Favicon sizes
magick smk62.png -resize 32x32 icons/favicon-32x32.png
magick smk62.png -resize 16x16 icons/favicon-16x16.png
```

---

## Required Icon Sizes

- ✅ 72x72 - Android notification
- ✅ 96x96 - Standard small icon
- ✅ 128x128 - Chrome Web Store
- ✅ 144x144 - Windows tile
- ✅ 152x152 - iPad non-retina
- ✅ 192x192 - Android home screen (minimum)
- ✅ 384x384 - Android splash screen
- ✅ 512x512 - Android home screen (recommended)
- ✅ 180x180 - Apple Touch Icon (iOS)
- ✅ 192x192 & 512x512 Maskable - Adaptive icons

---

## Quick Setup (Temporary)

Untuk testing sementara, copy `smk62.png` ke semua ukuran:

```powershell
cd c:\xampp\htdocs\Data-Base-Guru-v2\assets\images

Copy-Item smk62.png icons/icon-72x72.png
Copy-Item smk62.png icons/icon-96x96.png
Copy-Item smk62.png icons/icon-128x128.png
Copy-Item smk62.png icons/icon-144x144.png
Copy-Item smk62.png icons/icon-152x152.png
Copy-Item smk62.png icons/icon-192x192.png
Copy-Item smk62.png icons/icon-384x384.png
Copy-Item smk62.png icons/icon-512x512.png
Copy-Item smk62.png icons/icon-192x192-maskable.png
Copy-Item smk62.png icons/icon-512x512-maskable.png
Copy-Item smk62.png icons/apple-touch-icon.png
Copy-Item smk62.png icons/favicon-32x32.png
Copy-Item smk62.png icons/favicon-16x16.png
```

**Note**: Icon akan tetap berfungsi tapi ukurannya tidak optimal. Gunakan tool online untuk hasil terbaik.
