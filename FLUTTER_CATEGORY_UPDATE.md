# Update Flutter - Tambah Kategori "Lainnya"

## ✅ Perubahan yang Dilakukan

### File yang Diubah
- `carwashapk/washmanager_mobile/lib/screens/create_order_screen.dart`

### Perubahan Detail

#### 1. Menambahkan Opsi "Lainnya" di Vehicle Types
```dart
final List<Map<String, dynamic>> _vehicleTypes = [
  {
    'type': 'Motor',
    'icon': Icons.motorcycle,
    'color': Colors.orange,
    'description': 'Sepeda motor, scooter, dll'
  },
  {
    'type': 'Mobil',
    'icon': Icons.directions_car,
    'color': Colors.blue,
    'description': 'Sedan, hatchback, SUV, MPV, dll'
  },
  {
    'type': 'Lainnya',  // ← BARU!
    'icon': Icons.local_shipping,
    'color': Colors.green,
    'description': 'Truk, bus, pickup, dll'
  },
];
```

#### 2. Filter Service Sudah Mendukung "Lainnya"
Filter otomatis sudah mendukung kategori "Lainnya":
```dart
if (vehicleCategory == 'motor') {
  return serviceCategory == 'motor';
} else if (vehicleCategory == 'mobil') {
  return serviceCategory == 'mobil';
} else {
  return serviceCategory == 'lainnya';  // ← Sudah ada!
}
```

## 🎯 Cara Kerja

### Step 1: Pilih Jenis Kendaraan
User sekarang memiliki 3 pilihan:
1. **Motor** - untuk sepeda motor, scooter
2. **Mobil** - untuk sedan, SUV, MPV, dll
3. **Lainnya** - untuk truk, bus, pickup, dll

### Step 2: Filter Layanan Otomatis
- Pilih "Motor" → Hanya tampilkan layanan dengan `category='motor'`
- Pilih "Mobil" → Hanya tampilkan layanan dengan `category='mobil'`
- Pilih "Lainnya" → Hanya tampilkan layanan dengan `category='lainnya'`

## 🚀 Cara Test

### 1. Build & Run Aplikasi
```bash
cd c:\xampp\htdocs\carwashv2\carwashapk\washmanager_mobile
flutter run
```

### 2. Test Flow
1. Login ke aplikasi
2. Klik "Create New Order"
3. **Step 1**: Sekarang ada 3 pilihan kendaraan:
   - Motor (icon motor, warna orange)
   - Mobil (icon mobil, warna biru)
   - Lainnya (icon truk, warna hijau) ← BARU!
4. Pilih "Lainnya"
5. Klik "Continue"
6. **Step 2**: Hanya layanan dengan kategori "lainnya" yang muncul

### 3. Expected Result
✅ Ada 3 card pilihan kendaraan di Step 1
✅ Card "Lainnya" memiliki icon truk dan warna hijau
✅ Saat pilih "Lainnya", hanya layanan kategori "lainnya" yang muncul
✅ Badge kategori "lainnya" muncul di setiap card layanan
✅ Jika tidak ada layanan "lainnya", muncul pesan: "Tidak ada layanan untuk Lainnya"

## 📊 Mapping Kategori

| Pilihan User | Category Database | Icon | Warna |
|--------------|-------------------|------|-------|
| Motor | `motor` | motorcycle | Orange |
| Mobil | `mobil` | directions_car | Blue |
| Lainnya | `lainnya` | local_shipping | Green |

## 🎨 Tampilan UI

### Step 1 - Pilih Jenis Kendaraan
```
┌─────────────────────────────────────┐
│  🏍️  Motor                          │
│     Sepeda motor, scooter, dll      │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  🚗  Mobil                          │
│     Sedan, hatchback, SUV, MPV, dll │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  🚚  Lainnya                        │  ← BARU!
│     Truk, bus, pickup, dll          │
└─────────────────────────────────────┘
```

### Step 2 - Layanan Ter-filter
Jika user pilih "Lainnya", hanya tampilkan:
```
┌─────────────────────────────────────┐
│  ○ Cuci Truk Standard               │
│     Cuci eksterior truk/pickup      │
│     Rp 75.000 • 60 menit            │
│     [lainnya]                       │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│  ○ Cuci Bus Standard                │
│     Cuci eksterior bus              │
│     Rp 100.000 • 90 menit           │
│     [lainnya]                       │
└─────────────────────────────────────┘
```

## 🐛 Troubleshooting

### Issue: Card "Lainnya" tidak muncul
**Solusi:**
```bash
# Rebuild aplikasi
cd carwashapk/washmanager_mobile
flutter clean
flutter pub get
flutter run
```

### Issue: Filter tidak bekerja untuk "Lainnya"
**Solusi:**
1. Pastikan ada layanan dengan `category='lainnya'` di database
2. Cek API: `curl http://localhost/carwashv2/public/api/services?category=lainnya`
3. Restart aplikasi Flutter

### Issue: Icon tidak muncul
**Solusi:**
- Icon `Icons.local_shipping` adalah icon bawaan Flutter
- Tidak perlu import tambahan
- Jika tetap tidak muncul, coba icon lain seperti `Icons.fire_truck`

## ✅ Checklist Testing

### UI Testing
- [ ] Ada 3 card pilihan kendaraan di Step 1
- [ ] Card "Lainnya" memiliki icon truk
- [ ] Card "Lainnya" memiliki warna hijau
- [ ] Deskripsi "Truk, bus, pickup, dll" muncul

### Functional Testing
- [ ] Bisa pilih "Lainnya" di Step 1
- [ ] Bisa lanjut ke Step 2 setelah pilih "Lainnya"
- [ ] Layanan ter-filter dengan benar (hanya kategori "lainnya")
- [ ] Badge "lainnya" muncul di card layanan
- [ ] Pesan muncul jika tidak ada layanan "lainnya"

### Integration Testing
- [ ] Bisa create order dengan kategori "Lainnya"
- [ ] Data tersimpan dengan benar di database
- [ ] Order muncul di list orders
- [ ] Detail order menampilkan jenis kendaraan dengan benar

## 📝 Notes

- **Icon**: Menggunakan `Icons.local_shipping` (icon truk)
- **Warna**: Hijau (`Colors.green`)
- **Filter**: Otomatis bekerja dengan logic yang sudah ada
- **Backward Compatible**: Tidak mempengaruhi pilihan Motor dan Mobil

## 🎉 Selesai!

Aplikasi Flutter sekarang mendukung 3 kategori kendaraan:
- ✅ Motor
- ✅ Mobil
- ✅ Lainnya (BARU!)

Filter layanan otomatis bekerja untuk semua kategori.

**Happy Testing! 🚀**
