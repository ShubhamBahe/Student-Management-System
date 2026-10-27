@extends('layouts.dashboard')
@section('title', 'Add Assignment')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.assignments.index') }}">Assignments</a></li>
    <li class="breadcrumb-item active">Add</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Create New Assignment</h6>
        <form method="POST" action="{{ route('dashboard.assignments.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control"
                        value="{{ old('title') }}" placeholder="Assignment title" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Marks</label>
                    <input type="number" name="total_marks" class="form-control"
                        value="{{ old('total_marks', 100) }}" min="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id" class="form-select" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                    <select name="subject_id" class="form-select" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control"
                        value="{{ old('due_date') }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Description / Instructions</label>
                    <textarea name="description" class="form-control" rows="4"
                            placeholder="Assignment instructions...">{{ old('description') }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Create Assignment
                    </button>
                    <a href="{{ route('dashboard.assignments.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection