@extends('layouts.dashboard')
@section('title', 'Fees Report')

@section('breadcrumb')
    <li class="breadcrumb-item active">Fee Report</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Fee Collection Report</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Complete fee collection summary</p>
        </div>
        <a href="{{ route('dashboard.reports.export', ['fees','csv']) }}" class="btn btn-success btn-sm">
            <i class="fas fa-file-csv me-1"></i>Export CSV
        </a>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card text-center" style="border-left:4px solid #2563eb">
                <div class="fw-bold fs-4 text-primary">PKR {{ number_format($summary['total_amount']) }}</div>
                <div class="stat-label">Total Billed</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center" style="border-left:4px solid #10b981">
                <div class="fw-bold fs-4 text-success">PKR {{ number_format($summary['total_collected']) }}</div>
                <div class="stat-label">Collected</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center" style="border-left:4px solid #f59e0b">
                <div class="fw-bold fs-4 text-warning">PKR {{ number_format($summary['total_pending']) }}</div>
                <div class="stat-label">Pending</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card text-center" style="border-left:4px solid #8b5cf6">
                <div class="fw-bold fs-4" style="color:#8b5cf6">{{ $summary['total_paid'] }}</div>
                <div class="stat-label">Paid Records</div>
            </div>
        </div>
    </div>
    <div class="page-card mb-3">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="paid"    {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                </select>
            </div>
            <div class="col-md-4">
                <select name="month" class="form-select form-select-sm">
                    <option value="">All Months</option>
                    @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i>Filter
                </button>
                <a href="{{ route('dashboard.reports.fees') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </form>
    </div>
    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Student</th>
                        <th>Fee Type</th>
                        <th>Month</th>
                        <th>Amount</th>
                        <th>Paid</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $i => $fee)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>
                                <small style="font-weight:500">{{ $fee->student->user->name ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ $fee->feeType->name ?? '-' }}</small>
                            </td>
                            <td>
                                <small>{{ $fee->month ?? '-' }}</small>
                            </td>
                            <td>
                                <small>PKR {{ number_format($fee->amount) }}</small>
                            </td>
                            <td>
                                <small class="text-success">PKR {{ number_format($fee->paid_amount) }}</small>
                            </td>
                            <td>
                                <small>{{ \Carbon\Carbon::parse($fee->due_date)->format('d M Y') }}</small>
                            </td>
                            <td>
                                @if($fee->status === 'paid')
                                    <span class="badge badge-active">Paid</span>
                                @elseif($fee->status === 'pending')
                                    <span class="badge badge-pending">Pending</span>
                                @else
                                    <span class="badge" style="background:#dbeafe;color:#1d4ed8">{{ ucfirst($fee->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No fee records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection