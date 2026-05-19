<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran - {{ $order->order_number }}</title>
    
    <style>
        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: white;
        }
        
        .receipt {
            max-width: 300px;
            margin: 0 auto;
            background: white;
            padding: 0;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .logo {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .address {
            font-size: 10px;
            line-height: 1.2;
        }
        
        .section {
            margin-bottom: 15px;
        }
        
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }
        
        .divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }
        
        .total-row {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #000;
            padding-top: 5px;
        }
        
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }
        
        .status {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin: 10px 0;
        }
        
        .paid { color: #059669; }
        .unpaid { color: #dc2626; }
        
        @media print {
            body { margin: 0; padding: 10px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <!-- Header -->
        <div class="header">
            <div class="logo">WASHMANAGER PRO</div>
            <div class="address">
                Jl. Cuci Mobil No. 123<br>
                Jakarta Selatan 12345<br>
                Telp: (021) 1234-5678
            </div>
        </div>

        <!-- Order Info -->
        <div class="section">
            <div class="row">
                <span>No. Pesanan:</span>
                <span>{{ $order->order_number }}</span>
            </div>
            <div class="row">
                <span>Tanggal:</span>
                <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="row">
                <span>Kasir:</span>
                <span>{{ $order->user->name }}</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Vehicle Info -->
        <div class="section">
            <div class="row">
                <span>Plat Nomor:</span>
                <span><strong>{{ $order->vehicle->license_plate }}</strong></span>
            </div>
            <div class="row">
                <span>Kendaraan:</span>
                <span>{{ $order->vehicle->type }}</span>
            </div>
            @if($order->vehicle->model)
            <div class="row">
                <span>Model:</span>
                <span>{{ $order->vehicle->model }}</span>
            </div>
            @endif
            @if($order->vehicle->color)
            <div class="row">
                <span>Warna:</span>
                <span>{{ $order->vehicle->color }}</span>
            </div>
            @endif
        </div>

        <div class="divider"></div>

        <!-- Service Details -->
        <div class="section">
            <div class="row">
                <span>Layanan:</span>
                <span>{{ $order->service->name }}</span>
            </div>
            <div class="row">
                <span>Petugas:</span>
                <span>{{ $order->staff->name }}</span>
            </div>
            <div class="row">
                <span>Durasi:</span>
                <span>{{ $order->service->duration_minutes }} menit</span>
            </div>
        </div>

        <div class="divider"></div>

        <!-- Price Breakdown -->
        <div class="section">
            <div class="row">
                <span>Harga Layanan:</span>
                <span>Rp {{ number_format($order->base_price, 0, ',', '.') }}</span>
            </div>
            @if($order->additional_fee > 0)
            <div class="row">
                <span>Biaya Tambahan:</span>
                <span>Rp {{ number_format($order->additional_fee, 0, ',', '.') }}</span>
            </div>
            @endif
            
            <div class="divider"></div>
            
            <div class="row total-row">
                <span>TOTAL:</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="status {{ $order->payment_status === 'paid' ? 'paid' : 'unpaid' }}">
            @if($order->payment_status === 'paid')
                ✓ LUNAS
                @if($order->payment_method)
                    <br><small>({{ strtoupper($order->payment_method) }})</small>
                @endif
            @else
                ⚠ BELUM BAYAR
            @endif
        </div>

        @if($order->notes)
        <div class="divider"></div>
        <div class="section">
            <strong>Catatan:</strong><br>
            {{ $order->notes }}
        </div>
        @endif

        <!-- Timeline -->
        <div class="divider"></div>
        <div class="section">
            <div style="font-size: 10px;">
                <div>Dibuat: {{ $order->created_at->format('d/m/Y H:i') }}</div>
                @if($order->started_at)
                <div>Mulai: {{ $order->started_at->format('d/m/Y H:i') }}</div>
                @endif
                @if($order->completed_at)
                <div>Selesai: {{ $order->completed_at->format('d/m/Y H:i') }}</div>
                @endif
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <div>Terima kasih atas kepercayaan Anda!</div>
            <div>Kendaraan bersih, hati senang</div>
            <div style="margin-top: 10px;">
                <strong>{{ config('app.name') }}</strong><br>
                www.washmanagerpro.com
            </div>
        </div>
    </div>

    <!-- Print Controls -->
    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="printReceipt()" style="background: #3b82f6; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-right: 10px;">
            🖨️ Print Ulang
        </button>
        <button onclick="closeWindow()" style="background: #6b7280; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            ✕ Tutup
        </button>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() { 
            window.print(); 
        }

        // Close window after print
        window.onafterprint = function() {
            window.close();
        }
        
        function printReceipt() {
            window.print();
        }
        
        function closeWindow() {
            window.close();
        }
    </script>
</body>
</html>