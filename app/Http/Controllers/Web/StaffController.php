<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = Staff::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        // Filter by position
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }
        
        $staff = $query->orderBy('name')->get();
        return view('staff.index', compact('staff'));
    }

    public function create()
    {
        return view('staff.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'position' => 'required|string|max:100',
            'salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Staff::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'position' => $request->position,
            'salary' => $request->salary ?? 0,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Karyawan berhasil ditambahkan');
    }

    public function show(Staff $staff)
    {
        $staff->load(['washOrders' => function($query) {
            $query->with(['service', 'vehicle'])
                  ->orderBy('created_at', 'desc')
                  ->limit(10);
        }]);
        
        return view('staff.show', compact('staff'));
    }

    public function edit(Staff $staff)
    {
        return view('staff.edit', compact('staff'));
    }

    public function update(Request $request, Staff $staff)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'position' => 'required|string|max:100',
            'salary' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $staff->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'position' => $request->position,
            'salary' => $request->salary ?? 0,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('staff.index')
            ->with('success', 'Data karyawan berhasil diperbarui');
    }

    public function destroy(Staff $staff)
    {
        // Check if staff has orders
        if ($staff->washOrders()->count() > 0) {
            return redirect()->route('staff.index')
                ->with('error', 'Tidak dapat menghapus karyawan yang memiliki riwayat pesanan');
        }

        $staff->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Karyawan berhasil dihapus');
    }
}