<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WashOrder;
use App\Models\Vehicle;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WashOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = WashOrder::with(['vehicle', 'service', 'staff', 'user']);
        
        // Filter berdasarkan status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan tanggal
        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }
        
        // Search
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('vehicle', function($q) use ($search) {
                $q->where('license_plate', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            })->orWhere('order_number', 'like', "%{$search}%");
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        
        return view('orders.index', compact('orders'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)->get();
        $staff = Staff::where('is_active', true)->get();
        
        return view('orders.create', compact('services', 'staff'));
    }

    public function store(Request $request)
    {
        // Debug logging
        \Log::info('Order creation started', [
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);

        $request->validate([
            'license_plate' => 'required|string',
            'vehicle_type' => 'required|string',
            'vehicle_model' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'service_id' => 'required|exists:services,id',
            'staff_ids' => 'required|array|min:1',
            'staff_ids.*' => 'exists:staff,id',
            'additional_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,qris,transfer',
            'auto_complete' => 'nullable|boolean',
            'redirect_to_create' => 'nullable|boolean'
        ]);

        try {
            $order = DB::transaction(function () use ($request) {
                // Cari atau buat kendaraan
                $vehicle = Vehicle::firstOrCreate(
                    ['license_plate' => strtoupper($request->license_plate)],
                    [
                        'type' => $request->vehicle_type,
                        'model' => $request->vehicle_model,
                        'color' => $request->vehicle_color
                    ]
                );

                $service = Service::findOrFail($request->service_id);
                $additionalFee = $request->additional_fee ?? 0;
                $totalPrice = $service->price + $additionalFee;

                // Generate order number
                $orderNumber = $this->generateOrderNumber();

                // Determine status based on payment and auto_complete
                $status = 'pending';
                $paymentStatus = 'unpaid';
                $startedAt = null;
                $completedAt = null;

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
                } elseif ($request->auto_complete) {
                    $status = 'completed';
                    $startedAt = now();
                    $completedAt = now();
                }

                $washOrder = WashOrder::create([
                    'vehicle_id' => $vehicle->id,
                    'service_id' => $service->id,
                    'staff_id' => $request->staff_ids[0], // Keep first staff for backward compatibility
                    'staff_ids' => $request->staff_ids,
                    'user_id' => auth()->id(),
                    'order_number' => $orderNumber,
                    'base_price' => $service->price,
                    'additional_fee' => $additionalFee,
                    'total_price' => $totalPrice,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $request->payment_method,
                    'started_at' => $startedAt,
                    'completed_at' => $completedAt,
                    'notes' => $request->notes
                ]);

                // Sync staff members with commission calculation
                $washOrder->syncStaffMembers($request->staff_ids);

                \Log::info('Order created successfully', [
                    'order_id' => $washOrder->id,
                    'order_number' => $washOrder->order_number
                ]);

                return $washOrder;
            });

            // Redirect based on payment method and source
            if ($request->payment_method) {
                // Jika ada pembayaran, redirect ke orders index dengan notifikasi dan buka tab print
                \Log::info('Order created with payment, returning to orders index', ['order_id' => $order->id]);
                return redirect()->route('orders.index')
                    ->with('success', 'Pesanan berhasil dibuat dan dibayar! Struk sedang dicetak.')
                    ->with('print_order_id', $order->id)
                    ->with('open_print_tab', true);
            }

            // Jika tidak ada pembayaran, redirect ke orders index
            \Log::info('Redirecting to orders index', ['order_id' => $order->id]);
            return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat!');

        } catch (\Exception $e) {
            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat membuat pesanan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $order = WashOrder::with(['vehicle', 'service', 'staff', 'staffMembers', 'user'])->findOrFail($id);
        return view('orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed,cancelled'
        ]);

        $washOrder = WashOrder::findOrFail($id);
        
        $updateData = ['status' => $request->status];
        
        if ($request->status === 'in_progress' && !$washOrder->started_at) {
            $updateData['started_at'] = now();
        }
        
        if ($request->status === 'completed' && !$washOrder->completed_at) {
            $updateData['completed_at'] = now();
        }
        
        $washOrder->update($updateData);

        return redirect()->back()->with('success', 'Status pesanan berhasil diupdate!');
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid',
            'payment_method' => 'required_if:payment_status,paid|in:cash,qris,transfer'
        ]);

        $washOrder = WashOrder::findOrFail($id);
        
        $washOrder->update([
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method
        ]);

        return redirect()->back()->with('success', 'Status pembayaran berhasil diupdate!');
    }

    public function receipt($id)
    {
        $order = WashOrder::with(['vehicle', 'service', 'staff', 'user'])->findOrFail($id);
        return view('orders.receipt', compact('order'));
    }

    private function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $count = WashOrder::whereDate('created_at', now())->count() + 1;
        return 'WO' . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
