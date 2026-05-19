<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WashLane;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WashLaneController extends Controller
{
    public function index()
    {
        $lanes = WashLane::withCount(['washOrders as current_queue' => function($query) {
            $query->whereIn('status', ['pending', 'in_progress']);
        }])->orderBy('name')->get();
        
        return view('wash-lanes.index', compact('lanes'));
    }

    public function create()
    {
        return view('wash-lanes.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:wash_lanes',
            'type' => 'required|string|in:general,motor,mobil',
            'max_queue' => 'required|integer|min:1|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        WashLane::create([
            'name' => $request->name,
            'type' => $request->type,
            'max_queue' => $request->max_queue,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('wash-lanes.index')
            ->with('success', 'Jalur cuci berhasil ditambahkan');
    }

    public function show(WashLane $washLane)
    {
        $washLane->load(['washOrders' => function($query) {
            $query->whereIn('status', ['pending', 'in_progress'])
                  ->with(['vehicle', 'service', 'staff'])
                  ->orderBy('queue_position');
        }]);
        
        return view('wash-lanes.show', compact('washLane'));
    }

    public function edit(WashLane $washLane)
    {
        return view('wash-lanes.edit', compact('washLane'));
    }

    public function update(Request $request, WashLane $washLane)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:wash_lanes,name,' . $washLane->id,
            'type' => 'required|string|in:general,motor,mobil',
            'max_queue' => 'required|integer|min:1|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $washLane->update([
            'name' => $request->name,
            'type' => $request->type,
            'max_queue' => $request->max_queue,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('wash-lanes.index')
            ->with('success', 'Jalur cuci berhasil diperbarui');
    }

    public function destroy(WashLane $washLane)
    {
        // Check if lane has active orders
        if ($washLane->washOrders()->whereIn('status', ['pending', 'in_progress'])->count() > 0) {
            return redirect()->route('wash-lanes.index')
                ->with('error', 'Tidak dapat menghapus jalur yang masih memiliki antrian aktif');
        }

        $washLane->delete();

        return redirect()->route('wash-lanes.index')
            ->with('success', 'Jalur cuci berhasil dihapus');
    }
}