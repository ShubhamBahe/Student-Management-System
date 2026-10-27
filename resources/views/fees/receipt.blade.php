@extends('layouts.dashboard')
@section('title', 'Fee Receipt')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="page-card" id="receipt">
                {{-- Receipt Header --}}
                <div class="text-center border-bottom pb-3 mb-3">
                    <h4 class="fw-bold mb-1" style="color:#1e293b">{{ setting('school_name', 'School Management System') }}</h4>
                    <p class="text-muted mb-0" style="font-size:0.875rem">{{ setting('school_address', 'Hyderabad, Sindh, Pakistan') }}</p>
                    <p class="text-muted mb-0" style="font-size:0.875rem">{{ setting('school_phone') }} | {{ setting('school_email') }}</p>
                    <div class="mt-2">
                        <span class="badge" style="background:#dbeafe;color:#1d4ed8;font-size:0.875rem;padding:6px 16px">
                            FEE RECEIPT
                        </span>
                    </div>
                </div>
                {{-- Receipt Info --}}
                <div class="row mb-3">
                    <div class="col-6">
                        <small class="text-muted">Receipt No</small>
                        <div class="fw-bold">{{ $fee->receipt_number }}</div>
                    </div>
                    <div class="col-6 text-end">
                        <small class="text-muted">Date</small>
                        <div class="fw-bold">{{ now()->format('d M Y') }}</div>
                    </div>
                </div>
                {{-- Student Info --}}
                <div class="p-3 rounded mb-3" style="background:#f8fafc;border:1px solid #e2e8f0">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block">Student Name</small>
                            <strong>{{ $fee->student->user->name }}</strong>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Admission No</small>
                            <strong>{{ $fee->student->admission_number }}</strong>
                        </div>
                        <div class="col-6 mt-2">
                            <small class="text-muted d-block">Class</small>
                            <strong>{{ $fee->student->class->name ?? '-' }} / {{ $fee->student->section->name ?? '-' }}</strong>
                        </div>
                        <div class="col-6 mt-2">
                            <small class="text-muted d-block">Month</small>
                            <strong>{{ $fee->month ?? '-' }}</strong>
                        </div>
                    </div>
                </div>
                {{-- Fee Details --}}
                <table class="table table-sm mb-3">
                    <thead>
                        <tr>
                            <th>Description</th><th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $fee->feeType->name ?? 'Fee' }}</td>
                            <td class="text-end">PKR {{ number_format($fee->amount) }}</td>
                        </tr>
                        @if($fee->discount > 0)
                            <tr>
                                <td class="text-success">Discount</td><td class="text-end text-success">- PKR {{ number_format($fee->discount) }}</td>
                            </tr>
                        @endif
                        @if($fee->fine > 0)
                            <tr>
                                <td class="text-danger">Fine</td><td class="text-end text-danger">+ PKR {{ number_format($fee->fine) }}</td>
                            </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr style="background:#f8fafc">
                            <th>Total Paid</th>
                            <th class="text-end text-success">PKR {{ number_format($fee->paid_amount) }}</th>
                        </tr>
                    </tfoot>
                </table>
                {{-- Status --}}
                <div class="text-center mb-3">
                    @if($fee->status === 'paid')
                        <span class="badge badge-active" style="font-size:1rem;padding:8px 24px">✓ PAID</span>
                    @else
                        <span class="badge badge-pending" style="font-size:1rem;padding:8px 24px">PENDING</span>
                    @endif
                </div>
                <div class="text-center text-muted border-top pt-3" style="font-size:0.8rem">
                    Thank you for your payment! Keep this receipt for your records.
                </div>
            </div>
            <div class="text-center mt-3 d-print-none">
                <button onclick="window.print()" class="btn btn-primary me-2">
                    <i class="fas fa-print me-2"></i>Print Receipt
                </button>
                <a href="{{ route('dashboard.fees.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </div>
    </div>
    <style>
        @media print {
            .sidebar, .topbar, .breadcrumb, .d-print-none { display: none !important; }
            .main-content { margin-left: 0 !important; }
        .content-area { padding: 0 !important; }
        }
    </style>
@endsection