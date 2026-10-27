@extends('layouts.dashboard')
@section('title', 'Fee Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.fees.index') }}">Fees</a></li>
    <li class="breadcrumb-item active">Details</li>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="page-card">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <h6 class="fw-bold mb-0" style="color:#1e293b">Fee Details</h6>
                    <div class="d-flex gap-2">
                        <a href="{{ route('dashboard.fees.receipt', $fee) }}"
                        class="btn btn-sm btn-secondary" target="_blank">
                            <i class="fas fa-print me-1"></i>Receipt
                        </a>
                        <a href="{{ route('dashboard.fees.index') }}"
                        class="btn btn-sm btn-primary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>

                {{-- Student Info --}}
                <div class="p-3 rounded mb-4" style="background:#f8fafc;border:1px solid #e2e8f0">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $fee->student->user->photo_url }}"
                            class="rounded-circle"
                            style="width:50px;height:50px;object-fit:cover;">
                        <div>
                            <div class="fw-bold" style="color:#1e293b">
                                {{ $fee->student->user->name }}
                            </div>
                            <small class="text-muted">
                                {{ $fee->student->admission_number }} |
                                {{ $fee->student->class->name ?? '-' }} /
                                {{ $fee->student->section->name ?? '-' }}
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Fee Details --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Fee Type</small>
                        <strong>{{ $fee->feeType->name ?? '-' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Month</small>
                        <strong>{{ $fee->month ?? '-' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Amount</small>
                        <strong>PKR {{ number_format($fee->amount) }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Discount</small>
                        <strong class="text-success">PKR {{ number_format($fee->discount) }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Fine</small>
                        <strong class="text-danger">PKR {{ number_format($fee->fine) }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Paid Amount</small>
                        <strong class="text-primary">PKR {{ number_format($fee->paid_amount) }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Due Date</small>
                        <strong>{{ \Carbon\Carbon::parse($fee->due_date)->format('d M Y') }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Paid Date</small>
                        <strong>{{ $fee->paid_date ? \Carbon\Carbon::parse($fee->paid_date)->format('d M Y') : 'Not paid' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Receipt No</small>
                        <strong>{{ $fee->receipt_number ?? '-' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Status</small>
                        @if($fee->status === 'paid')
                            <span class="badge badge-active fs-6">Paid</span>
                        @elseif($fee->status === 'pending')
                            <span class="badge badge-pending fs-6">Pending</span>
                        @elseif($fee->status === 'partial')
                            <span class="badge" style="background:#dbeafe;color:#1d4ed8">Partial</span>
                        @else
                            <span class="badge badge-inactive fs-6">Overdue</span>
                        @endif
                    </div>
                </div>

                {{-- Total --}}
                <div class="p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0">
                    <div class="d-flex justify-content-between">
                        <span class="fw-bold">Net Amount Due:</span>
                        <span class="fw-bold text-success fs-5">
                            PKR {{ number_format($fee->amount + $fee->fine - $fee->discount) }}
                        </span>
                    </div>
                </div>

                {{-- Collect Fee --}}
                @if($fee->status !== 'paid')
                <div class="mt-4">
                    <button type="button" class="btn btn-success w-100"
                            onclick="collectFee({{ $fee->id }}, {{ $fee->amount + $fee->fine - $fee->discount }})">
                        <i class="fas fa-money-bill me-2"></i>Collect Payment
                    </button>
                </div>

                {{-- Collect Modal --}}
                <div class="modal fade" id="collectModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold">Collect Payment</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" id="collectForm">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label">Amount (PKR)</label>
                                        <input type="number" name="paid_amount" id="paidAmount"
                                            class="form-control" min="0" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check me-1"></i>Collect
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function collectFee(feeId, amount) {
            document.getElementById('collectForm').action = `/dashboard/fees/${feeId}/collect`;
            document.getElementById('paidAmount').value   = amount;
            new bootstrap.Modal(document.getElementById('collectModal')).show();
        }
    </script>
@endpush