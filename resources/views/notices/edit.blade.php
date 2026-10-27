@extends('layouts.dashboard')
@section('title', 'Edit Notice')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.notices.index') }}">Notices</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Edit Notice</h6>
        <form method="POST" action="{{ route('dashboard.notices.update', $notice) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title', $notice->title) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Audience</label>
                    <select name="audience" class="form-select">
                        @foreach(['all','students','teachers','parents'] as $aud)
                            <option value="{{ $aud }}" {{ $notice->audience == $aud ? 'selected' : '' }}>
                                {{ ucfirst($aud) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Publish Date</label>
                    <input type="date" name="publish_date" class="form-control"
                        value="{{ old('publish_date', $notice->publish_date) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   {{ $notice->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $notice->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <textarea name="content" class="form-control" rows="5" required>{{ old('content', $notice->content) }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Update Notice
                    </button>
                    <a href="{{ route('dashboard.notices.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection