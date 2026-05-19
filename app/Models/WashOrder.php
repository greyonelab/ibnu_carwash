<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WashOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'service_id',
        'staff_id',
        'staff_ids',
        'user_id',
        'wash_lane_id',
        'queue_position',
        'order_number',
        'base_price',
        'additional_fee',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'started_at',
        'completed_at',
        'queued_at',
        'lane_started_at',
        'notes'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'additional_fee' => 'decimal:2',
        'total_price' => 'decimal:2',
        'staff_ids' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'queued_at' => 'datetime',
        'lane_started_at' => 'datetime'
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function staffMembers()
    {
        return $this->belongsToMany(Staff::class, 'wash_order_staff')
            ->withPivot('commission_percentage', 'commission_amount')
            ->withTimestamps();
    }

    public function getAllStaff()
    {
        // Return staff from pivot table if exists, otherwise fallback to single staff
        if ($this->staffMembers()->exists()) {
            return $this->staffMembers;
        }
        
        if ($this->staff) {
            return collect([$this->staff]);
        }
        
        return collect();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function washLane()
    {
        return $this->belongsTo(WashLane::class);
    }

    public function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $count = self::whereDate('created_at', now())->count() + 1;
        return 'WO' . $date . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function calculateCommissions()
    {
        $staffCommissionRate = CommissionSetting::getStaffCommission();
        $totalStaff = $this->getAllStaff()->count();
        
        if ($totalStaff === 0) {
            return [];
        }
        
        $commissionPerStaff = ($this->total_price * $staffCommissionRate / 100) / $totalStaff;
        $ownerCommission = $this->total_price * CommissionSetting::getOwnerCommission() / 100;
        
        return [
            'staff_commission_per_person' => $commissionPerStaff,
            'total_staff_commission' => $commissionPerStaff * $totalStaff,
            'owner_commission' => $ownerCommission,
            'staff_count' => $totalStaff
        ];
    }

    public function syncStaffMembers($staffIds)
    {
        if (empty($staffIds)) {
            return;
        }

        $commissions = $this->calculateCommissions();
        $syncData = [];
        
        foreach ($staffIds as $staffId) {
            $syncData[$staffId] = [
                'commission_percentage' => CommissionSetting::getStaffCommission() / count($staffIds),
                'commission_amount' => $commissions['staff_commission_per_person'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        $this->staffMembers()->sync($syncData);
        
        // Update staff_ids JSON field for backward compatibility
        $this->update(['staff_ids' => $staffIds]);
    }
}