<?php

namespace App\Exports;

use App\Models\WashOrder;
use App\Models\Staff;
use App\Models\Service;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class ReportsExport implements WithMultipleSheets
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ? Carbon::parse($startDate) : Carbon::now()->startOfMonth();
        $this->endDate = $endDate ? Carbon::parse($endDate) : Carbon::now();
    }

    public function sheets(): array
    {
        return [
            new RevenueSheet($this->startDate, $this->endDate),
            new StaffPerformanceSheet($this->startDate, $this->endDate),
            new ServiceAnalyticsSheet($this->startDate, $this->endDate),
        ];
    }
}

class RevenueSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return WashOrder::with(['vehicle', 'service', 'staff'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('payment_status', 'paid')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'No. Pesanan',
            'Plat Nomor',
            'Layanan',
            'Petugas',
            'Total Harga',
            'Komisi',
            'Metode Pembayaran'
        ];
    }

    public function map($order): array
    {
        $commission = $order->total_price * ($order->staff->commission_rate / 100);
        
        return [
            $order->created_at->format('d/m/Y'),
            $order->order_number,
            $order->vehicle->license_plate,
            $order->service->name,
            $order->staff->name,
            $order->total_price,
            $commission,
            $order->payment_method ? strtoupper($order->payment_method) : '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Laporan Pendapatan';
    }
}

class StaffPerformanceSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Staff::with(['washOrders' => function($query) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate])
                  ->where('payment_status', 'paid');
        }])->get();
    }

    public function headings(): array
    {
        return [
            'Nama Karyawan',
            'Posisi',
            'Rate Komisi (%)',
            'Total Pesanan',
            'Total Revenue',
            'Total Komisi',
            'Rata-rata per Pesanan'
        ];
    }

    public function map($staff): array
    {
        $orders = $staff->washOrders;
        $totalRevenue = $orders->sum('total_price');
        $totalCommission = $totalRevenue * ($staff->commission_rate / 100);
        $avgPerOrder = $orders->count() > 0 ? $totalRevenue / $orders->count() : 0;
        
        return [
            $staff->name,
            $staff->position,
            $staff->commission_rate,
            $orders->count(),
            $totalRevenue,
            $totalCommission,
            $avgPerOrder
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Performa Karyawan';
    }
}

class ServiceAnalyticsSheet implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate, $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return Service::with(['washOrders' => function($query) {
            $query->whereBetween('created_at', [$this->startDate, $this->endDate])
                  ->where('payment_status', 'paid');
        }])->get();
    }

    public function headings(): array
    {
        return [
            'Nama Layanan',
            'Tipe',
            'Harga',
            'Durasi (menit)',
            'Total Pesanan',
            'Total Revenue',
            'Rata-rata per Hari'
        ];
    }

    public function map($service): array
    {
        $orders = $service->washOrders;
        $totalRevenue = $orders->sum('total_price');
        $days = $this->startDate->diffInDays($this->endDate) + 1;
        $avgPerDay = $days > 0 ? $orders->count() / $days : 0;
        
        return [
            $service->name,
            ucfirst($service->type),
            $service->price,
            $service->duration_minutes,
            $orders->count(),
            $totalRevenue,
            round($avgPerDay, 2)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Analisis Layanan';
    }
}