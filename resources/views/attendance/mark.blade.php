@extends('layouts.dashboard')
@section('title', 'Mark Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.attendance.index') }}">Attendance</a></li>
    <li class="breadcrumb-item active">Mark Attendance</li>
@endsection

@section('content')
    <div class="page-card mb-3">
        <h6 class="fw-bold mb-3" style="color:#1e293b">Select Class & Date</h6>
        <form method="POST" action="{{ route('dashboard.attendance.store') }}" id="attendanceForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" id="attendanceDate"
                        class="form-control" value="{{ $date }}"
                        onchange="checkLoadStudents()" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Class <span class="text-danger">*</span></label>
                    <select name="class_id" id="classSelect" class="form-select"
                            onchange="loadSectionsAjax()" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                {{ $class->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Section <span class="text-danger">*</span></label>
                    <select name="section_id" id="sectionSelect" class="form-select"
                            onchange="checkLoadStudents()" required>
                        <option value="">Select Section</option>
                        @foreach($sections as $section)
                            <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                                {{ $section->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="button" class="btn btn-primary w-100"
                            onclick="checkLoadStudents()">
                        <i class="fas fa-search me-2"></i>Load Students
                    </button>
                </div>
            </div>
            {{-- Students loaded here via Ajax --}}
            <div id="studentsContainer" class="mt-4">
                @if($students->count() > 0)
                    {{-- Show if already loaded via GET --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h6 class="fw-bold mb-0" style="color:#1e293b">Mark Attendance</h6>
                            <small class="text-muted">{{ $students->count() }} students</small>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-success btn-sm" onclick="markAll('present')">
                                <i class="fas fa-check me-1"></i>All Present
                            </button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="markAll('absent')">
                                <i class="fas fa-times me-1"></i>All Absent
                            </button>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Roll No</th>
                                    <th class="text-center">Present</th>
                                    <th class="text-center">Absent</th>
                                    <th class="text-center">Late</th>
                                    <th class="text-center">Half Day</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $i => $student)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $student->user->photo_url }}"
                                                    class="rounded-circle"
                                                    style="width:32px;height:32px;object-fit:cover;">
                                                    <span style="font-size:0.875rem;font-weight:500">{{ $student->user->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $student->roll_number }}</small>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]"
                                                value="present" class="form-check-input"
                                                {{ $student->attendance_status === 'present' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]"
                                                value="absent" class="form-check-input"
                                                {{ $student->attendance_status === 'absent' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]"
                                                value="late" class="form-check-input"
                                                {{ $student->attendance_status === 'late' ? 'checked' : '' }}>
                                        </td>
                                        <td class="text-center">
                                            <input type="radio" name="attendance[{{ $student->id }}]"
                                                value="half_day" class="form-check-input"
                                                {{ $student->attendance_status === 'half_day' ? 'checked' : '' }}>
                                        </td>
                                        <td>
                                            <input type="text" name="remarks[{{ $student->id }}]"
                                                class="form-control form-control-sm"
                                                style="width:120px" placeholder="Optional">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-save me-2"></i>Save Attendance
                        </button>
                        <a href="{{ route('dashboard.attendance.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                @endif
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function loadSectionsAjax() {
            const classId = $('#classSelect').val();
            loadSections(classId, 'sectionSelect');
            $('#studentsContainer').html('');
        }
        function checkLoadStudents() {
            const classId   = $('#classSelect').val();
            const sectionId = $('#sectionSelect').val();
            const date      = $('#attendanceDate').val();
            if (classId && sectionId && date) {
                // Update hidden inputs
                $('input[name="class_id"]').val(classId);
                $('input[name="section_id"]').val(sectionId);
                $('input[name="date"]').val(date);
                loadStudentsForAttendance(classId, sectionId, date);
            }
        }
        // Add hidden inputs for form submission
        $('#attendanceForm').prepend(`
            <input type="hidden" name="class_id" value="${$('#classSelect').val()}">
            <input type="hidden" name="section_id" value="${$('#sectionSelect').val()}">
        `);
        // Auto load if class already selected
        $(document).ready(function() {
            const classId   = $('#classSelect').val();
            const sectionId = $('#sectionSelect').val();
            const date      = $('#attendanceDate').val();
            if (classId && sectionId) {
                loadStudentsForAttendance(classId, sectionId, date);
            }
        });
    </script>
@endpush