@extends('layouts.app')

@section('title', 'Pesanan Baru - WashManager Pro')

@section('content')
<div class="p-6 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('orders.index') }}" class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <span class="material-symbols-outlined text-slate-600">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Pesanan Cuci Baru</h1>
                <p class="text-slate-600">Buat pesanan cuci kendaraan baru</p>
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="flex items-center justify-center mb-8">
        <div class="flex items-center">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium">1</div>
                <span class="text-xs mt-2 text-blue-600 font-medium">Kendaraan</span>
            </div>
            <div class="h-px w-16 bg-blue-600 mx-4"></div>
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-sm font-medium">2</div>
                <span class="text-xs mt-2 text-blue-600 font-medium">Detail</span>
            </div>
            <div class="h-px w-16 bg-slate-200 mx-4"></div>
            <div class="flex flex-col items-center opacity-40">
                <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 text-sm font-medium">3</div>
                <span class="text-xs mt-2 text-slate-500">Konfirmasi</span>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('orders.store') }}" class="space-y-8">
        @csrf
        
        <!-- Vehicle Information -->
        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Informasi Kendaraan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label for="license_plate" class="block text-sm font-medium text-slate-700 mb-2">Nomor Plat *</label>
                    <input 
                        type="text" 
                        id="license_plate" 
                        name="license_plate" 
                        value="{{ old('license_plate') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('license_plate') border-red-300 @enderror font-mono text-lg tracking-widest uppercase"
                        placeholder="B 1234 ABC"
                        required
                    >
                    @error('license_plate')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="vehicle_type" class="block text-sm font-medium text-slate-700 mb-2">Jenis Kendaraan *</label>
                    <select 
                        id="vehicle_type" 
                        name="vehicle_type" 
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vehicle_type') border-red-300 @enderror"
                        required
                    >
                        <option value="">Pilih Jenis</option>
                        <option value="Sedan" {{ old('vehicle_type') === 'Sedan' ? 'selected' : '' }}>Sedan</option>
                        <option value="Hatchback" {{ old('vehicle_type') === 'Hatchback' ? 'selected' : '' }}>Hatchback</option>
                        <option value="SUV" {{ old('vehicle_type') === 'SUV' ? 'selected' : '' }}>SUV</option>
                        <option value="MPV" {{ old('vehicle_type') === 'MPV' ? 'selected' : '' }}>MPV</option>
                        <option value="Pickup" {{ old('vehicle_type') === 'Pickup' ? 'selected' : '' }}>Pickup</option>
                        <option value="Motor" {{ old('vehicle_type') === 'Motor' ? 'selected' : '' }}>Motor</option>
                    </select>
                    @error('vehicle_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="vehicle_model" class="block text-sm font-medium text-slate-700 mb-2">Model/Merk</label>
                    <input 
                        type="text" 
                        id="vehicle_model" 
                        name="vehicle_model" 
                        value="{{ old('vehicle_model') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vehicle_model') border-red-300 @enderror"
                        placeholder="Honda CR-V"
                    >
                    @error('vehicle_model')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="vehicle_color" class="block text-sm font-medium text-slate-700 mb-2">Warna</label>
                    <input 
                        type="text" 
                        id="vehicle_color" 
                        name="vehicle_color" 
                        value="{{ old('vehicle_color') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('vehicle_color') border-red-300 @enderror"
                        placeholder="Putih"
                    >
                    @error('vehicle_color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Service Selection -->
        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Pilih Layanan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($services as $service)
                <label class="relative flex flex-col p-4 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-blue-300 transition-colors group">
                    <input 
                        type="radio" 
                        name="service_id" 
                        value="{{ $service->id }}"
                        class="absolute top-4 right-4 text-blue-600 focus:ring-blue-500"
                        {{ old('service_id') == $service->id ? 'checked' : '' }}
                        required
                        data-price="{{ $service->price }}"
                    >
                    <div class="mb-3">
                        @php
                            $icons = [
                                'standard' => 'water_drop',
                                'premium' => 'auto_awesome',
                                'detail' => 'cleaning_services'
                            ];
                        @endphp
                        <span class="material-symbols-outlined text-blue-600 text-2xl">{{ $icons[$service->type] ?? 'local_car_wash' }}</span>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-1">{{ $service->name }}</h3>
                    <p class="text-sm text-slate-600 mb-3 flex-1">{{ $service->description }}</p>
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-bold text-blue-600">Rp {{ number_format($service->price, 0, ',', '.') }}</span>
                        <span class="text-xs text-slate-500">{{ $service->duration_minutes }} mnt</span>
                    </div>
                </label>
                @endforeach
            </div>
            @error('service_id')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Additional Details -->
        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Detail Tambahan</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="staff_ids" class="block text-sm font-medium text-slate-700 mb-2">Petugas *</label>
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 mb-3">
                            <input type="checkbox" id="select_all_staff" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded">
                            <label for="select_all_staff" class="text-sm font-medium text-blue-600">Pilih Semua Staff</label>
                        </div>
                        <div class="max-h-40 overflow-y-auto border border-slate-300 rounded-lg p-3 space-y-2">
                            @foreach($staff as $member)
                            <label class="flex items-center gap-3 p-2 hover:bg-slate-50 rounded cursor-pointer">
                                <input 
                                    type="checkbox" 
                                    name="staff_ids[]" 
                                    value="{{ $member->id }}" 
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded staff-checkbox"
                                    {{ in_array($member->id, old('staff_ids', [])) ? 'checked' : '' }}
                                >
                                <div class="flex-1">
                                    <div class="font-medium text-slate-900">{{ $member->name }}</div>
                                    <div class="text-sm text-slate-500">{{ $member->position }} • Komisi: {{ $member->commission_rate }}%</div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <div class="text-xs text-slate-500 mt-2">
                            <span id="selected-staff-count">0</span> staff dipilih. Komisi akan dibagi rata di antara staff yang dipilih.
                        </div>
                    </div>
                    @error('staff_ids')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('staff_ids.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="additional_fee" class="block text-sm font-medium text-slate-700 mb-2">Biaya Tambahan</label>
                    <input 
                        type="number" 
                        id="additional_fee" 
                        name="additional_fee" 
                        value="{{ old('additional_fee') }}"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('additional_fee') border-red-300 @enderror"
                        placeholder="0"
                        min="0"
                    >
                    <p class="text-xs text-slate-500 mt-1">Biaya tambahan untuk SUV, kondisi sangat kotor, dll.</p>
                    @error('additional_fee')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">Catatan</label>
                    <textarea 
                        id="notes" 
                        name="notes" 
                        rows="3"
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror"
                        placeholder="Catatan khusus untuk pesanan ini..."
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Payment & Completion Options -->
        <div class="bg-white p-6 rounded-xl border border-slate-200">
            <h2 class="text-lg font-semibold text-slate-900 mb-4">Pembayaran & Penyelesaian</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-3">Metode Pembayaran</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <label class="payment-method-label relative flex items-center p-4 border-2 border-slate-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                            <input type="radio" name="payment_method" value="cash" class="sr-only payment-method-radio">
                            <div class="flex items-center gap-3 w-full">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-green-600">payments</span>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">Cash</p>
                                    <p class="text-sm text-slate-500">Tunai</p>
                                </div>
                            </div>
                        </label>
                        
                        <label class="payment-method-label relative flex items-center p-4 border-2 border-slate-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                            <input type="radio" name="payment_method" value="qris" class="sr-only payment-method-radio">
                            <div class="flex items-center gap-3 w-full">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-blue-600">qr_code</span>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">QRIS</p>
                                    <p class="text-sm text-slate-500">Scan QR</p>
                                </div>
                            </div>
                        </label>
                        
                        <label class="payment-method-label relative flex items-center p-4 border-2 border-slate-200 rounded-lg cursor-pointer hover:border-blue-300 transition-colors">
                            <input type="radio" name="payment_method" value="transfer" class="sr-only payment-method-radio">
                            <div class="flex items-center gap-3 w-full">
                                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <span class="material-symbols-outlined text-purple-600">account_balance</span>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">Transfer</p>
                                    <p class="text-sm text-slate-500">Bank</p>
                                </div>
                            </div>
                        </label>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Pilih metode pembayaran jika pelanggan langsung membayar</p>
                </div>

                <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-lg border border-blue-200">
                    <input type="checkbox" id="auto_complete" name="auto_complete" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded">
                    <label for="auto_complete" class="text-sm font-medium text-blue-900">
                        Tandai sebagai selesai (untuk cuci express yang langsung selesai)
                    </label>
                </div>
            </div>
        </div>
        <div class="bg-slate-50 p-6 rounded-xl border border-slate-200">
            <h3 class="text-lg font-semibold text-slate-900 mb-4">Ringkasan Biaya & Komisi</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Biaya -->
                <div class="space-y-2">
                    <h4 class="font-medium text-slate-700 mb-3">Biaya</h4>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Harga Layanan:</span>
                        <span id="service-price" class="font-medium">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Biaya Tambahan:</span>
                        <span id="additional-price" class="font-medium">Rp 0</span>
                    </div>
                    <div class="border-t border-slate-300 pt-2">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold text-slate-900">Total:</span>
                            <span id="total-price" class="text-lg font-bold text-blue-600">Rp 0</span>
                        </div>
                    </div>
                </div>
                
                <!-- Komisi -->
                <div class="space-y-2">
                    <h4 class="font-medium text-slate-700 mb-3">Pembagian Komisi</h4>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Komisi per Staff:</span>
                        <span id="commission-per-staff" class="font-medium">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Total Komisi Staff:</span>
                        <span id="total-staff-commission" class="font-medium text-blue-600">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">Komisi Owner:</span>
                        <span id="owner-commission" class="font-medium text-green-600">Rp 0</span>
                    </div>
                    <div class="text-xs text-slate-500 mt-2">
                        <span id="staff-count-display">0</span> staff • Komisi staff: <span id="staff-commission-rate">15</span>%
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.index') }}" class="flex-1 bg-slate-200 text-slate-700 py-3 px-6 rounded-lg hover:bg-slate-300 transition-colors text-center font-medium">
                Batal
            </a>
            <button type="submit" id="submit-btn" class="flex-[2] bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <span id="submit-text">Buat Pesanan</span>
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Commission settings (you can fetch this from backend)
    const STAFF_COMMISSION_RATE = 15; // 15%
    const OWNER_COMMISSION_RATE = 85; // 85%
    
    // Price and commission calculation
    function updatePriceAndCommission() {
        const serviceRadio = document.querySelector('input[name="service_id"]:checked');
        const additionalFeeInput = document.getElementById('additional_fee');
        const selectedStaff = document.querySelectorAll('input[name="staff_ids[]"]:checked');
        
        const servicePrice = serviceRadio ? parseInt(serviceRadio.dataset.price) : 0;
        const additionalFee = parseInt(additionalFeeInput.value) || 0;
        const total = servicePrice + additionalFee;
        const staffCount = selectedStaff.length;
        
        // Update price display
        document.getElementById('service-price').textContent = 'Rp ' + servicePrice.toLocaleString('id-ID');
        document.getElementById('additional-price').textContent = 'Rp ' + additionalFee.toLocaleString('id-ID');
        document.getElementById('total-price').textContent = 'Rp ' + total.toLocaleString('id-ID');
        
        // Update commission calculation
        const totalStaffCommission = total * (STAFF_COMMISSION_RATE / 100);
        const commissionPerStaff = staffCount > 0 ? totalStaffCommission / staffCount : 0;
        const ownerCommission = total * (OWNER_COMMISSION_RATE / 100);
        
        document.getElementById('commission-per-staff').textContent = 'Rp ' + Math.round(commissionPerStaff).toLocaleString('id-ID');
        document.getElementById('total-staff-commission').textContent = 'Rp ' + Math.round(totalStaffCommission).toLocaleString('id-ID');
        document.getElementById('owner-commission').textContent = 'Rp ' + Math.round(ownerCommission).toLocaleString('id-ID');
        document.getElementById('staff-count-display').textContent = staffCount;
        document.getElementById('staff-commission-rate').textContent = STAFF_COMMISSION_RATE;
        
        // Update selected staff count
        document.getElementById('selected-staff-count').textContent = staffCount;
    }
    
    // Update button text based on payment method
    function updateSubmitButton() {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        const autoComplete = document.getElementById('auto_complete').checked;
        const submitBtn = document.getElementById('submit-btn');
        const submitText = document.getElementById('submit-text');
        
        if (paymentMethod) {
            submitBtn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            submitBtn.classList.add('bg-green-600', 'hover:bg-green-700');
            if (autoComplete) {
                submitText.textContent = 'Buat, Bayar & Print Struk';
            } else {
                submitText.textContent = 'Buat & Bayar Pesanan';
            }
        } else {
            submitBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            submitBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
            submitText.textContent = 'Buat Pesanan';
        }
    }
    
    // Payment method styling
    function updatePaymentMethodStyling() {
        document.querySelectorAll('.payment-method-radio').forEach(radio => {
            const label = radio.closest('.payment-method-label');
            
            if (radio.checked) {
                label.classList.remove('border-slate-200');
                label.classList.add('border-blue-500', 'bg-blue-50');
            } else {
                label.classList.remove('border-blue-500', 'bg-blue-50');
                label.classList.add('border-slate-200');
            }
        });
    }
    
    // Staff selection functions
    function updateSelectAllStaff() {
        const selectAllCheckbox = document.getElementById('select_all_staff');
        const staffCheckboxes = document.querySelectorAll('.staff-checkbox');
        const checkedStaff = document.querySelectorAll('.staff-checkbox:checked');
        
        if (checkedStaff.length === staffCheckboxes.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
        } else if (checkedStaff.length > 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Price calculation events
        document.querySelectorAll('input[name="service_id"]').forEach(radio => {
            radio.addEventListener('change', updatePriceAndCommission);
        });
        
        document.getElementById('additional_fee').addEventListener('input', updatePriceAndCommission);
        
        // Staff selection events
        document.querySelectorAll('.staff-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSelectAllStaff();
                updatePriceAndCommission();
            });
        });
        
        // Select all staff checkbox
        document.getElementById('select_all_staff').addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.staff-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updatePriceAndCommission();
        });
        
        // Payment method events
        document.querySelectorAll('.payment-method-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                updatePaymentMethodStyling();
                updateSubmitButton();
            });
        });
        
        // Auto complete checkbox
        document.getElementById('auto_complete').addEventListener('change', updateSubmitButton);
        
        // Form submission
        document.querySelector('form').addEventListener('submit', function(e) {
            console.log('Form submission started');
            
            // Check if required fields are filled
            const licencePlate = document.getElementById('license_plate').value.trim();
            const vehicleType = document.getElementById('vehicle_type').value;
            const serviceId = document.querySelector('input[name="service_id"]:checked');
            const selectedStaff = document.querySelectorAll('input[name="staff_ids[]"]:checked');
            
            console.log('Form data:', {
                licencePlate,
                vehicleType,
                serviceId: serviceId ? serviceId.value : null,
                staffCount: selectedStaff.length
            });
            
            // Validate required fields
            if (!licencePlate) {
                alert('Plat nomor harus diisi!');
                e.preventDefault();
                return false;
            }
            
            if (!vehicleType) {
                alert('Jenis kendaraan harus dipilih!');
                e.preventDefault();
                return false;
            }
            
            if (!serviceId) {
                alert('Layanan harus dipilih!');
                e.preventDefault();
                return false;
            }
            
            if (selectedStaff.length === 0) {
                alert('Minimal satu petugas harus dipilih!');
                e.preventDefault();
                return false;
            }
            
            console.log('All validations passed, submitting form...');
            
            const submitBtn = document.getElementById('submit-btn');
            
            // Disable button to prevent double submission
            submitBtn.disabled = true;
            
            // Add loading spinner
            submitBtn.innerHTML = '<span class="material-symbols-outlined animate-spin mr-2">refresh</span>Memproses...';
            
            // Allow form to submit
            return true;
        });
        
        // Initial calculation
        updatePriceAndCommission();
        updateSubmitButton();
        updatePaymentMethodStyling();
        updateSelectAllStaff();
    });
</script>
@endpush
@endsection