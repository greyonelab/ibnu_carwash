@extends('layouts.app')

@section('title', 'Commission Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-percentage me-2"></i>
                        Commission Settings
                    </h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            @foreach($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('commission.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-users me-2"></i>
                                            Staff Commission
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="staff_commission" class="form-label">Staff Commission Percentage</label>
                                            <div class="input-group">
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="staff_commission" 
                                                       name="staff_commission" 
                                                       value="{{ $commissionSettings->where('name', 'default_staff_commission')->first()->percentage ?? 15 }}"
                                                       min="0" 
                                                       max="100" 
                                                       step="0.01" 
                                                       required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <div class="form-text">
                                                This percentage will be divided equally among all staff members assigned to an order.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">
                                            <i class="fas fa-crown me-2"></i>
                                            Owner Commission
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="owner_commission" class="form-label">Owner Commission Percentage</label>
                                            <div class="input-group">
                                                <input type="number" 
                                                       class="form-control" 
                                                       id="owner_commission" 
                                                       name="owner_commission" 
                                                       value="{{ $commissionSettings->where('name', 'owner_commission')->first()->percentage ?? 85 }}"
                                                       min="0" 
                                                       max="100" 
                                                       step="0.01" 
                                                       required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <div class="form-text">
                                                Remaining percentage after staff commission.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">
                                            <i class="fas fa-calculator me-2"></i>
                                            Commission Calculator Preview
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label class="form-label">Sample Order Amount</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" class="form-control" id="sample_amount" value="100000" min="1">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Number of Staff</label>
                                                <input type="number" class="form-control" id="staff_count" value="2" min="1">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Commission per Staff</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="text" class="form-control" id="commission_per_staff" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <div class="alert alert-info">
                                                    <strong>Total Staff Commission:</strong> <span id="total_staff_commission">Rp 0</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="alert alert-success">
                                                    <strong>Owner Commission:</strong> <span id="owner_commission_amount">Rp 0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Commission Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

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
        
        document.getElementById('commission_per_staff').value = commissionPerStaff.toLocaleString('id-ID');
        document.getElementById('total_staff_commission').textContent = 'Rp ' + totalStaffCommission.toLocaleString('id-ID');
        document.getElementById('owner_commission_amount').textContent = 'Rp ' + ownerCommissionAmount.toLocaleString('id-ID');
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