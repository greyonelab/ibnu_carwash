@extends('layouts.app')

@section('title', 'Edit Layanan - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('services.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit Layanan</h1>
            <p class="text-slate-600">Perbarui layanan {{ $service->name }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form action="{{ route('services.update', $service) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Layanan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $service->name) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-medium text-slate-700 mb-2">
                        Kategori Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select id="category" name="category" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category') border-red-500 @enderror">
                        <option value="">Pilih Kategori</option>
                        <option value="mobil" {{ old('category', $service->category) == 'mobil' ? 'selected' : '' }}>Mobil</option>
                        <option value="motor" {{ old('category', $service->category) == 'motor' ? 'selected' : '' }}>Motor</option>
                        <option value="lainnya" {{ old('category', $service->category) == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-slate-700 mb-2">
                        Tipe Layanan <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">Pilih Tipe</option>
                        <option value="standard" {{ old('type', $service->type) == 'standard' ? 'selected' : '' }}>Standard</option>
                        <option value="premium" {{ old('type', $service->type) == 'premium' ? 'selected' : '' }}>Premium</option>
                        <option value="detail" {{ old('type', $service->type) == 'detail' ? 'selected' : '' }}>Detail</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-slate-700 mb-2">
                        Harga (Rp) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="price" name="price" value="{{ old('price', $service->price) }}" min="0" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration -->
                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-slate-700 mb-2">
                        Durasi (Menit) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="duration_minutes" name="duration_minutes" value="{{ old('duration_minutes', $service->duration_minutes) }}" min="1" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('duration_minutes') border-red-500 @enderror">
                    @error('duration_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $service->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status -->
            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $service->is_active) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-medium text-slate-700">Aktif</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('services.index') }}" 
                    class="px-4 py-2 text-slate-600 hover:text-slate-800 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Perbarui Layanan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection