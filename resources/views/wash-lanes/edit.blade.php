@extends('layouts.app')

@section('title', 'Edit Jalur Cuci - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('wash-lanes.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Edit Jalur Cuci</h1>
            <p class="text-slate-600">Perbarui pengaturan {{ $washLane->name }}</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form action="{{ route('wash-lanes.update', $washLane) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Jalur <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $washLane->name) }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Type -->
                <div>
                    <label for="type" class="block text-sm font-medium text-slate-700 mb-2">
                        Jenis Jalur <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                        <option value="">Pilih Jenis</option>
                        <option value="general" {{ old('type', $washLane->type) == 'general' ? 'selected' : '' }}>General (Semua Kendaraan)</option>
                        <option value="motor" {{ old('type', $washLane->type) == 'motor' ? 'selected' : '' }}>Khusus Motor</option>
                        <option value="mobil" {{ old('type', $washLane->type) == 'mobil' ? 'selected' : '' }}>Khusus Mobil</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Max Queue -->
                <div>
                    <label for="max_queue" class="block text-sm font-medium text-slate-700 mb-2">
                        Maksimal Antrian <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="max_queue" name="max_queue" value="{{ old('max_queue', $washLane->max_queue) }}" 
                        min="1" max="50" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('max_queue') border-red-500 @enderror">
                    @error('max_queue')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $washLane->is_active) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-slate-700">Jalur Aktif</span>
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                    Deskripsi (Opsional)
                </label>
                <textarea id="description" name="description" rows="3"
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $washLane->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('wash-lanes.index') }}" 
                    class="px-4 py-2 text-slate-600 hover:text-slate-800 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Perbarui Jalur
                </button>
            </div>
        </form>
    </div>
</div>
@endsection