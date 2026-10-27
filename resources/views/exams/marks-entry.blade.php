@extends('layouts.dashboard')
@section('title', 'Marks Entry')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.exams.index') }}">Exams</a></li>
    <li class="breadcrumb-item active">Marks Entry</li>
@endsection

@section('content')
    <div class="page-card mb-3">
        <h6 class="fw-bold mb-3" style="color:#1e293b">Select Exam, Class & Subject</h6>
        <form method="POST" action="{{ route('dashboard.marks.store') }}" id="marksForm">
            @csrf
            <input type="hidden" name="exam_id"    id="hiddenExamId">
            <input type="hidden" name="subject_id" id="hiddenSubjectId">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Exam <span class="text-danger">*</span></label>
                    <select name="exam_id_select" id="examSelect" class="form-select"
                        onchange="checkLoadMarks()">
                        <option value="">Select Exam</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}"
                                {{ request('exam_id') == $exam->id ? 'selected' : '' }}>
                                {{ $exam->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id_select" id="classSelect" class="form-select"
                            onchange="loadSectionsForMarks()">
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}"
                                {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Subject <span class="text-danger">*</span></label>
                    <select name="subject_id_select" id="subjectSelect" class="form-select"
                            onchange="checkLoadMarks()">
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100"
                        onclick="checkLoadMarks()">
                        <i class="fas fa-search me-2"></i>Load Students
                    </button>
                </div>
            </div>

            {{-- Students loaded here via Ajax --}}
            <div id="marksContainer" class="mt-4"></div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function loadSectionsForMarks() {
            checkLoadMarks();
        }
        function checkLoadMarks() {
            const examId    = $('#examSelect').val();
            const classId   = $('#classSelect').val();
            const subjectId = $('#subjectSelect').val();

            $('#hiddenExamId').val(examId);
            $('#hiddenSubjectId').val(subjectId);

            if (examId && classId && subjectId) {
                loadStudentsForMarks(examId, classId, subjectId);
            }
        }
        // Auto load if values already set
        $(document).ready(function() {
            const examId    = $('#examSelect').val();
            const classId   = $('#classSelect').val();
            const subjectId = $('#subjectSelect').val();
            if (examId && classId && subjectId) {
                loadStudentsForMarks(examId, classId, subjectId);
            }
        });
    </script>
@endpush