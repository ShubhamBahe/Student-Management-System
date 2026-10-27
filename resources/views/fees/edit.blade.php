@extends('layouts.dashboard')
@section('title', 'Edit Fee')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.fees.index') }}">Fees</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Edit Fee Record</h6>
        <form method="POST" action="{{ route('dashboard.fees.update', $fee) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-select">
                        @foreach($students as $student)
                            <option value="{{ $student->id }}"
                                {{ $fee->student_id == $student->id ? 'selected' : '' }}>
                                {{ $student->user->name }} — {{ $student->admission_number }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Fee Type</label>
                    <select name="fee_type_id" class="form-select">
                        @foreach($feeTypes as $ft)
                            <option value="{{ $ft->id }}"
                                {{ $fee->fee_type_id == $ft->id ? 'selected' : '' }}>
                                {{ $ft->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Amount (PKR)</label>
                    <input type="number" name="amount" class="form-control"
                        value="{{ old('amount', $fee->amount) }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Discount (PKR)</label>
                    <input type="number" name="discount" class="form-control"
                        value="{{ old('discount', $fee->discount) }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Fine (PKR)</label>
                    <input type="number" name="fine" class="form-control"
                        value="{{ old('fine', $fee->fine) }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control"
                        value="{{ old('due_date', $fee->due_date) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                            <option value="{{ $m }}" {{ $fee->month == $m ? 'selected' : '' }}>{{ $m }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="pending" {{ $fee->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid"    {{ $fee->status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partial" {{ $fee->status == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="overdue" {{ $fee->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Update Fee
                    </button>
                    <a href="{{ route('dashboard.fees.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection