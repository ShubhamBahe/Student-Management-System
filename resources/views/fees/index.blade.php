@extends('layouts.dashboard')
@section('title', 'Fees')

@section('breadcrumb')
    <li class="breadcrumb-item active">Fees</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Fee Management</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Manage student fee collection</p>
        </div>
        @can('manage fees')
        <a href="{{ route('dashboard.fees.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Fee
        </a>
        @endcan
    </div>

    {{-- Summary --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="stat-card" style="border-left:4px solid #10b981">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-number text-success">PKR {{ number_format($summary['total_collected']) }}</div>
                        <div class="stat-label">Total Collected</div>
                    </div>
                    <div class="stat-icon" style="background:#dcfce7"><i class="fas fa-check-circle" style="color:#10b981"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-left:4px solid #f59e0b">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-number text-warning">PKR {{ number_format($summary['total_pending']) }}</div>
                        <div class="stat-label">Total Pending</div>
                    </div>
                    <div class="stat-icon" style="background:#fef9c3"><i class="fas fa-clock" style="color:#f59e0b"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="border-left:4px solid #dc2626">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-number text-danger">{{ $summary['total_overdue'] }}</div>
                        <div class="stat-label">Overdue Records</div>
                    </div>
                    <div class="stat-icon" style="background:#fee2e2"><i class="fas fa-exclamation-circle" style="color:#dc2626"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="page-card mb-3">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Search student name or admission no..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="paid"    {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="month" class="form-select form-select-sm">
                    <option value="">All Months</option>
                    @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $month)
                    <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>{{ $month }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i></button>
                <a href="{{ route('dashboard.fees.index') }}" class="btn btn-secondary btn-sm"><i class="fas fa-times"></i></a>
            </div>
        </form>
    </div>

    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover">
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fees as $i => $fee)
                        <tr>
                            <td>{{ $fees->firstItem() + $i }}</td>
                            <td>
                                <div style="font-weight:500;font-size:0.875rem">{{ $fee->student->user->name ?? '-' }}</div>
                                <small class="text-muted">{{ $fee->student->admission_number ?? '-' }}</small>
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
                                @elseif($fee->status === 'partial')
                                    <span class="badge" style="background:#dbeafe;color:#1d4ed8">Partial</span>
                                @else
                                    <span class="badge badge-inactive">Overdue</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('dashboard.fees.show', $fee) }}" class="btn btn-sm btn-info text-white">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($fee->status !== 'paid')
                                        @can('collect fees')
                                            <button type="button" class="btn btn-sm btn-success" onclick="collectFee({{ $fee->id }}, {{ $fee->amount }})">
                                            <i class="fas fa-money-bill"></i>
                                            </button>
                                        @endcan
                                    @endif
                                    <a href="{{ route('dashboard.fees.receipt', $fee) }}" class="btn btn-sm btn-secondary" target="_blank">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted"><i class="fas fa-money-bill fa-2x mb-2 d-block"></i>No fee records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $fees->withQueryString()->links() }}
    </div>

    {{-- Collect Fee Modal --}}
    <div class="modal fade" id="collectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title fw-bold">Collect Fee Payment</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="collectForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Total Due Amount</label>
                            <input type="text" id="dueAmount" class="form-control" readonly style="background:#f8fafc">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount to Collect (PKR) <span class="text-danger">*</span></label>
                            <input type="number" name="paid_amount" id="paidAmount" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-check me-2"></i>Collect Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function collectFee(feeId, amount) {
            document.getElementById('collectForm').action = `/dashboard/fees/${feeId}/collect`;
            document.getElementById('dueAmount').value    = 'PKR ' + amount.toLocaleString();
            document.getElementById('paidAmount').value   = amount;
            new bootstrap.Modal(document.getElementById('collectModal')).show();
        }
    </script>
@endpush