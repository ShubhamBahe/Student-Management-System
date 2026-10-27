// ============================================
// GLOBAL AJAX SETUP
// ============================================
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

// ============================================
// 1. CLASS → SECTION DYNAMIC DROPDOWN
// ============================================
function loadSections(classId, sectionSelectId, selectedId = null) {
    const sectionSelect = $('#' + sectionSelectId);
    sectionSelect.html('<option value="">Loading...</option>');

    if (!classId) {
        sectionSelect.html('<option value="">Select Section</option>');
        return;
    }

    $.ajax({
        url: '/dashboard/ajax/sections/' + classId,
        type: 'GET',
        success: function(data) {
            let options = '<option value="">Select Section</option>';
            data.forEach(function(section) {
                let selected = selectedId == section.id ? 'selected' : '';
                options += `<option value="${section.id}" ${selected}>${section.name}</option>`;
            });
            sectionSelect.html(options);
        },
        error: function() {
            sectionSelect.html('<option value="">Error loading sections</option>');
        }
    });
}

// ============================================
// 2. LOAD STUDENTS BY CLASS + SECTION (ATTENDANCE)
// ============================================
function loadStudentsForAttendance(classId, sectionId, date) {
    if (!classId || !sectionId) return;

    $('#studentsContainer').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2 text-muted">Loading students...</div>
        </div>
    `);

    $.ajax({
        url: '/dashboard/ajax/students-attendance',
        type: 'GET',
        data: { class_id: classId, section_id: sectionId, date: date },
        success: function(data) {
            if (data.students.length === 0) {
                $('#studentsContainer').html(`
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-users fa-2x mb-2 d-block"></i>
                        No active students found in this class/section
                    </div>
                `);
                return;
            }

            let html = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0" style="color:#1e293b">Mark Attendance</h6>
                        <small class="text-muted">${data.students.length} students found</small>
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
            `;

            data.students.forEach(function(student, i) {
                let status = student.attendance_status || 'present';
                html += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="${student.photo}" class="rounded-circle"
                                     style="width:32px;height:32px;object-fit:cover;">
                                <span style="font-size:0.875rem;font-weight:500">${student.name}</span>
                            </div>
                        </td>
                        <td><small>${student.roll_number}</small></td>
                        <td class="text-center">
                            <input type="radio" name="attendance[${student.id}]"
                                   value="present" class="form-check-input att-radio"
                                   ${status === 'present' ? 'checked' : ''}>
                        </td>
                        <td class="text-center">
                            <input type="radio" name="attendance[${student.id}]"
                                   value="absent" class="form-check-input att-radio"
                                   ${status === 'absent' ? 'checked' : ''}>
                        </td>
                        <td class="text-center">
                            <input type="radio" name="attendance[${student.id}]"
                                   value="late" class="form-check-input att-radio"
                                   ${status === 'late' ? 'checked' : ''}>
                        </td>
                        <td class="text-center">
                            <input type="radio" name="attendance[${student.id}]"
                                   value="half_day" class="form-check-input att-radio"
                                   ${status === 'half_day' ? 'checked' : ''}>
                        </td>
                        <td>
                            <input type="text" name="remarks[${student.id}]"
                                   class="form-control form-control-sm"
                                   style="width:120px" placeholder="Optional">
                        </td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Save Attendance
                    </button>
                    <a href="/dashboard/attendance" class="btn btn-secondary">Cancel</a>
                </div>
            `;

            $('#studentsContainer').html(html);
        },
        error: function() {
            $('#studentsContainer').html(`
                <div class="alert alert-danger">Error loading students. Please try again.</div>
            `);
        }
    });
}

// ============================================
// 3. LOAD STUDENTS FOR MARKS ENTRY (AJAX)
// ============================================
function loadStudentsForMarks(examId, classId, subjectId) {
    if (!examId || !classId || !subjectId) return;

    $('#marksContainer').html(`
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <div class="mt-2 text-muted">Loading students...</div>
        </div>
    `);

    $.ajax({
        url: '/dashboard/ajax/students-marks',
        type: 'GET',
        data: { exam_id: examId, class_id: classId, subject_id: subjectId },
        success: function(data) {
            if (data.students.length === 0) {
                $('#marksContainer').html(`
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-users fa-2x mb-2 d-block"></i>
                        No students found
                    </div>
                `);
                return;
            }

            let html = `
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0" style="color:#1e293b">
                            ${data.exam} — ${data.subject}
                        </h6>
                        <small class="text-muted">
                            Full Marks: ${data.full_marks} |
                            Pass Marks: ${data.pass_marks} |
                            ${data.students.length} students
                        </small>
                    </div>
                </div>
                <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Roll No</th>
                            <th>Marks Obtained <small class="text-muted">(out of ${data.full_marks})</small></th>
                            <th>Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            data.students.forEach(function(student, i) {
                html += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="${student.photo}" class="rounded-circle"
                                     style="width:32px;height:32px;object-fit:cover;">
                                <span style="font-size:0.875rem;font-weight:500">${student.name}</span>
                            </div>
                        </td>
                        <td><small>${student.roll_number}</small></td>
                        <td>
                            <input type="number"
                                   name="marks[${student.id}]"
                                   class="form-control form-control-sm marks-input"
                                   style="width:100px"
                                   min="0" max="${data.full_marks}"
                                   value="${student.existing_marks || ''}"
                                   placeholder="0"
                                   data-full="${data.full_marks}"
                                   oninput="calcGrade(this)">
                        </td>
                        <td>
                            <span class="grade-badge badge" id="grade-${student.id}"
                                  style="background:#f1f5f9;color:#475569">
                                ${student.existing_grade || '-'}
                            </span>
                        </td>
                        <td>
                            <input type="text"
                                   name="remarks[${student.id}]"
                                   class="form-control form-control-sm"
                                   style="width:120px"
                                   placeholder="Optional"
                                   value="${student.existing_remarks || ''}">
                        </td>
                    </tr>
                `;
            });

            html += `
                    </tbody>
                </table>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="fas fa-save me-2"></i>Save Marks
                    </button>
                    <a href="/dashboard/exams" class="btn btn-secondary">Cancel</a>
                </div>
            `;

            $('#marksContainer').html(html);

            // Re-init grade calculation
            initGradeCalc();
        },
        error: function() {
            $('#marksContainer').html(`
                <div class="alert alert-danger">Error loading students. Please try again.</div>
            `);
        }
    });
}

// ============================================
// 4. LIVE STUDENT SEARCH
// ============================================
let searchTimeout;
function liveStudentSearch(query) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        if (query.length < 2 && query.length > 0) return;

        $.ajax({
            url: '/dashboard/ajax/students-search',
            type: 'GET',
            data: { q: query },
            success: function(data) {
                renderStudentTable(data);
            }
        });
    }, 400);
}

function renderStudentTable(students) {
    let tbody = $('#studentsTableBody');
    if (students.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="8" class="text-center py-4 text-muted">
                    <i class="fas fa-user-graduate fa-2x mb-2 d-block"></i>
                    No students found
                </td>
            </tr>
        `);
        $('#totalCount').text('0');
        return;
    }

    let html = '';
    students.forEach(function(s, i) {
        html += `
            <tr>
                <td>${i + 1}</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <img src="${s.photo}" class="rounded-circle"
                             style="width:36px;height:36px;object-fit:cover;">
                        <div>
                            <div style="font-weight:500;font-size:0.875rem">${s.name}</div>
                            <small class="text-muted">${s.email}</small>
                        </div>
                    </div>
                </td>
                <td><small>${s.admission_number}</small></td>
                <td><small>${s.roll_number}</small></td>
                <td><small>${s.class} / ${s.section}</small></td>
                <td><small>${s.gender}</small></td>
                <td>
                    <span class="badge ${s.status === 'active' ? 'badge-active' : 'badge-inactive'}">
                        ${s.status.charAt(0).toUpperCase() + s.status.slice(1)}
                    </span>
                </td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="/dashboard/students/${s.id}" class="btn btn-sm btn-info text-white">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="/dashboard/students/${s.id}/edit" class="btn btn-sm btn-warning text-white">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/dashboard/students/${s.id}/id-card" class="btn btn-sm btn-secondary">
                            <i class="fas fa-id-card"></i>
                        </a>
                    </div>
                </td>
            </tr>
        `;
    });

    tbody.html(html);
    $('#totalCount').text(students.length);
}

// ============================================
// 5. MARK ALL ATTENDANCE
// ============================================
function markAll(status) {
    $(`input[type="radio"][value="${status}"]`).prop('checked', true);

    // Show toast
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: 'All marked as ' + status,
        showConfirmButton: false,
        timer: 1500
    });
}

// ============================================
// 6. GRADE CALCULATOR
// ============================================
function calcGrade(input) {
    const marks   = parseFloat(input.value);
    const full    = parseFloat(input.dataset.full);
    const pct     = (marks / full) * 100;
    const row     = input.closest('tr');
    const badge   = row.querySelector('.grade-badge');

    let grade = '-', bg = '#f1f5f9', color = '#475569';

    if (!isNaN(pct)) {
        if (pct >= 90)      { grade = 'A+'; bg = '#dcfce7'; color = '#166534'; }
        else if (pct >= 80) { grade = 'A';  bg = '#dcfce7'; color = '#166534'; }
        else if (pct >= 70) { grade = 'B';  bg = '#dbeafe'; color = '#1d4ed8'; }
        else if (pct >= 60) { grade = 'C';  bg = '#fef9c3'; color = '#854d0e'; }
        else if (pct >= 50) { grade = 'D';  bg = '#ffedd5'; color = '#9a3412'; }
        else if (pct >= 33) { grade = 'E';  bg = '#fce7f3'; color = '#9d174d'; }
        else                { grade = 'F';  bg = '#fee2e2'; color = '#991b1b'; }
    }

    if (badge) {
        badge.textContent      = grade;
        badge.style.background = bg;
        badge.style.color      = color;
    }
}

function initGradeCalc() {
    document.querySelectorAll('.marks-input').forEach(input => {
        input.addEventListener('input', function() { calcGrade(this); });
    });
}

// ============================================
// 7. AJAX DELETE WITH SWEETALERT
// ============================================
function ajaxDelete(formId, name) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Delete "' + name + '"? This cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

// ============================================
// 8. SHOW TOAST NOTIFICATION
// ============================================
function showToast(message, type = 'success') {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: type,
        title: message,
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
}

// Init on page load
$(document).ready(function() {
    initGradeCalc();
});

// ============================================
// GLOBAL AJAX DELETE FUNCTION
// ============================================
function ajaxDelete(url, rowId, itemName, countId = null) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'Delete "' + itemName + '"? This cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const btn = document.getElementById('del-btn-' + rowId);
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                btn.disabled = true;
            }

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    '_method': 'DELETE',
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function() {
                    $('#row-' + rowId).fadeOut(400, function() {
                        $(this).remove();
                        if (countId) {
                            let current = parseInt($('#' + countId).text()) || 0;
                            if (current > 0) $('#' + countId).text(current - 1);
                        }
                    });

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: '"' + itemName + '" deleted successfully!',
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                },
                error: function(xhr) {
                    let msg = 'Could not delete. Please try again.';
                    if (xhr.status === 403) msg = 'You do not have permission to delete this.';
                    if (xhr.status === 422) msg = 'This record cannot be deleted — it has related data.';

                    Swal.fire('Error!', msg, 'error');

                    if (btn) {
                        btn.innerHTML = '<i class="fas fa-trash"></i>';
                        btn.disabled = false;
                    }
                }
            });
        }
    });
}