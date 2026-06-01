<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::where('is_active', true);
        
        // Filter by category if provided
        if ($request->has('category') && in_array($request->category, ['mobil', 'motor', 'lainnya'])) {
            $query->where('category', $request->category);
        }
        
        $services = $query->get();
        
        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $service
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'type' => 'required|in:standard,premium,detail',
            'category' => 'required|in:mobil,motor,lainnya'
        ]);

        $service = Service::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully',
            'data' => $service
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:1',
            'type' => 'required|in:standard,premium,detail',
            'category' => 'required|in:mobil,motor,lainnya'
        ]);

        $service->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully',
            'data' => $service
        ]);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => 'Service deactivated successfully'
        ]);
    }
}
