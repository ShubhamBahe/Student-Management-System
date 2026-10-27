<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ID Card — {{ $student->user->name }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f1f5f9; display: flex; flex-direction: column; align-items: center; padding: 30px; }
        .id-card {
            width: 85.6mm;
            height: 54mm;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            position: relative;
        }
        /* Front Card */
        .card-header {
            background: linear-gradient(135deg, #1e293b, #2563eb);
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .card-header .school-name {
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            line-height: 1.3;
        }
        .card-header .school-sub {
            color: rgba(255,255,255,0.7);
            font-size: 7px;
        }
        .card-logo {
            width: 28px;
            height: 28px;
            background: rgba(255,255,255,0.2);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 14px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .card-body {
            display: flex;
            gap: 10px;
            padding: 10px 12px;
        }
        .student-photo {
            width: 50px;
            height: 60px;
            border-radius: 6px;
            object-fit: cover;
            border: 2px solid #2563eb;
            flex-shrink: 0;
        }
        .student-photo-placeholder {
            width: 50px;
            height: 60px;
            border-radius: 6px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            border: 2px solid #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
            font-weight: 800;
            flex-shrink: 0;
        }
        .student-details { flex: 1; }
        .student-name {
            font-size: 11px;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
            line-height: 1.2;
        }
        .detail-row {
            display: flex;
            gap: 4px;
            margin-bottom: 2px;
        }
        .detail-label {
            font-size: 7px;
            color: #94a3b8;
            font-weight: 600;
            width: 40px;
            flex-shrink: 0;
        }
        .detail-value {
            font-size: 7.5px;
            color: #1e293b;
            font-weight: 600;
        }
        .card-footer {
            background: linear-gradient(135deg, #1e293b, #2563eb);
            padding: 5px 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
        }
        .card-footer .label {
            font-size: 7px;
            color: rgba(255,255,255,0.7);
        }
        .card-footer .value {
            font-size: 8px;
            color: #fff;
            font-weight: 700;
        }
        .id-badge {
            background: rgba(255,255,255,0.2);
            color: #fff;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 7px;
            font-weight: 700;
        }
        /* Back Card */
        .back-card {
            width: 85.6mm;
            height: 54mm;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            margin-top: 15px;
        }
        .back-header {
            background: linear-gradient(135deg, #1e293b, #2563eb);
            padding: 8px 12px;
            text-align: center;
        }
        .back-header p { color: rgba(255,255,255,0.8); font-size: 8px; }
        .back-body { padding: 10px 12px; }
        .back-row { display: flex; gap: 6px; margin-bottom: 5px; align-items: flex-start; }
        .back-icon { color: #2563eb; font-size: 9px; width: 12px; flex-shrink: 0; margin-top: 1px; }
        .back-text { font-size: 8px; color: #475569; line-height: 1.4; }
        .barcode-area {
            text-align: center;
            padding: 5px 0;
            border-top: 1px solid #e2e8f0;
            margin-top: 5px;
        }
        .barcode-text { font-size: 8px; color: #94a3b8; letter-spacing: 3px; }
        .barcode-num { font-size: 7px; color: #cbd5e1; }
        /* Print Controls */
        .print-controls {
            text-align: center;
            margin-bottom: 25px;
        }
        .print-controls button, .print-controls a {
            padding: 10px 25px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-block;
            margin: 0 5px;
        }
        .cards-container { display: flex; flex-direction: column; align-items: center; }
        .card-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        @media print {
            body { background: #fff; padding: 5mm; }
            .print-controls { display: none; }
            .id-card, .back-card { box-shadow: none; border: 1px solid #e2e8f0; }
        }
    </style>
</head>
<body>
    {{-- Controls --}}
    <div class="print-controls">
        <button onclick="window.print()" style="background:#2563eb;color:#fff">
            Print ID Card
        </button>
        <a href="{{ route('dashboard.students.show', $student) }}" style="background:#64748b;color:#fff">Back</a>
    </div>
    <div class="cards-container">
        {{-- FRONT --}}
        <div class="card-label">Front Side</div>
        <div class="id-card">
            {{-- Header --}}
            <div class="card-header">
                <div class="card-logo">
                    {{ strtoupper(substr(setting('school_name', 'S'), 0, 1)) }}
                </div>
                <div>
                    <div class="school-name">{{ setting('school_name', 'Al-Noor Public School') }}</div>
                    <div class="school-sub">{{ setting('school_address', 'Hyderabad, Sindh') }}</div>
                </div>
                <span class="id-badge" style="margin-left:auto">STUDENT</span>
            </div>
            {{-- Body --}}
            <div class="card-body">
                {{-- Photo --}}
                <div class="student-photo-placeholder">
                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                </div>
                {{-- Details --}}
                <div class="student-details">
                    <div class="student-name">{{ $student->user->name }}</div>
                    <div class="detail-row">
                        <span class="detail-label">Adm. No</span>
                        <span class="detail-value">{{ $student->admission_number }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Roll No</span>
                        <span class="detail-value">{{ $student->roll_number }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Class</span>
                        <span class="detail-value">{{ $student->class->name ?? '-' }} — {{ $student->section->name ?? '-' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Session</span>
                        <span class="detail-value">{{ $student->academicYear->name ?? '2025-2026' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Gender</span>
                        <span class="detail-value">{{ ucfirst($student->user->gender ?? '-') }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">D.O.B</span>
                        <span class="detail-value">{{ $student->user->date_of_birth ? \Carbon\Carbon::parse($student->user->date_of_birth)->format('d/m/Y') : '-' }}</span>
                    </div>
                </div>
            </div>
            {{-- Footer --}}
            <div class="card-footer">
                <div>
                    <div class="label">Valid Until</div>
                    <div class="value">March 2026</div>
                </div>
                <div style="text-align:center">
                    <div class="label">Signature</div>
                    <div style="border-top:1px solid rgba(255,255,255,0.4);width:60px;margin-top:6px"></div>
                </div>
                <div style="text-align:right">
                    <div class="label">Principal</div>
                    <div style="border-top:1px solid rgba(255,255,255,0.4);width:50px;margin-top:6px;margin-left:auto"></div>
                </div>
            </div>
        </div>
        {{-- BACK --}}
        <div style="margin-top:20px"></div>
        <div class="card-label">Back Side</div>
        <div class="back-card">
            <div class="back-header">
                <p style="color:#fff;font-size:9px;font-weight:700">{{ setting('school_name') }}</p>
                <p>If found, please return to the school address below</p>
            </div>
            <div class="back-body">
                <div class="back-row">
                    <span class="back-icon">&#9679;</span>
                    <span class="back-text">
                        <strong>Address:</strong> 
                        {{ setting('school_address') }}
                    </span>
                </div>
                <div class="back-row">
                    <span class="back-icon">&#9679;</span>
                    <span class="back-text">
                        <strong>Phone:</strong> 
                        {{ setting('school_phone') }}
                    </span>
                </div>
                <div class="back-row">
                    <span class="back-icon">&#9679;</span>
                    <span class="back-text">
                        <strong>Email:</strong> 
                        {{ setting('school_email') }}
                    </span>
                </div>
                <div class="back-row">
                    <span class="back-icon">&#9679;</span>
                    <span class="back-text">
                        <strong>Timings:</strong> 
                        Mon–Sat: 7:30 AM – 2:00 PM
                    </span>
                </div>
                <div class="back-row">
                    <span class="back-icon">&#9679;</span>
                    <span class="back-text" style="color:#dc2626">
                        <strong>Note:</strong> 
                        This card must be carried at all times. Loss must be reported immediately.
                    </span>
                </div>
            </div>
            <div class="barcode-area">
                <div class="barcode-text">||| || | || ||| | || ||| || | ||</div>
                <div class="barcode-num">{{ $student->admission_number }}</div>
            </div>
        </div>
    </div>
</body>
</html>