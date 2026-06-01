<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WashOrder;
use App\Models\Vehicle;
use App\Models\Service;
use App\Models\Staff;
use App\Models\WashLane;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WashOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = WashOrder::with(['vehicle', 'service', 'staff', 'staffMembers', 'user']);
        
        // Filter berdasarkan status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter berdasarkan tanggal
        if ($request->has('date')) {
            $query->whereDate('created_at', $request->date);
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Add staff information and commission data to each order
        $orders->getCollection()->transform(function ($order) {
            $allStaff = $order->getAllStaff();
            $commissions = $order->calculateCommissions();
            
            $order->all_staff = $allStaff;
            $order->commission_breakdown = $commissions;
            
            return $order;
        });
        
        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'license_plate' => 'required|string',
            'vehicle_type' => 'required|string',
            'vehicle_model' => 'nullable|string',
            'vehicle_color' => 'nullable|string',
            'service_id' => 'required|exists:services,id',
            'staff_ids' => 'required|array|min:1',
            'staff_ids.*' => 'exists:staff,id',
            'wash_lane_id' => 'nullable|exists:wash_lanes,id',
            'additional_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'payment_method' => 'nullable|in:cash,qris,transfer',
            'auto_complete' => 'nullable|boolean'
        ]);

        return DB::transaction(function () use ($request) {
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

            // Handle wash lane assignment
            $washLane = null;
            $queuePosition = null;

            if ($request->wash_lane_id) {
                $washLane = WashLane::find($request->wash_lane_id);
                if ($washLane && $washLane->canAcceptOrder()) {
                    $queuePosition = $washLane->next_queue_position;
                } else {
                    $washLane = null;
                }
            }

            if (!$washLane) {
                $washLane = WashLane::getAvailableLane($request->vehicle_type);
                if ($washLane) {
                    $queuePosition = $washLane->next_queue_position;
                }
            }

            // Tentukan status berdasarkan auto_complete
            $autoComplete = filter_var($request->auto_complete, FILTER_VALIDATE_BOOLEAN);
            $status = $autoComplete ? 'completed' : 'pending';
            $paymentStatus = $request->payment_method ? 'paid' : 'unpaid';
            $completedAt = $autoComplete ? now() : null;
            $startedAt = $autoComplete ? now() : null;

            $washOrder = WashOrder::create([
                'vehicle_id' => $vehicle->id,
                'service_id' => $service->id,
                'staff_id' => $request->staff_ids[0], // Keep first staff for backward compatibility
                'staff_ids' => $request->staff_ids,
                'user_id' => auth()->id(),
                'wash_lane_id' => $washLane?->id,
                'queue_position' => $queuePosition,
                'order_number' => $orderNumber,
                'base_price' => $service->price,
                'additional_fee' => $additionalFee,
                'total_price' => $totalPrice,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $request->payment_method,
                'started_at' => $startedAt,
                'completed_at' => $completedAt,
                'queued_at' => $autoComplete ? null : now(),
                'notes' => $request->notes
            ]);

            // Sync staff members with commission calculation
            $washOrder->syncStaffMembers($request->staff_ids);

            $washOrder->load(['vehicle', 'service', 'staff', 'staffMembers', 'user']);

            return response()->json([
                'success' => true,
                'message' => 'Wash order created successfully',
                'data' => $washOrder
            ], 201);
        });
    }

    public function show($id)
    {
        $washOrder = WashOrder::with(['vehicle', 'service', 'staff', 'staffMembers', 'user'])
            ->findOrFail($id);
        
        // Add staff information and commission data
        $washOrder->all_staff = $washOrder->getAllStaff();
        $washOrder->commission_breakdown = $washOrder->calculateCommissions();
        
        return response()->json([
            'success' => true,
            'data' => $washOrder
        ]);
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
        $washOrder->load(['vehicle', 'service', 'staff', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => $washOrder
        ]);
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
        
        $washOrder->load(['vehicle', 'service', 'staff', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'data' => $washOrder
        ]);
    }

    private function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $count = WashOrder::whereDate('created_at', now())->count() + 1;
        return 'WO' . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
