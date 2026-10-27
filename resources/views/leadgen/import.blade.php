@extends('layouts.dashboard')
@section('title', 'Import Leads')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.leadgen.overview') }}">Lead Generation</a></li>
    <li class="breadcrumb-item active">Import</li>
@endsection

@section('content')
<div class="row g-3">
    <div class="col-lg-7">
        <div class="page-card">
            <h6 class="fw-bold mb-3" style="color:#1e293b"><i class="fas fa-file-excel me-2 text-success"></i>Import from Excel / CSV</h6>

            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <p class="text-muted" style="font-size:0.875rem">
                The first row must be headers. Only <strong>phone</strong> is required; recognised columns are
                name, phone, email, student_name, class and notes. Duplicates (same phone) are skipped.
            </p>
            <a href="{{ route('dashboard.leadgen.leads.template') }}" class="btn btn-sm btn-outline-secondary mb-3"><i class="fas fa-download me-1"></i>Download template</a>

            <form method="POST" action="{{ route('dashboard.leadgen.leads.import.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" name="consent" value="1" id="consent" required>
                    <label class="form-check-label" for="consent" style="font-size:0.875rem">
                        I confirm these contacts have agreed to be contacted by the school on WhatsApp and phone.
                    </label>
                </div>
                <button class="btn btn-primary"><i class="fas fa-upload me-2"></i>Import</button>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="page-card">
            <h6 class="fw-bold mb-3" style="color:#1e293b"><i class="fas fa-globe me-2 text-primary"></i>Website admission form</h6>
            <p class="text-muted" style="font-size:0.875rem">
                New submissions from your public admission page become leads automatically.
                Use this to import older enquiries ({{ $pending }} found in Messages).
            </p>
            <form method="POST" action="{{ route('dashboard.leadgen.leads.sync') }}">
                @csrf
                <button class="btn btn-outline-primary"><i class="fas fa-sync me-2"></i>Import old enquiries</button>
            </form>
        </div>
    </div>
</div>
@endsection
