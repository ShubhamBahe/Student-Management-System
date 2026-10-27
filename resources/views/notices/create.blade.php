@extends('layouts.dashboard')
@section('title', 'Add Notice')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.notices.index') }}">Notices</a></li>
    <li class="breadcrumb-item active">Add Notice</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Create New Notice</h6>
        <form method="POST" action="{{ route('dashboard.notices.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}"
                        placeholder="Notice title" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Audience <span class="text-danger">*</span></label>
                    <select name="audience" class="form-select" required>
                        <option value="all">All</option>
                        <option value="students">Students</option>
                        <option value="teachers">Teachers</option>
                        <option value="parents">Parents</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Publish Date</label>
                    <input type="date" name="publish_date" class="form-control"
                        value="{{ old('publish_date', date('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="5"
                            placeholder="Write notice content here..." required>{{ old('content') }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Publish Notice
                    </button>
                    <a href="{{ route('dashboard.notices.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection