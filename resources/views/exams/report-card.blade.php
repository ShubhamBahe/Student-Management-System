<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Card — {{ $student->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; background: #fff; }

        .page { width: 210mm; min-height: 297mm; margin: 0 auto; padding: 15mm; }

        /* Header */
        .header { text-align: center; border-bottom: 3px solid #2563eb; padding-bottom: 15px; margin-bottom: 15px; }
        .header .school-name { font-size: 22px; font-weight: 800; color: #1e293b; letter-spacing: 0.5px; }
        .header .school-info { font-size: 11px; color: #64748b; margin-top: 3px; }
        .header .report-title { background: #2563eb; color: #fff; padding: 6px 30px; border-radius: 20px; display: inline-block; font-size: 13px; font-weight: 700; margin-top: 10px; letter-spacing: 1px; }

        /* Student Info */
        .student-info { display: flex; justify-content: space-between; margin: 15px 0; gap: 15px; }
        .info-box { flex: 1; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; background: #f8fafc; }
        .info-box table { width: 100%; }
        .info-box table td { padding: 4px 0; font-size: 11px; }
        .info-box table td:first-child { color: #64748b; width: 45%; font-weight: 600; }
        .info-box table td:last-child { color: #1e293b; font-weight: 500; }

        /* Marks Table */
        .marks-table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        .marks-table th { background: #1e293b; color: #fff; padding: 10px 8px; text-align: center; font-size: 11px; font-weight: 600; letter-spacing: 0.3px; }
        .marks-table th:first-child { text-align: left; }
        .marks-table td { padding: 9px 8px; border-bottom: 1px solid #e2e8f0; font-size: 11px; text-align: center; }
        .marks-table td:first-child { text-align: left; font-weight: 500; }
        .marks-table tr:nth-child(even) { background: #f8fafc; }
        .marks-table tr:last-child td { border-bottom: none; }

        /* Grade Badge */
        .grade-A { color: #166534; font-weight: 700; }
        .grade-B { color: #1d4ed8; font-weight: 700; }
        .grade-C { color: #854d0e; font-weight: 700; }
        .grade-D { color: #9a3412; font-weight: 700; }
        .grade-F { color: #991b1b; font-weight: 700; }
        .grade-P { color: #166534; }
        .grade-F-fail { color: #991b1b; }

        /* Result Summary */
        .result-summary { display: flex; gap: 15px; margin: 15px 0; }
        .summary-box { flex: 1; border-radius: 8px; padding: 15px; text-align: center; border: 1px solid #e2e8f0; }
        .summary-box .label { font-size: 10px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px; }
        .summary-box .value { font-size: 22px; font-weight: 800; }

        /* Progress Bar */
        .progress-bar-outer { background: #e2e8f0; border-radius: 10px; height: 8px; margin-top: 5px; }
        .progress-bar-inner { height: 8px; border-radius: 10px; background: #2563eb; }

        /* Grade Scale */
        .grade-scale { border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px; margin: 15px 0; background: #f8fafc; }
        .grade-scale table { width: 100%; }
        .grade-scale td { padding: 3px 8px; font-size: 10px; text-align: center; }
        .grade-scale th { font-size: 10px; color: #64748b; padding: 4px 8px; text-align: center; }

        /* Signatures */
        .signatures { display: flex; justify-content: space-between; margin-top: 30px; padding-top: 15px; border-top: 1px solid #e2e8f0; }
        .sig-box { text-align: center; width: 28%; }
        .sig-line { border-top: 1px solid #1e293b; margin-top: 35px; padding-top: 5px; font-size: 10px; color: #64748b; font-weight: 600; }

        /* Footer */
        .footer { text-align: center; margin-top: 20px; padding-top: 10px; border-top: 1px solid #e2e8f0; font-size: 10px; color: #94a3b8; }

        /* Print */
        @media print {
            .no-print { display: none !important; }
            body { background: #fff; }
            .page { padding: 10mm; }
        }
    </style>
</head>
<body>
    {{-- Print Button --}}
    <div class="no-print" style="text-align:center;padding:15px;background:#f8fafc;border-bottom:1px solid #e2e8f0">
        <button onclick="window.print()" style="background:#2563eb;color:#fff;border:none;padding:10px 30px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;margin-right:10px">
            Print Report Card
        </button>
        <a href="{{ route('dashboard.exams.index') }}" style="background:#64748b;color:#fff;border:none;padding:10px 30px;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;text-decoration:none">
            Back
        </a>
    </div>
    <div class="page">
        {{-- HEADER --}}
        <div class="header">
            <div class="school-name">{{ setting('school_name', 'Al-Noor Public School') }}</div>
            <div class="school-info">
                {{ setting('school_address') }} | Tel: {{ setting('school_phone') }} | {{ setting('school_email') }}
            </div>
            <div class="report-title">STUDENT REPORT CARD</div>
        </div>
        {{-- STUDENT INFO --}}
        <div class="student-info">
            <div class="info-box">
                <table>
                    <tr>
                        <td>Student Name:</td>
                        <td>{{ $student->user->name }}</td>
                    </tr>
                    <tr>
                        <td>Admission No:</td>
                        <td>{{ $student->admission_number }}</td>
                    </tr>
                    <tr>
                        <td>Roll Number:</td>
                        <td>{{ $student->roll_number }}</td>
                    </tr>
                    <tr>
                        <td>Class:</td>
                        <td>{{ $student->class->name ?? '-' }} — {{ $student->section->name ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div class="info-box">
                <table>
                    <tr>
                        <td>Exam Name:</td>
                        <td>{{ $exam->name }}</td>
                    </tr>
                    <tr>
                        <td>Academic Year:</td>
                        <td>{{ $exam->academicYear->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Exam Period:</td>
                        <td>{{ \Carbon\Carbon::parse($exam->start_date)->format('d M') }} — {{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td>Issue Date:</td>
                        <td>{{ now()->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>
        {{-- MARKS TABLE --}}
        <table class="marks-table">
            <thead>
                <tr>
                    <th style="width:5%">#</th>
                    <th style="text-align:left">Subject</th>
                    <th>Full Marks</th>
                    <th>Pass Marks</th>
                    <th>Marks Obtained</th>
                    <th>Percentage</th>
                    <th>Grade</th>
                    <th>Result</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marks as $i => $mark)
                    @php
                        $pct    = $mark->full_marks > 0 ? round(($mark->marks_obtained / $mark->full_marks) * 100, 1) : 0;
                        $passed = $mark->marks_obtained >= ($mark->subject->pass_marks ?? 33);
                    @endphp
                    <tr>
                        <td style="text-align:center">{{ $i + 1 }}</td>
                        <td>{{ $mark->subject->name ?? '-' }}</td>
                        <td>{{ $mark->full_marks }}</td>
                        <td>{{ $mark->subject->pass_marks ?? 33 }}</td>
                        <td style="font-weight:700">{{ $mark->marks_obtained }}</td>
                        <td>{{ $pct }}%</td>
                        <td class="grade-{{ $mark->grade }}">{{ $mark->grade }}</td>
                        <td class="{{ $passed ? 'grade-P' : 'grade-F-fail' }}">{{ $passed ? 'Pass' : 'Fail' }}</td>
                        <td style="color:#64748b">{{ $mark->remarks ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#1e293b;color:#fff">
                    <td colspan="4" style="text-align:right;padding:10px 8px;font-weight:700;color:#fff">Total</td>
                    <td style="text-align:center;font-weight:800;font-size:13px;color:#fff">{{ $totalObtained }} / {{ $totalFull }}</td>
                    <td style="text-align:center;font-weight:800;color:#fff">{{ $percentage }}%</td>
                    <td style="text-align:center;font-weight:800;color:#fff">{{ $grade }}</td>
                    <td style="text-align:center;color:#fff">{{ $percentage >= 33 ? 'Pass' : 'Fail' }}</td>
                    <td style="color:#fff">-</td>
                </tr>
            </tfoot>
        </table>
        {{-- RESULT SUMMARY --}}
        <div class="result-summary">
            <div class="summary-box" style="background:#dbeafe;border-color:#bfdbfe">
                <div class="label">Total Marks</div>
                <div class="value" style="color:#1d4ed8">{{ $totalObtained }}/{{ $totalFull }}</div>
            </div>
            <div class="summary-box" style="background:#dcfce7;border-color:#bbf7d0">
                <div class="label">Percentage</div>
                <div class="value" style="color:#15803d">{{ $percentage }}%</div>
                <div class="progress-bar-outer">
                    <div class="progress-bar-inner" style="width:{{ $percentage }}%;background:#15803d"></div>
                </div>
            </div>
            <div class="summary-box" style="background:#fef9c3;border-color:#fde68a">
                <div class="label">Overall Grade</div>
                <div class="value" style="color:#854d0e">{{ $grade }}</div>
            </div>
            <div class="summary-box" style="{{ $percentage >= 33 ? 'background:#dcfce7;border-color:#bbf7d0' : 'background:#fee2e2;border-color:#fca5a5' }}">
                <div class="label">Final Result</div>
                <div class="value" style="{{ $percentage >= 33 ? 'color:#15803d' : 'color:#991b1b' }}">
                    {{ $percentage >= 33 ? 'PASS' : 'FAIL' }}
                </div>
            </div>
            <div class="summary-box" style="background:#f3e8ff;border-color:#d8b4fe">
                <div class="label">Subjects Passed</div>
                <div class="value" style="color:#7e22ce">
                    {{ $marks->filter(fn($m) => $m->marks_obtained >= ($m->subject->pass_marks ?? 33))->count() }}/{{ $marks->count() }}
                </div>
            </div>
        </div>
        {{-- GRADE SCALE --}}
        <div class="grade-scale">
            <div style="font-weight:700;font-size:11px;margin-bottom:8px;color:#1e293b">Grading Scale</div>
            <table>
                <tr>
                    <th>Grade</th>
                    <th>A+</th>
                    <th>A</th>
                    <th>B</th>
                    <th>C</th>
                    <th>D</th>
                    <th>E</th>
                    <th>F</th>
                </tr>
                <tr>
                    <td style="font-weight:600;color:#64748b">Percentage</td>
                    <td>90-100%</td>
                    <td>80-89%</td>
                    <td>70-79%</td>
                    <td>60-69%</td>
                    <td>50-59%</td>
                    <td>33-49%</td>
                    <td>Below 33%</td>
                </tr>
            </table>
        </div>
        {{-- REMARKS --}}
        <div style="border:1px solid #e2e8f0;border-radius:8px;padding:12px;margin:15px 0;background:#f8fafc">
            <div style="font-weight:700;font-size:11px;color:#1e293b;margin-bottom:5px">Class Teacher Remarks</div>
            <div style="color:#64748b;font-size:11px;min-height:30px">
                @if($percentage >= 80)
                    Outstanding performance! Keep up the excellent work.
                @elseif($percentage >= 60)
                    Good performance. Continue to work hard.
                @elseif($percentage >= 33)
                    Satisfactory performance. More effort needed in weak subjects.
                @else
                    Needs significant improvement. Please study harder.
                @endif
            </div>
        </div>
        {{-- SIGNATURES --}}
        <div class="signatures">
            <div class="sig-box">
                <div class="sig-line">Class Teacher</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Examination Controller</div>
            </div>
            <div class="sig-box">
                <div class="sig-line">Principal</div>
            </div>
        </div>
        {{-- FOOTER --}}
        <div class="footer">
            This is a computer-generated report card. | {{ setting('school_name') }} | {{ now()->format('d M Y') }}
        </div>
    </div>
</body>
</html>