@extends('layouts.dashboard')
@section('title', 'Edit Exam')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.exams.index') }}">Exams</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Edit Exam — {{ $exam->name }}</h6>
        <form method="POST" action="{{ route('dashboard.exams.update', $exam) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Exam Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $exam->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Academic Year</label>
                    <select name="academic_year_id" class="form-select">
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}"
                                {{ $exam->academic_year_id == $year->id ? 'selected' : '' }}>
                                {{ $year->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control"
                        value="{{ old('start_date', $exam->start_date) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" name="end_date" class="form-control"
                        value="{{ old('end_date', $exam->end_date) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="upcoming"  {{ $exam->status == 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing"   {{ $exam->status == 'ongoing'   ? 'selected' : '' }}>Ongoing</option>
                        <option value="completed" {{ $exam->status == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $exam->description) }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Update Exam
                    </button>
                    <a href="{{ route('dashboard.exams.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection