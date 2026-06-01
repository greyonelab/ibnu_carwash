# 🚀 Quick Start - Fitur Kategori Layanan

## TL;DR - Langkah Cepat

```bash
# 1. Jalankan migration
cd c:\xampp\htdocs\carwashv2
php artisan migrate

# 2. Update data existing (pilih salah satu)
# Opsi A: Via MySQL command
mysql -u root -p CARWASH < update_service_categories.sql

# Opsi B: Via phpMyAdmin
# Buka http://localhost/phpmyadmin
# Pilih database CARWASH > SQL tab
# Copy-paste isi file update_service_categories.sql

# 3. (Opsional) Insert sample data
mysql -u root -p CARWASH < sample_services_with_categories.sql

# 4. Test web
# Buka http://localhost/carwashv2/public/services
# Buka http://localhost/carwashv2/public/orders/create

# 5. Test mobile
cd carwashapk/washmanager_mobile
flutter run
```

## ✅ Apa yang Sudah Berubah?

### Sebelum:
- Layanan hanya punya "type" (standard/premium/detail)
- Semua layanan muncul untuk semua jenis kendaraan
- Tidak ada filter berdasarkan jenis kendaraan

### Sesudah:
- Layanan punya "category" (mobil/motor/lainnya) DAN "type" (standard/premium/detail)
- Layanan otomatis ter-filter berdasarkan kategori kendaraan
- User hanya melihat layanan yang relevan

## 📋 Struktur Baru

```
Service {
  id: 1,
  name: "Cuci Motor Standard",
  type: "standard",        // ← Tipe layanan (standard/premium/detail)
  category: "motor",       // ← BARU! Kategori kendaraan (mobil/motor/lainnya)
  price: 15000,
  duration_minutes: 20
}
```

## 🎯 Cara Kerja Filter

### Web (orders/create)
```
User pilih: "Kategori Kendaraan" = "Motor"
         ↓
JavaScript filter layanan
         ↓
Hanya tampilkan: category = "motor"
```

### Mobile (create_order_screen)
```
User pilih: "Jenis Kendaraan" = "Motor"
         ↓
Dart .where() filter layanan
         ↓
Hanya tampilkan: category = "motor"
```

## 🔧 File Penting

| File | Fungsi |
|------|--------|
| `UPDATE_CATEGORY_GUIDE.md` | Panduan lengkap & troubleshooting |
| `CATEGORY_UPDATE_SUMMARY.md` | Summary semua perubahan |
| `IMPLEMENTATION_CHECKLIST.md` | Checklist step-by-step |
| `update_service_categories.sql` | Update data existing |
| `sample_services_with_categories.sql` | Sample data testing |

## 🧪 Quick Test

### Test 1: Web Create Service
```
1. Buka: http://localhost/carwashv2/public/services/create
2. Isi form dengan kategori "motor"
3. Save
4. ✅ Berhasil jika data tersimpan dengan category="motor"
```

### Test 2: Web Create Order
```
1. Buka: http://localhost/carwashv2/public/orders/create
2. Pilih "Kategori Kendaraan" = "Motor"
3. ✅ Berhasil jika hanya layanan motor yang muncul
```

### Test 3: API
```bash
curl http://localhost/carwashv2/public/api/services?category=motor
# ✅ Berhasil jika response hanya berisi layanan motor
```

### Test 4: Mobile
```
1. Run: flutter run
2. Create Order > Pilih "Motor"
3. ✅ Berhasil jika hanya layanan motor yang muncul
```

## ⚠️ Common Issues

### Issue: Migration error "No connection"
```bash
# Fix: Pastikan MySQL running
# Buka XAMPP > Start MySQL
```

### Issue: Layanan tidak muncul
```sql
-- Fix: Update kategori yang NULL
UPDATE services SET category = 'mobil' WHERE category IS NULL;
```

### Issue: Flutter error
```bash
# Fix: Rebuild
cd carwashapk/washmanager_mobile
flutter clean
flutter pub get
flutter pub run build_runner build --delete-conflicting-outputs
```

## 📞 Need Help?

1. Baca `UPDATE_CATEGORY_GUIDE.md` untuk panduan lengkap
2. Cek `IMPLEMENTATION_CHECKLIST.md` untuk step-by-step
3. Lihat `CATEGORY_UPDATE_SUMMARY.md` untuk detail perubahan

## 🎉 Done!

Jika semua test passed, fitur kategori layanan sudah siap digunakan!

**Happy Coding! 🚀**
