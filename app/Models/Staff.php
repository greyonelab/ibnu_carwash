<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'position',
        'commission_rate',
        'is_active'
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public function washOrders()
    {
        return $this->hasMany(WashOrder::class);
    }

    public function washOrdersAsTeamMember()
    {
        return $this->belongsToMany(WashOrder::class, 'wash_order_staff')
            ->withPivot('commission_percentage', 'commission_amount')
            ->withTimestamps();
    }

    public function getAllWashOrders()
    {
        // Combine both single staff orders and team member orders
        $singleOrders = $this->washOrders()->get();
        $teamOrders = $this->washOrdersAsTeamMember()->get();
        
        return $singleOrders->merge($teamOrders)->unique('id');
    }

    public function getTotalCommissionAttribute()
    {
        // Commission from single staff orders
        $singleOrdersCommission = $this->washOrders()
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->sum('total_price') * ($this->commission_rate / 100);

        // Commission from team orders
        $teamOrdersCommission = $this->washOrdersAsTeamMember()
            ->where('status', 'completed')
            ->where('payment_status', 'paid')
            ->sum('wash_order_staff.commission_amount');

        return $singleOrdersCommission + $teamOrdersCommission;
    }
}
