<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'percentage',
        'description',
        'is_active'
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    public static function getStaffCommission()
    {
        return self::where('name', 'default_staff_commission')
            ->where('is_active', true)
            ->first()?->percentage ?? 15.00;
    }

    public static function getOwnerCommission()
    {
        return self::where('name', 'owner_commission')
            ->where('is_active', true)
            ->first()?->percentage ?? 85.00;
    }
}