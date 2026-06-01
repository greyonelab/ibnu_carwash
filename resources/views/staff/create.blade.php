@extends('layouts.app')

@section('title', 'Tambah Karyawan - WashManager Pro')

@section('content')
<div class="p-6 space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('staff.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tambah Karyawan</h1>
            <p class="text-slate-600">Tambahkan karyawan baru ke sistem</p>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl border border-slate-200 p-6">
        <form action="{{ route('staff.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Position -->
                <div>
                    <label for="position_select" class="block text-sm font-medium text-slate-700 mb-2">
                        Posisi <span class="text-red-500">*</span>
                    </label>
                    @php
                        $predefinedPositions = ['Washer', 'Detailer', 'Supervisor', 'Manager'];
                        $oldPosition = old('position', '');
                        $isCustom = $oldPosition !== '' && !in_array($oldPosition, $predefinedPositions);
                    @endphp
                    <select id="position_select" onchange="handlePositionChange(this)"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror">
                        <option value="">Pilih Posisi</option>
                        <option value="Washer" {{ $oldPosition == 'Washer' ? 'selected' : '' }}>Washer</option>
                        <option value="Detailer" {{ $oldPosition == 'Detailer' ? 'selected' : '' }}>Detailer</option>
                        <option value="Supervisor" {{ $oldPosition == 'Supervisor' ? 'selected' : '' }}>Supervisor</option>
                        <option value="Manager" {{ $oldPosition == 'Manager' ? 'selected' : '' }}>Manager</option>
                        <option value="__custom__" {{ $isCustom ? 'selected' : '' }}>+ Ketik Posisi Lain...</option>
                    </select>
                    <input type="text" id="position_custom" name="position"
                        value="{{ $oldPosition }}"
                        placeholder="Tulis posisi kustom..."
                        class="{{ $isCustom ? '' : 'hidden' }} mt-2 w-full px-3 py-2 border border-blue-400 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('position') border-red-500 @enderror"
                        {{ $isCustom ? 'required' : '' }}>
                    {{-- Hidden input untuk posisi dari dropdown --}}
                    <input type="hidden" id="position_hidden" name="position"
                        value="{{ $isCustom ? '' : $oldPosition }}"
                        {{ $isCustom ? 'disabled' : '' }}>
                    @error('position')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-2">
                        Nomor Telepon
                    </label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                        Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Commission Rate -->
                <div>
                    <label for="commission_rate" class="block text-sm font-medium text-slate-700 mb-2">
                        Persentase Komisi (%)
                    </label>
                    <div class="relative">
                        <input type="number" id="commission_rate" name="commission_rate"
                            value="{{ old('commission_rate', 15) }}"
                            min="0" max="100" step="0.5"
                            class="w-full px-3 py-2 pr-10 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('commission_rate') border-red-500 @enderror">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 font-medium text-sm">%</span>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">Persentase komisi dari total harga pesanan yang dikerjakan</p>
                    @error('commission_rate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Salary -->
                <div>
                    <label for="salary" class="block text-sm font-medium text-slate-700 mb-2">
                        Gaji Pokok (Rp)
                    </label>
                    <input type="number" id="salary" name="salary" value="{{ old('salary') }}" min="0"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('salary') border-red-500 @enderror">
                    @error('salary')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div class="flex items-center pt-6">
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <div>
                            <span class="text-sm font-medium text-slate-700">Status Aktif</span>
                            <p class="text-xs text-slate-500">Karyawan aktif dapat dipilih saat membuat pesanan</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-3 pt-6 border-t border-slate-200">
                <a href="{{ route('staff.index') }}" 
                    class="px-4 py-2 text-slate-600 hover:text-slate-800 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    Simpan Karyawan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function handlePositionChange(select) {
    const customInput = document.getElementById('position_custom');
    const hiddenInput = document.getElementById('position_hidden');

    if (select.value === '__custom__') {
        // Tampilkan input kustom, sembunyikan hidden
        customInput.classList.remove('hidden');
        customInput.required = true;
        customInput.value = '';
        customInput.focus();
        hiddenInput.disabled = true;
        hiddenInput.value = '';
    } else {
        // Sembunyikan input kustom, gunakan hidden
        customInput.classList.add('hidden');
        customInput.required = false;
        customInput.value = '';
        hiddenInput.disabled = false;
        hiddenInput.value = select.value;
    }
}

// Inisialisasi saat halaman load
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('position_select');
    const customInput = document.getElementById('position_custom');
    const hiddenInput = document.getElementById('position_hidden');

    // Jika ada nilai kustom (dari old input), pastikan state benar
    if (!customInput.classList.contains('hidden')) {
        hiddenInput.disabled = true;
    } else {
        hiddenInput.value = select.value !== '__custom__' ? select.value : '';
    }
});
</script>
@endpush
@endsection