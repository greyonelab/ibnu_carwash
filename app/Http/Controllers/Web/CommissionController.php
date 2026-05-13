<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CommissionSetting;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {
        $commissionSettings = CommissionSetting::where('is_active', true)->get();
        
        return view('commission.index', compact('commissionSettings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'staff_commission' => 'required|numeric|min:0|max:100',
            'owner_commission' => 'required|numeric|min:0|max:100',
        ]);

        // Validate that total doesn't exceed 100%
        if ($request->staff_commission + $request->owner_commission != 100) {
            return back()->withErrors(['error' => 'Total commission must equal 100%']);
        }

        // Update staff commission
        CommissionSetting::where('name', 'default_staff_commission')->update([
            'percentage' => $request->staff_commission
        ]);

        // Update owner commission
        CommissionSetting::where('name', 'owner_commission')->update([
            'percentage' => $request->owner_commission
        ]);

        return back()->with('success', 'Commission settings updated successfully!');
    }
}