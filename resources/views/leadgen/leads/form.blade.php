@extends('layouts.dashboard')
@section('title', $lead->exists ? 'Edit Lead' : 'Add Lead')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.leadgen.overview') }}">Lead Generation</a></li>
    <li class="breadcrumb-item"><a href="{{ route('dashboard.leadgen.leads.index') }}">Leads</a></li>
    <li class="breadcrumb-item active">{{ $lead->exists ? 'Edit' : 'Add' }}</li>
@endsection

@section('content')
@php use App\Models\Lead; @endphp
<div class="page-card">
    <h6 class="fw-bold mb-4" style="color:#1e293b">{{ $lead->exists ? 'Edit Lead' : 'Add New Lead' }}</h6>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ $lead->exists ? route('dashboard.leadgen.leads.update', $lead) : route('dashboard.leadgen.leads.store') }}">
        @csrf
        @if($lead->exists) @method('PUT') @endif
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Parent / contact name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $lead->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone (WhatsApp number) <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $lead->phone) }}" placeholder="9876543210 or +919876543210" required>
                <div class="form-text">10-digit numbers get the default country code (+{{ config('leadgen.default_country_code') }}).</div>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Child's name</label>
                <input type="text" name="student_name" class="form-control" value="{{ old('student_name', $lead->student_name) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">Class interested in</label>
                <input type="text" name="class_interest" class="form-control" value="{{ old('class_interest', $lead->class_interest) }}" placeholder="e.g. Class 5">
            </div>
            <div class="col-md-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    @foreach(Lead::STATUSES as $key => [$label])
                        <option value="{{ $key }}" @selected(old('status', $lead->status) === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Next follow-up</label>
                <input type="datetime-local" name="next_follow_up_at" class="form-control"
                       value="{{ old('next_follow_up_at', optional($lead->next_follow_up_at)->format('Y-m-d\TH:i')) }}">
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" rows="3" class="form-control">{{ old('notes', $lead->notes) }}</textarea>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="consent" value="1" id="consent" @checked(old('consent', $lead->consent))>
                    <label class="form-check-label" for="consent">This person has agreed to be contacted on WhatsApp / phone</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="ai_enabled" value="1" id="ai" @checked(old('ai_enabled', $lead->ai_enabled))>
                    <label class="form-check-label" for="ai">Let the AI reply to this lead automatically</label>
                </div>
            </div>
            <div class="col-12">
                <button class="btn btn-primary me-2"><i class="fas fa-save me-2"></i>Save</button>
                <a href="{{ route('dashboard.leadgen.leads.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </div>
    </form>
</div>
@endsection
