@extends('layouts.app')

@section('title', 'Tambah Jalur Cuci - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('wash-lanes.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tambah Jalur Cuci</h1>
            <p class="text-slate-600">Buat jalur cuci baru untuk sistem antrian</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form action="{{ route('wash-lanes.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Jalur <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        placeholder="Jalur A, Jalur B, dll"
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
                        <option value="general" {{ old('type') == 'general' ? 'selected' : '' }}>General (Semua Kendaraan)</option>
                        <option value="motor" {{ old('type') == 'motor' ? 'selected' : '' }}>Khusus Motor</option>
                        <option value="mobil" {{ old('type') == 'mobil' ? 'selected' : '' }}>Khusus Mobil</option>
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
                    <input type="number" id="max_queue" name="max_queue" value="{{ old('max_queue', 10) }}" 
                        min="1" max="50" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('max_queue') border-red-500 @enderror">
                    <p class="mt-1 text-sm text-slate-500">Jumlah maksimal kendaraan yang bisa mengantri di jalur ini</p>
                    @error('max_queue')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-medium text-slate-700">Jalur Aktif</span>
                    </label>
                    <p class="ml-6 text-sm text-slate-500">Jalur yang aktif akan menerima antrian baru</p>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-slate-700 mb-2">
                    Deskripsi (Opsional)
                </label>
                <textarea id="description" name="description" rows="3"
                    placeholder="Deskripsi tambahan tentang jalur ini..."
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview -->
            <div class="bg-slate-50 rounded-lg p-4 border border-slate-200">
                <h3 class="text-sm font-medium text-slate-700 mb-3">Preview Jalur</h3>
                <div class="bg-white rounded-lg p-4 border border-slate-200">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold" id="preview-initial">A</span>
                        </div>
                        <div>
                            <div class="font-semibold text-slate-900" id="preview-name">Jalur A</div>
                            <div class="text-sm text-slate-600 capitalize" id="preview-type">general</div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">Kapasitas Antrian</span>
                        <span class="font-medium">0/<span id="preview-max">10</span></span>
                    </div>
                    <div class="w-full bg-slate-200 rounded-full h-2 mt-2">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full" style="width: 0%"></div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('wash-lanes.index') }}" 
                    class="px-4 py-2 text-slate-600 hover:text-slate-800 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan Jalur
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const typeSelect = document.getElementById('type');
    const maxQueueInput = document.getElementById('max_queue');
    
    const previewName = document.getElementById('preview-name');
    const previewType = document.getElementById('preview-type');
    const previewMax = document.getElementById('preview-max');
    const previewInitial = document.getElementById('preview-initial');
    
    function updatePreview() {
        const name = nameInput.value || 'Jalur A';
        const type = typeSelect.value || 'general';
        const maxQueue = maxQueueInput.value || '10';
        
        previewName.textContent = name;
        previewType.textContent = type;
        previewMax.textContent = maxQueue;
        previewInitial.textContent = name.slice(-1) || 'A';
    }
    
    nameInput.addEventListener('input', updatePreview);
    typeSelect.addEventListener('change', updatePreview);
    maxQueueInput.addEventListener('input', updatePreview);
});
</script>
@endsection