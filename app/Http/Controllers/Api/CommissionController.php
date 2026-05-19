<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommissionSetting;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $commissionSettings = CommissionSetting::where('is_active', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => [
                'staff_commission' => CommissionSetting::getStaffCommission(),
                'owner_commission' => CommissionSetting::getOwnerCommission(),
                'settings' => $commissionSettings
            ]
        ]);
    }
}