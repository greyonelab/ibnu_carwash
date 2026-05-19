@extends('layouts.app')

@section('title', 'Edit Pesanan - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('orders.show', $order) }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit Pesanan</h1>
            <p class="text-slate-600">{{ $order->order_number }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form action="{{ route('orders.update', $order) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Vehicle Information -->
            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Informasi Kendaraan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="license_plate" class="block text-sm font-medium text-slate-700 mb-2">
                            Plat Nomor <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="license_plate" name="license_plate" 
                            value="{{ old('license_plate', $order->vehicle->license_plate) }}" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('license_plate') border-red-500 @enderror">
                        @error('license_plate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="vehicle_type" class="block text-sm font-medium text-slate-700 mb-2">
                            Jenis Kendaraan <span class="text-red-500">*</span>
                        </label>
                        <select id="vehicle_type" name="vehicle_type" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vehicle_type') border-red-500 @enderror">
                            <option value="">Pilih Jenis</option>
                            <option value="Mobil" {{ old('vehicle_type', $order->vehicle->type) == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                            <option value="Motor" {{ old('vehicle_type', $order->vehicle->type) == 'Motor' ? 'selected' : '' }}>Motor</option>
                            <option value="Truk" {{ old('vehicle_type', $order->vehicle->type) == 'Truk' ? 'selected' : '' }}>Truk</option>
                        </select>
                        @error('vehicle_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="vehicle_model" class="block text-sm font-medium text-slate-700 mb-2">
                            Model/Merk
                        </label>
                        <input type="text" id="vehicle_model" name="vehicle_model" 
                            value="{{ old('vehicle_model', $order->vehicle->model) }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vehicle_model') border-red-500 @enderror">
                        @error('vehicle_model')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="vehicle_color" class="block text-sm font-medium text-slate-700 mb-2">
                            Warna
                        </label>
                        <input type="text" id="vehicle_color" name="vehicle_color" 
                            value="{{ old('vehicle_color', $order->vehicle->color) }}"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vehicle_color') border-red-500 @enderror">
                        @error('vehicle_color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Service Information -->
            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Informasi Layanan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="service_id" class="block text-sm font-medium text-slate-700 mb-2">
                            Layanan <span class="text-red-500">*</span>
                        </label>
                        <select id="service_id" name="service_id" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('service_id') border-red-500 @enderror">
                            <option value="">Pilih Layanan</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" 
                                    data-price="{{ $service->price }}"
                                    {{ old('service_id', $order->service_id) == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }} - Rp {{ number_format($service->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                        @error('service_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="additional_fee" class="block text-sm font-medium text-slate-700 mb-2">
                            Biaya Tambahan (Rp)
                        </label>
                        <input type="number" id="additional_fee" name="additional_fee" 
                            value="{{ old('additional_fee', $order->additional_fee) }}" min="0"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('additional_fee') border-red-500 @enderror">
                        @error('additional_fee')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Staff Assignment -->
            <div>
                <h3 class="text-lg font-semibold text-slate-900 mb-4">Penugasan Karyawan</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($staff as $member)
                        <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer">
                            <input type="checkbox" name="staff_ids[]" value="{{ $member->id }}"
                                {{ in_array($member->id, old('staff_ids', $order->staff_ids ?? [])) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            <div class="flex-1">
                                <p class="font-medium text-slate-900">{{ $member->name }}</p>
                                <p class="text-sm text-slate-600">{{ $member->position }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('staff_ids')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">
                    Catatan
                </label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror">{{ old('notes', $order->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('orders.show', $order) }}" 
                    class="px-4 py-2 text-slate-600 hover:text-slate-800 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Perbarui Pesanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection