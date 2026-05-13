<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'license_plate',
        'type',
        'model',
        'color'
    ];

    public function washOrders()
    {
        return $this->hasMany(WashOrder::class);
    }
}
