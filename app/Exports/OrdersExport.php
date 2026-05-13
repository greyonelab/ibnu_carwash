<?php

namespace App\Exports;

use App\Models\WashOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class OrdersExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $this->endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
    }

    public function collection()
    {
        return WashOrder::with(['vehicle', 'service', 'staff', 'user'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'No. Pesanan',
            'Tanggal',
            'Plat Nomor',
            'Jenis Kendaraan',
            'Model',
            'Layanan',
            'Petugas',
            'Harga Dasar',
            'Biaya Tambahan',
            'Total Harga',
            'Status',
            'Status Pembayaran',
            'Metode Pembayaran',
            'Kasir',
            'Mulai',
            'Selesai',
            'Catatan'
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->created_at->format('d/m/Y H:i'),
            $order->vehicle->license_plate,
            $order->vehicle->type,
            $order->vehicle->model ?? '-',
            $order->service->name,
            $order->staff->name,
            $order->base_price,
            $order->additional_fee,
            $order->total_price,
            $this->getStatusLabel($order->status),
            $this->getPaymentStatusLabel($order->payment_status),
            $order->payment_method ? strtoupper($order->payment_method) : '-',
            $order->user->name,
            $order->started_at ? $order->started_at->format('d/m/Y H:i') : '-',
            $order->completed_at ? $order->completed_at->format('d/m/Y H:i') : '-',
            $order->notes ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu',
            'in_progress' => 'Proses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];
        return $labels[$status] ?? $status;
    }

    private function getPaymentStatusLabel($status)
    {
        $labels = [
            'unpaid' => 'Belum Bayar',
            'paid' => 'Lunas'
        ];
        return $labels[$status] ?? $status;
    }
}