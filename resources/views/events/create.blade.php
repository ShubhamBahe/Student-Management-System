@extends('layouts.dashboard')
@section('title', 'Add Event')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.events.index') }}">Events</a></li>
    <li class="breadcrumb-item active">Add Event</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Create New Event</h6>
        <form method="POST" action="{{ route('dashboard.events.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Event Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title') }}" placeholder="Event title" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control"
                        value="{{ old('start_date') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control"
                        value="{{ old('end_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Location</label>
                    <input type="text" name="location" class="form-control"
                        value="{{ old('location') }}" placeholder="e.g. School Ground">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"
                            placeholder="Event description...">{{ old('description') }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Create Event
                    </button>
                    <a href="{{ route('dashboard.events.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection