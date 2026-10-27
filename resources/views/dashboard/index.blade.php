    @extends('layouts.dashboard')
    @section('title', 'Dashboard')

    @section('content')
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1 fw-bold" style="color:#1e293b">
                    Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }},
                    {{ auth()->user()->name }}
                </h4>
                <p class="text-muted mb-0" style="font-size:0.875rem">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('dashboard.students.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Add Student
                </a>
                <a href="{{ route('dashboard.attendance.mark') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-clipboard-check me-1"></i>Mark Attendance
                </a>
            </div>
        </div>

        {{-- Row 1: Main Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('dashboard.students.index') }}" style="text-decoration:none">
                    <div class="stat-card" style="border-left: 4px solid #2563eb;cursor:pointer">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number text-primary">{{ number_format($stats['total_students']) }}</div>
                                <div class="stat-label">Total Students</div>
                                <small class="text-success mt-1 d-block">
                                    <i class="fas fa-arrow-up"></i> Active Enrollment
                                </small>
                            </div>
                            <div class="stat-icon" style="background:#dbeafe">
                                <i class="fas fa-user-graduate" style="color:#2563eb"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height:4px;border-radius:2px">
                                <div class="progress-bar bg-primary" style="width:75%"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('dashboard.teachers.index') }}" style="text-decoration:none">
                    <div class="stat-card" style="border-left: 4px solid #10b981;cursor:pointer">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number text-success">{{ number_format($stats['total_teachers']) }}</div>
                                <div class="stat-label">Total Teachers</div>
                                <small class="text-success mt-1 d-block">
                                    <i class="fas fa-check-circle"></i> All Active
                                </small>
                            </div>
                            <div class="stat-icon" style="background:#dcfce7">
                                <i class="fas fa-chalkboard-teacher" style="color:#10b981"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height:4px;border-radius:2px">
                                <div class="progress-bar bg-success" style="width:90%"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('dashboard.attendance.index') }}" style="text-decoration:none">
                    <div class="stat-card" style="border-left: 4px solid #f59e0b;cursor:pointer">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number" style="color:#f59e0b">{{ $stats['present_today'] }}</div>
                                <div class="stat-label">Present Today</div>
                                <small class="text-danger mt-1 d-block">
                                    <i class="fas fa-times-circle"></i> {{ $stats['absent_today'] }} absent
                                </small>
                            </div>
                            <div class="stat-icon" style="background:#fef9c3">
                                <i class="fas fa-clipboard-check" style="color:#f59e0b"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            @php $total = $stats['present_today'] + $stats['absent_today']; $pct = $total > 0 ? round($stats['present_today']/$total*100) : 0; @endphp
                            <div class="progress" style="height:4px;border-radius:2px">
                                <div class="progress-bar bg-warning" style="width:{{ $pct }}%"></div>
                            </div>
                            <small class="text-muted" style="font-size:0.75rem">{{ $pct }}% attendance rate</small>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6">
                <a href="{{ route('dashboard.fees.index') }}" style="text-decoration:none">
                    <div class="stat-card" style="border-left: 4px solid #8b5cf6;cursor:pointer">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="stat-number" style="color:#8b5cf6">PKR {{ number_format($stats['fees_collected']) }}</div>
                                <div class="stat-label">Fees Collected</div>
                                <small class="text-danger mt-1 d-block">
                                    <i class="fas fa-exclamation-circle"></i> PKR {{ number_format($stats['fees_pending']) }} pending
                                </small>
                            </div>
                            <div class="stat-icon" style="background:#ede9fe">
                                <i class="fas fa-money-bill-wave" style="color:#8b5cf6"></i>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="progress" style="height:4px;border-radius:2px">
                                <div class="progress-bar" style="background:#8b5cf6;width:65%"></div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div> {{-- ✅ Row 1 closed --}}

        {{-- Row 2: Secondary Stats --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-2 col-md-4">
                <a href="{{ route('dashboard.classes.index') }}" style="text-decoration:none">
                    <div class="stat-card text-center py-3" style="cursor:pointer">
                        <div class="stat-icon mx-auto mb-2" style="background:#e0e7ff">
                            <i class="fas fa-school" style="color:#4f46e5"></i>
                        </div>
                        <div class="fw-bold fs-5" style="color:#1e293b">{{ $stats['total_classes'] }}</div>
                        <div class="stat-label">Classes</div>
                    </div>
                </a>
            </div>
            <div class="col-xl-2 col-md-4">
                <a href="{{ route('dashboard.users.index') }}" style="text-decoration:none">
                    <div class="stat-card text-center py-3" style="cursor:pointer">
                        <div class="stat-icon mx-auto mb-2" style="background:#fce7f3">
                            <i class="fas fa-users" style="color:#db2777"></i>
                        </div>
                        <div class="fw-bold fs-5" style="color:#1e293b">{{ $stats['total_parents'] }}</div>
                        <div class="stat-label">Parents</div>
                    </div>
                </a>
            </div>
            <div class="col-xl-2 col-md-4">
                <a href="{{ route('dashboard.library.books.index') }}" style="text-decoration:none">
                    <div class="stat-card text-center py-3" style="cursor:pointer">
                        <div class="stat-icon mx-auto mb-2" style="background:#ffedd5">
                            <i class="fas fa-book" style="color:#ea580c"></i>
                        </div>
                        <div class="fw-bold fs-5" style="color:#1e293b">{{ $stats['total_books'] }}</div>
                        <div class="stat-label">Books</div>
                    </div>
                </a>
            </div>
            <div class="col-xl-2 col-md-4">
                <a href="{{ route('dashboard.exams.index') }}" style="text-decoration:none">
                    <div class="stat-card text-center py-3" style="cursor:pointer">
                        <div class="stat-icon mx-auto mb-2" style="background:#f0fdf4">
                            <i class="fas fa-file-alt" style="color:#15803d"></i>
                        </div>
                        <div class="fw-bold fs-5" style="color:#1e293b">{{ $stats['upcoming_exams'] }}</div>
                        <div class="stat-label">Exams</div>
                    </div>
                </a>
            </div>
            <div class="col-xl-2 col-md-4">
                <a href="{{ route('dashboard.attendance.index') }}" style="text-decoration:none">
                    <div class="stat-card text-center py-3" style="cursor:pointer">
                        <div class="stat-icon mx-auto mb-2" style="background:#fef9c3">
                            <i class="fas fa-check" style="color:#ca8a04"></i>
                        </div>
                        <div class="fw-bold fs-5 text-warning">{{ $pct }}%</div>
                        <div class="stat-label">Attendance %</div>
                    </div>
                </a>
            </div>
            <div class="col-xl-2 col-md-4">
                <a href="{{ route('dashboard.fees.index') }}" style="text-decoration:none">
                    <div class="stat-card text-center py-3" style="cursor:pointer">
                        <div class="stat-icon mx-auto mb-2" style="background:#fee2e2">
                            <i class="fas fa-clock" style="color:#dc2626"></i>
                        </div>
                        <div class="fw-bold fs-5 text-danger">{{ $stats['overdue_fees'] }}</div>
                        <div class="stat-label">Overdue Fees</div>
                    </div>
                </a>
            </div>
        </div> {{-- ✅ Row 2 closed --}}

        {{-- Row 3: Charts --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-8">
                <div class="page-card h-100">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h6 class="fw-bold mb-0" style="color:#1e293b">Attendance Overview</h6>
                            <small class="text-muted">Last 6 months performance</small>
                        </div>
                        <div class="d-flex gap-2">
                            <span class="badge" style="background:#dbeafe;color:#1d4ed8;font-size:0.75rem">
                                <i class="fas fa-circle me-1" style="font-size:8px"></i>Present
                            </span>
                            <span class="badge" style="background:#fee2e2;color:#991b1b;font-size:0.75rem">
                                <i class="fas fa-circle me-1" style="font-size:8px"></i>Absent
                            </span>
                        </div>
                    </div>
                    <canvas id="attendanceChart" height="90"></canvas>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="page-card h-100">
                    <div class="mb-4">
                        <h6 class="fw-bold mb-0" style="color:#1e293b">Today's Attendance</h6>
                        <small class="text-muted">{{ now()->format('d M Y') }}</small>
                    </div>
                    <div style="position:relative;height:180px;display:flex;align-items:center;justify-content:center">
                        <canvas id="todayChart"></canvas>
                        <div style="position:absolute;text-align:center">
                            <div style="font-size:1.5rem;font-weight:700;color:#1e293b">{{ $pct }}%</div>
                            <div style="font-size:0.7rem;color:#64748b">Present</div>
                        </div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <div class="p-2 rounded text-center" style="background:#dbeafe">
                                <div class="fw-bold" style="color:#1d4ed8">{{ $stats['present_today'] }}</div>
                                <small style="color:#1d4ed8">Present</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded text-center" style="background:#fee2e2">
                                <div class="fw-bold" style="color:#991b1b">{{ $stats['absent_today'] }}</div>
                                <small style="color:#991b1b">Absent</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> {{-- ✅ Row 3 closed --}}

        {{-- Row 4: Fees Chart + Students by Class --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-6">
                <div class="page-card">
                    <div class="mb-4">
                        <h6 class="fw-bold mb-0" style="color:#1e293b">Monthly Fee Collection</h6>
                        <small class="text-muted">Last 6 months revenue</small>
                    </div>
                    <canvas id="feesChart" height="120"></canvas>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="page-card">
                    <div class="mb-4">
                        <h6 class="fw-bold mb-0" style="color:#1e293b">Students by Class</h6>
                        <small class="text-muted">Enrollment distribution</small>
                    </div>
                    <canvas id="classChart" height="120"></canvas>
                </div>
            </div>
        </div> {{-- ✅ Row 4 closed --}}

        {{-- Row 5: Recent Students + Upcoming Exams + Notices --}}
        <div class="row g-3">
            <div class="col-xl-5">
                <div class="page-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="color:#1e293b">Recent Students</h6>
                        <a href="{{ route('dashboard.students.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Class</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents as $student)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-white"
                                                    style="width:34px;height:34px;min-width:34px;font-size:0.8rem;
                                                    background:{{ $student->user->gender === 'female' ? '#db2777' : '#2563eb' }}">
                                                    {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div style="font-weight:500;font-size:0.8rem">{{ Str::limit($student->user->name, 18) }}</div>
                                                    <small class="text-muted" style="font-size:0.7rem">{{ $student->admission_number }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $student->class->name ?? '-' }}/{{ $student->section->name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $student->status === 'active' ? 'active' : 'inactive' }}">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-3">No students yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="page-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="color:#1e293b">Upcoming Exams</h6>
                        <a href="{{ route('dashboard.exams.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    @forelse($upcomingExams as $exam)
                        <div class="d-flex align-items-center gap-3 mb-3 p-2 rounded" style="background:#f8fafc;border-left:3px solid #2563eb">
                            <div>
                                <div class="fw-500" style="font-size:0.875rem;font-weight:500">{{ $exam->name }}</div>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($exam->start_date)->format('d M') }}
                                    — {{ \Carbon\Carbon::parse($exam->end_date)->format('d M Y') }}
                                </small>
                            </div>
                            <span class="ms-auto badge" style="background:#dbeafe;color:#1d4ed8;white-space:nowrap">
                                {{ ucfirst($exam->status) }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-file-alt fa-2x mb-2 d-block" style="color:#cbd5e1"></i>
                            No upcoming exams
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="col-xl-3">
                <div class="page-card">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0" style="color:#1e293b">Notices</h6>
                        <a href="{{ route('dashboard.notices.index') }}" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    @foreach(\App\Models\Notice::latest()->take(4)->get() as $notice)
                        <div class="mb-3 pb-3" style="border-bottom:1px solid #f1f5f9">
                            <div style="font-size:0.8rem;font-weight:500;color:#1e293b">{{ Str::limit($notice->title, 35) }}</div>
                            <div class="d-flex justify-content-between mt-1">
                                <span class="badge" style="font-size:0.65rem;background:#f0fdf4;color:#15803d">
                                    {{ ucfirst($notice->audience) }}
                                </span>
                                <small class="text-muted" style="font-size:0.7rem">{{ $notice->created_at->diffForHumans() }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div> {{-- ✅ Row 5 closed --}}

    @endsection

    @push('scripts')
        <script>
            const attendanceData = @json($attendanceData);
            const feesData       = @json($feesData);

            new Chart(document.getElementById('attendanceChart'), {
                type: 'bar',
                data: {
                    labels: attendanceData.map(d => d.month),
                    datasets: [
                        {
                            label: 'Present',
                            data: attendanceData.map(d => d.present),
                            backgroundColor: 'rgba(37,99,235,0.85)',
                            borderRadius: 6,
                            borderSkipped: false,
                        },
                        {
                            label: 'Absent',
                            data: attendanceData.map(d => d.absent),
                            backgroundColor: 'rgba(239,68,68,0.5)',
                            borderRadius: 6,
                            borderSkipped: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: ctx => ctx.dataset.label + ': ' + ctx.parsed.y + ' students'
                            }
                        }
                    },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    }
                }
            });

            new Chart(document.getElementById('todayChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Present', 'Absent'],
                    datasets: [{
                        data: [{{ $stats['present_today'] }}, {{ $stats['absent_today'] }}],
                        backgroundColor: ['#2563eb', '#fca5a5'],
                        borderWidth: 0,
                        cutout: '75%',
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false }, tooltip: { enabled: true } }
                }
            });

            new Chart(document.getElementById('feesChart'), {
                type: 'line',
                data: {
                    labels: feesData.map(d => d.month),
                    datasets: [{
                        label: 'Collected (PKR)',
                        data: feesData.map(d => d.collected),
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139,92,246,0.1)',
                        borderWidth: 2.5,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#8b5cf6',
                        pointRadius: 4,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 }, callback: v => 'PKR ' + v.toLocaleString() } },
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } }
                    }
                }
            });

            const classLabels = @json($classStats['labels'] ?? []);
            const classData   = @json($classStats['data'] ?? []);
            new Chart(document.getElementById('classChart'), {
                type: 'bar',
                data: {
                    labels: classLabels,
                    datasets: [{
                        label: 'Students',
                        data: classData,
                        backgroundColor: [
                            '#2563eb','#10b981','#f59e0b','#8b5cf6','#ec4899',
                            '#06b6d4','#84cc16','#f97316','#6366f1','#14b8a6'
                        ],
                        borderRadius: 6,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, ticks: { font: { size: 11 } } },
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } }
                    }
                }
            });
        </script>
    @endpush