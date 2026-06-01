@extends('layouts.app')

@section('title', 'Commission Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Header Section -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold text-dark mb-0">
                        <i class="fas fa-percentage text-primary me-3"></i>Commission Settings
                    </h1>
                    <p class="text-muted mt-2 mb-0">Manage staff and owner commission percentages</p>
                </div>
                <div class="badge bg-light text-dark fs-6 px-3 py-2">
                    <i class="fas fa-cog me-2"></i>Settings
                </div>
            </div>

            <!-- Main Card -->
            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-body p-4">
                    
                    <!-- Success Alert -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-check-circle me-3 fs-5"></i>
                                <div>
                                    <strong>Success!</strong>
                                    <p class="mb-0">{{ session('success') }}</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Error Alert -->
                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                            <div class="d-flex">
                                <i class="fas fa-exclamation-circle me-3 fs-5 flex-shrink-0 mt-1"></i>
                                <div class="flex-grow-1">
                                    <strong>Validation Errors</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('commission.update') }}" method="POST" id="commissionForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Commission Settings Section -->
                        <div class="row g-4 mb-5">
                            <!-- Staff Commission Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-gradient-info">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm bg-info bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-users text-info fs-5"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-0 text-dark fw-bold">Staff Commission</h5>
                                                <small class="text-muted">Shared among team members</small>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label for="staff_commission" class="form-label fw-semibold text-dark">Percentage</label>
                                            <div class="input-group input-group-lg">
                                                <input type="number" 
                                                       class="form-control form-control-lg border-end-0" 
                                                       id="staff_commission" 
                                                       name="staff_commission" 
                                                       value="{{ $commissionSettings->where('name', 'default_staff_commission')->first()->percentage ?? 15 }}"
                                                       min="0" 
                                                       max="100" 
                                                       step="0.01" 
                                                       required
                                                       style="font-size: 1.2rem; font-weight: 500;">
                                                <span class="input-group-text bg-white border-start-0 fs-5 fw-bold">%</span>
                                            </div>
                                            <div class="form-text mt-2">
                                                <i class="fas fa-info-circle me-1"></i>This percentage will be divided equally among all staff members assigned to an order.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Owner Commission Card -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-gradient-success">
                                    <div class="card-body p-4">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-sm bg-success bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                    <i class="fas fa-crown text-success fs-5"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="mb-0 text-dark fw-bold">Owner Commission</h5>
                                                <small class="text-muted">Auto-calculated remainder</small>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <label for="owner_commission" class="form-label fw-semibold text-dark">Percentage</label>
                                            <div class="input-group input-group-lg">
                                                <input type="number" 
                                                       class="form-control form-control-lg border-end-0" 
                                                       id="owner_commission" 
                                                       name="owner_commission" 
                                                       value="{{ $commissionSettings->where('name', 'owner_commission')->first()->percentage ?? 85 }}"
                                                       min="0" 
                                                       max="100" 
                                                       step="0.01" 
                                                       required
                                                       style="font-size: 1.2rem; font-weight: 500;">
                                                <span class="input-group-text bg-white border-start-0 fs-5 fw-bold">%</span>
                                            </div>
                                            <div class="form-text mt-2">
                                                <i class="fas fa-info-circle me-1"></i>Automatically calculated as 100% - Staff Commission.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Commission Calculator Section -->
                        <div class="card border-0 bg-light rounded-3 mb-5">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-4">
                                    <i class="fas fa-calculator text-warning fs-4 me-3"></i>
                                    <h5 class="mb-0 fw-bold text-dark">Commission Calculator</h5>
                                    <small class="ms-2 text-muted">Preview with sample values</small>
                                </div>

                                <!-- Calculator Inputs -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark" for="sample_amount">Sample Order Amount</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-rupiah-sign text-dark"></i>
                                            </span>
                                            <input type="number" 
                                                   class="form-control form-control-lg" 
                                                   id="sample_amount" 
                                                   value="100000" 
                                                   min="1"
                                                   placeholder="100000">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark" for="staff_count">Number of Staff Members</label>
                                        <input type="number" 
                                               class="form-control form-control-lg" 
                                               id="staff_count" 
                                               value="2" 
                                               min="1"
                                               placeholder="2">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold text-dark" for="commission_per_staff">Commission per Staff</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text bg-white">
                                                <i class="fas fa-rupiah-sign text-dark"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control form-control-lg" 
                                                   id="commission_per_staff" 
                                                   readonly
                                                   style="background-color: #f8f9fa;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Results Section -->
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-4 bg-gradient-info rounded-3 text-white border-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-white text-opacity-80 d-block mb-1">Total Staff Commission</small>
                                                    <h4 class="mb-0 fw-bold"><span id="total_staff_commission">Rp 0</span></h4>
                                                </div>
                                                <div class="fs-1 text-white text-opacity-20">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-4 bg-gradient-success rounded-3 text-white border-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <small class="text-white text-opacity-80 d-block mb-1">Owner Commission</small>
                                                    <h4 class="mb-0 fw-bold"><span id="owner_commission_amount">Rp 0</span></h4>
                                                </div>
                                                <div class="fs-1 text-white text-opacity-20">
                                                    <i class="fas fa-crown"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Order Amount Info -->
                                <div class="alert alert-light border-2 border-secondary mt-3 mb-0">
                                    <div class="row g-3">
                                        <div class="col-md-4 text-center border-end">
                                            <small class="d-block text-muted mb-1">Total Order Amount</small>
                                            <h5 class="mb-0 text-dark fw-bold" id="total_order_amount">Rp 100,000</h5>
                                        </div>
                                        <div class="col-md-4 text-center border-end">
                                            <small class="d-block text-muted mb-1">Staff Commission Rate</small>
                                            <h5 class="mb-0 text-dark fw-bold" id="staff_rate_display">15%</h5>
                                        </div>
                                        <div class="col-md-4 text-center">
                                            <small class="d-block text-muted mb-1">Owner Commission Rate</small>
                                            <h5 class="mb-0 text-dark fw-bold" id="owner_rate_display">85%</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 justify-content-between">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="fas fa-save me-2"></i>Update Commission Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-info {
        background: linear-gradient(135deg, #cfe9ff 0%, #e7f0ff 100%) !important;
    }
    .bg-gradient-success {
        background: linear-gradient(135deg, #d4edda 0%, #e8f5e9 100%) !important;
    }
    .card {
        transition: all 0.3s ease;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }
    .form-control:focus, .input-group .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    .form-label {
        margin-bottom: 0.75rem;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const staffCommissionInput = document.getElementById('staff_commission');
    const ownerCommissionInput = document.getElementById('owner_commission');
    const sampleAmountInput = document.getElementById('sample_amount');
    const staffCountInput = document.getElementById('staff_count');
    
    function updateCalculator() {
        const staffCommission = parseFloat(staffCommissionInput.value) || 0;
        const ownerCommission = parseFloat(ownerCommissionInput.value) || 0;
        const sampleAmount = parseFloat(sampleAmountInput.value) || 0;
        const staffCount = parseInt(staffCountInput.value) || 1;
        
        const totalStaffCommission = (sampleAmount * staffCommission / 100);
        const commissionPerStaff = totalStaffCommission / staffCount;
        const ownerCommissionAmount = (sampleAmount * ownerCommission / 100);
        
        // Update calculator displays
        document.getElementById('commission_per_staff').value = commissionPerStaff.toLocaleString('id-ID');
        document.getElementById('total_staff_commission').textContent = 'Rp ' + totalStaffCommission.toLocaleString('id-ID');
        document.getElementById('owner_commission_amount').textContent = 'Rp ' + ownerCommissionAmount.toLocaleString('id-ID');
        document.getElementById('total_order_amount').textContent = 'Rp ' + sampleAmount.toLocaleString('id-ID');
        document.getElementById('staff_rate_display').textContent = staffCommission.toFixed(2) + '%';
        document.getElementById('owner_rate_display').textContent = ownerCommission.toFixed(2) + '%';
    }
    
    function updateOwnerCommission() {
        const staffCommission = parseFloat(staffCommissionInput.value) || 0;
        ownerCommissionInput.value = (100 - staffCommission).toFixed(2);
        updateCalculator();
    }
    
    staffCommissionInput.addEventListener('input', updateOwnerCommission);
    ownerCommissionInput.addEventListener('input', updateCalculator);
    sampleAmountInput.addEventListener('input', updateCalculator);
    staffCountInput.addEventListener('input', updateCalculator);
    
    // Initial calculation
    updateCalculator();
});
</script>
@endsection