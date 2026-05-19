<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WashLane extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'is_active',
        'max_queue',
        'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_queue' => 'integer'
    ];

    public function washOrders()
    {
        return $this->hasMany(WashOrder::class);
    }

    public function currentQueue()
    {
        return $this->washOrders()
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('queue_position');
    }

    public function getQueueCountAttribute()
    {
        return $this->currentQueue()->count();
    }

    public function getNextQueuePositionAttribute()
    {
        $lastPosition = $this->washOrders()
            ->whereIn('status', ['pending', 'in_progress'])
            ->max('queue_position');
        
        return ($lastPosition ?? 0) + 1;
    }

    public function canAcceptOrder()
    {
        return $this->is_active && $this->queue_count < $this->max_queue;
    }

    public static function getAvailableLane($vehicleType = null)
    {
        $query = self::where('is_active', true);
        
        // Prioritas jalur berdasarkan jenis kendaraan
        if ($vehicleType) {
            $query->where(function($q) use ($vehicleType) {
                $q->where('type', strtolower($vehicleType))
                  ->orWhere('type', 'general');
            });
        }
        
        return $query->get()
            ->filter(function($lane) {
                return $lane->canAcceptOrder();
            })
            ->sortBy('queue_count')
            ->first();
    }
}