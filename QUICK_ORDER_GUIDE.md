# Quick Order & Payment Guide

Panduan lengkap untuk fitur pembuatan pesanan cepat dengan pembayaran dan print struk otomatis.

## 🚀 Fitur Baru: Buat & Bayar Pesanan

### **1. Workflow Pesanan dengan Pembayaran**

#### **Opsi 1: Pesanan Biasa (Tanpa Pembayaran)**
```
Create Order → Pending Status → Dashboard
```
- Pesanan dibuat dengan status "pending"
- Pembayaran dilakukan nanti
- Redirect ke dashboard

#### **Opsi 2: Pesanan dengan Pembayaran Langsung**
```
Create Order + Payment → Auto Print Receipt → Dashboard
```
- Pesanan dibuat dengan status "in_progress" atau "completed"
- Pembayaran langsung diproses
- Auto print struk
- Redirect ke dashboard setelah print

### **2. Form Create Order - Fitur Baru**

#### **Payment Method Selection**
- **Cash**: Pembayaran tunai
- **QRIS**: Scan QR Code
- **Transfer**: Bank Transfer

#### **Auto Complete Option**
- ☑️ **Tandai sebagai selesai**: Untuk cuci express yang langsung selesai
- Mengubah status dari "in_progress" ke "completed"
- Cocok untuk layanan cepat (15-30 menit)

#### **Dynamic Button Text**
- **Default**: "Buat Pesanan"
- **With Payment**: "Buat & Bayar Pesanan"
- **With Payment + Auto Complete**: "Buat, Bayar & Print Struk"

### **3. Quick Order Modal (Dashboard)**

#### **Cuci Express Button**
- Modal popup untuk pesanan cepat
- Pre-filled dengan layanan standar
- Hanya perlu input plat nomor dan metode pembayaran
- Langsung auto-complete dan print struk

#### **Fields:**
- **Plat Nomor**: Input manual
- **Jenis Kendaraan**: Auto "Sedan"
- **Layanan**: Auto "Cuci Standar"
- **Petugas**: Auto staff pertama
- **Status**: Auto "completed"

### **4. Receipt System**

#### **Auto Print Features**
- Print otomatis saat ada session `auto_print`
- Tombol "Selesai & Kembali" untuk redirect ke dashboard
- Konfirmasi setelah print selesai

#### **Receipt Content**
- Header dengan logo dan alamat
- Detail pesanan lengkap
- Breakdown harga
- Status pembayaran
- Timeline (dibuat, mulai, selesai)
- Footer dengan terima kasih

### **5. Status Flow Management**

#### **Tanpa Pembayaran**
```
pending → (manual update) → in_progress → completed
```

#### **Dengan Pembayaran**
```
paid + in_progress → (manual complete) → completed
```

#### **Dengan Pembayaran + Auto Complete**
```
paid + completed (langsung selesai)
```

## 🎯 User Experience Flow

### **Scenario 1: Cuci Reguler**
1. Klik "Pesanan Baru" di dashboard
2. Isi form lengkap
3. **Jangan pilih** metode pembayaran
4. Klik "Buat Pesanan"
5. Redirect ke dashboard
6. Pesanan masuk antrian dengan status "pending"

### **Scenario 2: Cuci dengan Pembayaran**
1. Klik "Pesanan Baru" di dashboard
2. Isi form lengkap
3. **Pilih metode pembayaran** (Cash/QRIS/Transfer)
4. Klik "Buat & Bayar Pesanan"
5. Popup struk dengan auto-print
6. Klik "Selesai & Kembali"
7. Redirect ke dashboard

### **Scenario 3: Cuci Express (Quick Order)**
1. Klik "Cuci Express" di dashboard
2. Input plat nomor
3. Pilih metode pembayaran
4. Klik "Buat & Print"
5. Auto-print struk
6. Redirect ke dashboard
7. Pesanan langsung selesai

## 🔧 Technical Implementation

### **Controller Logic**
```php
// Determine status based on payment and auto_complete
$status = 'pending';
$paymentStatus = 'unpaid';

if ($request->payment_method) {
    $paymentStatus = 'paid';
    if ($request->auto_complete) {
        $status = 'completed';
        $startedAt = now();
        $completedAt = now();
    } else {
        $status = 'in_progress';
        $startedAt = now();
    }
}
```

### **Session Management**
```php
return redirect()->route('orders.receipt', $order->id)
    ->with('success', 'Pesanan berhasil dibuat dan dibayar!')
    ->with('auto_print', true)
    ->with('redirect_to_dashboard', true);
```

### **JavaScript Auto Print**
```javascript
@if(session('auto_print'))
window.onload = function() { 
    window.print(); 
}
@endif
```

## 📱 Mobile Responsive

### **Touch-Friendly Design**
- Large buttons untuk payment methods
- Easy-to-tap radio buttons
- Optimized modal size untuk mobile
- Swipe gestures support

### **Mobile-Specific Features**
- Auto-zoom prevention pada input fields
- Touch feedback pada buttons
- Optimized keyboard layout
- Fast tap response

## 🎨 Visual Feedback

### **Payment Method Selection**
- **Cash**: Green border dan background
- **QRIS**: Blue border dan background  
- **Transfer**: Purple border dan background
- Smooth transitions dan hover effects

### **Button States**
- **Default**: Blue "Buat Pesanan"
- **With Payment**: Green "Buat & Bayar Pesanan"
- **Loading**: Spinner + "Memproses..."
- **Disabled**: Prevent double submission

### **Form Validation**
- Real-time price calculation
- Required field indicators
- Error messages dengan styling
- Success feedback

## 🚀 Performance Optimizations

### **Fast Order Creation**
- Minimal required fields untuk quick order
- Pre-filled defaults untuk speed
- Auto-complete untuk frequent customers
- Cached staff dan service data

### **Print Optimization**
- Lightweight receipt HTML
- Optimized untuk thermal printers
- Fast rendering dengan minimal CSS
- Auto-close after print

### **Database Efficiency**
- Single transaction untuk order creation
- Efficient vehicle lookup/creation
- Optimized queries dengan eager loading
- Proper indexing pada search fields

## 📊 Analytics & Tracking

### **Order Metrics**
- Track payment method usage
- Monitor auto-complete vs manual flow
- Measure time-to-completion
- Customer satisfaction scores

### **Performance Metrics**
- Order creation speed
- Print success rate
- User flow completion
- Error rates per scenario

## 🔒 Security Considerations

### **Payment Security**
- No sensitive payment data stored
- Secure session management
- CSRF protection pada forms
- Input validation dan sanitization

### **Access Control**
- Role-based permissions
- Audit trail untuk orders
- Secure receipt URLs
- Session timeout management

## 🛠 Troubleshooting

### **Common Issues**

#### **Print Tidak Muncul**
- Check browser print permissions
- Verify printer connection
- Test dengan Ctrl+P manual
- Check print CSS media queries

#### **Redirect Tidak Bekerja**
- Clear browser cache
- Check session configuration
- Verify route definitions
- Test dengan incognito mode

#### **Form Tidak Submit**
- Check JavaScript errors
- Verify CSRF token
- Check network connectivity
- Validate required fields

### **Debug Commands**
```bash
# Check routes
php artisan route:list --name=orders

# Check sessions
php artisan tinker
session()->all()

# Check logs
tail -f storage/logs/laravel.log
```

---

**Fitur Quick Order & Payment siap digunakan! 🎉**

Workflow yang lebih efisien untuk operasional car wash harian.