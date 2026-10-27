<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ setting('school_name', 'School Management System') }}</title>
    <link rel="icon" type="image/png" href="https://ui-avatars.com/api/?name=SMS&background=2563eb&color=fff&size=64&bold=true&font-size=0.4">
    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- DataTables --}}
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    {{-- SweetAlert2 --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #10b981;
            --sidebar-bg: #1e293b;
            --sidebar-text: #94a3b8;
            --sidebar-active: #2563eb;
        }
        body { font-family: 'Inter', sans-serif; background: #f1f5f9; }
        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--sidebar-bg);
            height: 100vh;
            position: fixed;
            top: 0; left: 0;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            transition: all 0.3s;
            scrollbar-width: thin;
            scrollbar-color: rgba(255,255,255,0.1) transparent;
        }
        .sidebar::-webkit-scrollbar {
            width: 4px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 2px;
        }
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.3);
        }
        .sidebar-brand {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand h5 {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            margin: 0;
        }
        .sidebar-nav { padding: 10px 0; }
        .sidebar-nav .nav-section {
            padding: 8px 20px 4px;
            font-size: 10px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.3);
            font-weight: 600;
            letter-spacing: 1px;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background: rgba(37, 99, 235, 0.15);
            color: #fff;
            border-left-color: var(--primary);
        }
        .sidebar-nav a i { width: 18px; text-align: center; }
        /* Main content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            transition: all 0.3s;
        }
        /* Topbar */
        .topbar {
            background: #fff;
            padding: 12px 24px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .stat-card .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
        }
        .stat-card .stat-label {
            font-size: 0.8rem;
            color: #64748b;
            font-weight: 500;
        }
        /* Content area */
        .content-area { padding: 24px; }
        .page-card {
            background: #fff;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        /* Breadcrumb */
        .breadcrumb { background: none; padding: 0; margin: 0; font-size: 0.8rem; }
        .breadcrumb-item + .breadcrumb-item::before { color: #94a3b8; }
        /* Table */
        .table th { background: #f8fafc; font-size: 0.8rem; font-weight: 600; color: #475569; }
        .table td { font-size: 0.875rem; vertical-align: middle; }
        /* Badges */
        .badge-active   { background: #dcfce7; color: #166534; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .badge-pending  { background: #fef9c3; color: #854d0e; }
        /* Mobile */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- SIDEBAR --}}
    <div class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ auth()->user()->photo_url }}"
                    class="rounded-circle"
                    style="width:36px;height:36px;object-fit:cover;flex-shrink:0;"
                    alt="{{ auth()->user()->name }}"
                    onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2563eb&color=fff&size=100&bold=true'">
                <div style="color:#fff;font-weight:600;font-size:0.875rem;line-height:1.2">
                    {{ setting('school_name', 'School MS') }}
                </div>
                <div style="color:rgba(255,255,255,0.5);font-size:0.7rem">
                    {{ str_replace('_', ' ', ucfirst(auth()->user()->getRoleNames()->first() ?? 'User')) }}
                </div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard.index') }}" class="{{ request()->routeIs('dashboard.index') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
            @canany(['view students', 'create students'])
                <div class="nav-section">Students</div>
                <a href="{{ route('dashboard.students.index') }}" class="{{ request()->routeIs('dashboard.students.*') ? 'active' : '' }}">
                    <i class="fas fa-user-graduate"></i> Students
                </a>
            @endcanany
            @canany(['view teachers', 'create teachers'])
                <div class="nav-section">Staff</div>
                <a href="{{ route('dashboard.teachers.index') }}" class="{{ request()->routeIs('dashboard.teachers.*') ? 'active' : '' }}">
                    <i class="fas fa-chalkboard-teacher"></i> Teachers
                </a>
            @endcanany
            @can('view classes')
                <div class="nav-section">Academic</div>
                <a href="{{ route('dashboard.classes.index') }}" class="{{ request()->routeIs('dashboard.classes.*') ? 'active' : '' }}">
                    <i class="fas fa-school"></i> Classes
                </a>
                <a href="{{ route('dashboard.sections.index') }}" class="{{ request()->routeIs('dashboard.sections.*') ? 'active' : '' }}">
                    <i class="fas fa-sitemap"></i> Sections
                </a>
                <a href="{{ route('dashboard.subjects.index') }}" class="{{ request()->routeIs('dashboard.subjects.*') ? 'active' : '' }}">
                    <i class="fas fa-book-open"></i> Subjects
                </a>
            @endcan
            @can('view attendance')
            <a href="{{ route('dashboard.attendance.index') }}" class="{{ request()->routeIs('dashboard.attendance.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i> Attendance
            </a>
            @endcan
            @can('view exams')
            <div class="nav-section">Examination</div>
            <a href="{{ route('dashboard.exams.index') }}" class="{{ request()->routeIs('dashboard.exams.*') ? 'active' : '' }}">
                <i class="fas fa-file-alt"></i> Exams
            </a>
            <a href="{{ route('dashboard.marks.entry') }}" class="{{ request()->routeIs('dashboard.marks.*') ? 'active' : '' }}">
                <i class="fas fa-pen"></i> Marks Entry
            </a>
            @endcan
            @can('view fees')
            <div class="nav-section">Finance</div>
            <a href="{{ route('dashboard.fees.index') }}" class="{{ request()->routeIs('dashboard.fees.*') ? 'active' : '' }}">
                <i class="fas fa-money-bill-wave"></i> Fees
            </a>
            @endcan
            @can('view library')
                <div class="nav-section">Library</div>
                <a href="{{ route('dashboard.library.books.index') }}"
                    class="{{ request()->routeIs('dashboard.library.*') ? 'active' : '' }}">
                    <i class="fas fa-book"></i> Library
                </a>
            @endcan
            @can('view assignments')
                <div class="nav-section">Assignments</div>
                <a href="{{ route('dashboard.assignments.index') }}" class="{{ request()->routeIs('dashboard.assignments.*') ? 'active' : '' }}">
                    <i class="fas fa-tasks"></i> Assignments
                </a>
            @endcan
            @can('view notices')
                <div class="nav-section">Communication</div>
                <a href="{{ route('dashboard.notices.index') }}" class="{{ request()->routeIs('dashboard.notices.*') ? 'active' : '' }}">
                    <i class="fas fa-bullhorn"></i> Notices
                </a>
                <a href="{{ route('dashboard.events.index') }}" class="{{ request()->routeIs('dashboard.events.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
                <a href="{{ route('dashboard.messages.index') }}" class="{{ request()->routeIs('dashboard.messages.*') ? 'active' : '' }}">
                    <i class="fas fa-envelope"></i> Messages
                    @php $unread = \App\Models\Message::where('status','unread')->count(); @endphp
                    @if($unread > 0)
                        <span class="ms-auto badge" style="background:#dc2626;font-size:0.65rem">{{ $unread }}</span>
                    @endif
                </a>
            @endcan
            @can('view reports')
                <div class="nav-section">Reports</div>
                <a href="{{ route('dashboard.reports.students') }}" class="{{ request()->routeIs('dashboard.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            @endcan
            @role('super_admin|admin')
                <div class="nav-section">Lead Generation</div>
                <a href="{{ route('dashboard.lead.overview') }}" class="{{ request()->routeIs('dashboard.lead.overview') ? 'active' : '' }}">
                    <i class="fas fa-headset"></i> AI Voice Agent
                </a>
                <a href="{{ route('dashboard.lead.console') }}" class="{{ request()->routeIs('dashboard.lead.console*') ? 'active' : '' }}">
                    <i class="fas fa-microphone-lines"></i> Voice Console
                </a>
                <a href="{{ route('dashboard.lead.guardians.index') }}" class="{{ request()->routeIs('dashboard.lead.guardians.*') ? 'active' : '' }}">
                    <i class="fas fa-address-book"></i> Parent Contacts
                </a>
                <a href="{{ route('dashboard.lead.scripts.index') }}" class="{{ request()->routeIs('dashboard.lead.scripts.*') ? 'active' : '' }}">
                    <i class="fas fa-file-lines"></i> Call Scripts
                </a>
                <a href="{{ route('dashboard.lead.calls.index') }}" class="{{ request()->routeIs('dashboard.lead.calls.*') ? 'active' : '' }}">
                    <i class="fas fa-phone-volume"></i> Outbound Calls
                </a>
                <a href="{{ route('dashboard.lead.logs') }}" class="{{ request()->routeIs('dashboard.lead.logs*') ? 'active' : '' }}">
                    <i class="fas fa-clipboard-list"></i> Query Log &amp; Alerts
                </a>
                <a href="{{ route('dashboard.lead.settings') }}" class="{{ request()->routeIs('dashboard.lead.settings*') ? 'active' : '' }}">
                    <i class="fas fa-sliders"></i> Agent Settings
                </a>
            @endrole
            @role('super_admin|admin')
                <div class="nav-section">System</div>
                <a href="{{ route('dashboard.notifications.index') }}"
                    class="{{ request()->routeIs('dashboard.notifications.*') ? 'active' : '' }}">
                    <i class="fas fa-bell"></i> Notifications
                </a>
                <a href="{{ route('dashboard.users.index') }}"
                    class="{{ request()->routeIs('dashboard.users.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i> Users
                </a>
                <a href="{{ route('dashboard.settings.index') }}"
                    class="{{ request()->routeIs('dashboard.settings.*') ? 'active' : '' }}">
                    <i class="fas fa-cog"></i> Settings
                </a>
            @endrole
        </nav>
    </div>
    {{-- MAIN CONTENT --}}
    <div class="main-content">
        {{-- TOPBAR --}}
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard.index') }}">Dashboard</a>
                        </li>
                        @yield('breadcrumb')
                    </ol>
                </nav>
            </div>
            <div class="d-flex align-items-center gap-3">
                {{-- Notifications Bell --}}
                <div class="dropdown" id="notifDropdown">
                    <button class="btn btn-sm btn-light position-relative" 
                            data-bs-toggle="dropdown" 
                            id="notifBell"
                            onclick="markAllReadOnOpen()">
                        <i class="fas fa-bell"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            id="notifCount" style="font-size:9px;display:none">0</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="width:340px;border-radius:12px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,0.15)">
                        {{-- Header --}}
                        <div class="d-flex justify-content-between align-items-center px-3 py-2"
                            style="background:#1e293b">
                            <span style="color:#fff;font-weight:600;font-size:0.875rem">
                                <i class="fas fa-bell me-2"></i>Notifications
                            </span>
                            <div class="d-flex gap-2">
                                <button onclick="markAllRead()" class="btn btn-sm" 
                                        style="color:rgba(255,255,255,0.7);font-size:0.75rem;padding:2px 8px">
                                    Mark all read
                                </button>
                                <a href="{{ route('dashboard.notifications.index') }}" 
                                    class="btn btn-sm" 
                                    style="color:#60a5fa;font-size:0.75rem;padding:2px 8px">
                                    View all
                                </a>
                            </div>
                        </div>
                        {{-- Notification List --}}
                        <div id="notifList" style="max-height:320px;overflow-y:auto">
                            <div class="text-center py-4 text-muted" id="notifEmpty" style="display:none">
                                <i class="fas fa-bell-slash fa-2x mb-2 d-block" style="color:#cbd5e1"></i>
                                <small>No notifications</small>
                            </div>
                        </div>
                        {{-- Footer --}}
                        <div class="text-center py-2" style="border-top:1px solid #e2e8f0;background:#f8fafc">
                            <a href="{{ route('dashboard.notifications.index') }}" 
                                style="font-size:0.8rem;color:#2563eb;text-decoration:none">
                                View All Notifications
                            </a>
                        </div>
                    </div>
                </div>
                {{-- Profile --}}
                <div class="dropdown">
                    <button class="btn btn-sm d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                        <img src="{{ auth()->user()->photo_url }}"
                            class="rounded-circle"
                            style="width:32px;height:32px;object-fit:cover;"
                            alt="{{ auth()->user()->name }}"
                            onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=2563eb&color=fff&size=100&bold=true'">
                        <span class="d-none d-md-block" style="font-size:0.875rem;font-weight:500;">
                            {{ auth()->user()->name }}
                        </span>
                        <i class="fas fa-chevron-down" style="font-size:0.7rem;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard.profile') }}">
                                <i class="fas fa-user me-2"></i>Profile
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('dashboard.password.change') }}">
                                <i class="fas fa-key me-2"></i>Change Password
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        {{-- PAGE CONTENT --}}
        <div class="content-area">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </div>
    </div>
    {{-- Scripts --}}
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script>
        // Sidebar toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('open');
        });
        // DataTables init
        $('.datatable').DataTable({
            pageLength: 15,
            responsive: true,
            language: { search: 'Search:', lengthMenu: 'Show _MENU_ entries' }
        });
        // SweetAlert delete confirm
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const form = document.getElementById(this.dataset.form);
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });

        // CSRF for AJAX
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        // ===== Notification System =====
        let notifLoaded = false;
        // Load notifications on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadNotifCount();
            // Refresh count every 30 seconds
            setInterval(loadNotifCount, 30000);
        });
        // Get unread count
        function loadNotifCount() {
            fetch('{{ route("dashboard.notifications.count") }}')
                .then(r => r.json())
                .then(data => {
                    const badge = document.getElementById('notifCount');
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }
                })
                .catch(() => {});
        }
        // Load notification list when dropdown opens
        document.getElementById('notifBell').addEventListener('click', function() {
            loadNotifList();
        });
        function loadNotifList() {
            fetch('{{ route("dashboard.notifications.latest") }}')
                .then(r => r.json())
                .then(data => {
                    const list  = document.getElementById('notifList');
                    const empty = document.getElementById('notifEmpty');

                    if (data.length === 0) {
                        list.innerHTML = '';
                        empty.style.display = 'block';
                        return;
                    }
                    empty.style.display = 'none';
                    list.innerHTML = data.map(n => `
                        <div class="d-flex gap-3 px-3 py-2 notif-item" 
                            style="border-bottom:1px solid #f1f5f9;cursor:pointer;background:${n.is_read ? '#fff' : '#f0f9ff'}"
                            onclick="openNotif(${n.id}, '${n.url ?? '#'}')">
                            <div style="width:36px;height:36px;border-radius:10px;background:${n.color}20;
                                        display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px">
                                <i class="${n.icon}" style="color:${n.color};font-size:0.875rem"></i>
                            </div>
                            <div style="flex:1;min-width:0">
                                <div style="font-size:0.8rem;font-weight:${n.is_read ? '400' : '600'};color:#1e293b;
                                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    ${n.title}
                                </div>
                                <div style="font-size:0.75rem;color:#64748b;
                                            white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                                    ${n.message}
                                </div>
                                <div style="font-size:0.7rem;color:#94a3b8;margin-top:2px">
                                    ${timeAgo(n.created_at)}
                                </div>
                            </div>
                            ${!n.is_read ? '<div style="width:8px;height:8px;background:#2563eb;border-radius:50%;flex-shrink:0;margin-top:6px"></div>' : ''}
                        </div>
                    `).join('');
                })
                .catch(() => {});
        }
        // Mark single as read and navigate
        function openNotif(id, url) {
            fetch(`/dashboard/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                loadNotifCount();
                if (url && url !== '#') {
                    window.location.href = url;
                }
            });
        }
        // Mark all as read
        function markAllRead() {
            fetch('{{ route("dashboard.notifications.read-all") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            }).then(() => {
                loadNotifCount();
                loadNotifList();
            });
        }
        // Mark all read when bell is opened
        function markAllReadOnOpen() {
            setTimeout(() => {
                loadNotifCount();
            }, 1000);
        }
        // Time ago helper
        function timeAgo(dateStr) {
            const date = new Date(dateStr);
            const now  = new Date();
            const diff = Math.floor((now - date) / 1000);
            if (diff < 60)     return 'Just now';
            if (diff < 3600)   return Math.floor(diff/60) + ' min ago';
            if (diff < 86400)  return Math.floor(diff/3600) + ' hours ago';
            return Math.floor(diff/86400) + ' days ago';
        }
    </script>
    @stack('scripts')
</body>
</html>