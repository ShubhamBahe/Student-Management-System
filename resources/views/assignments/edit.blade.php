@extends('layouts.dashboard')
@section('title', 'Edit Assignment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.assignments.index') }}">Assignments</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Edit Assignment</h6>
        <form method="POST" action="{{ route('dashboard.assignments.update', $assignment) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title', $assignment->title) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Marks</label>
                    <input type="number" name="total_marks" class="form-control"
                        value="{{ old('total_marks', $assignment->total_marks) }}" min="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select">
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}"
                                {{ $assignment->class_id == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-select">
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ $assignment->subject_id == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control"
                        value="{{ old('due_date', $assignment->due_date) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   {{ $assignment->status == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $assignment->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $assignment->description) }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Update Assignment
                    </button>
                    <a href="{{ route('dashboard.assignments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection