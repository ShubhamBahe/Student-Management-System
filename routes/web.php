<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ClassController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\MarkController;
use App\Http\Controllers\Admin\FeeController;
use App\Http\Controllers\Admin\LibraryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\LeadGeneration as Lead;
use App\Http\Controllers\Public\LeadWebhookController;



// ==================== PUBLIC ROUTES ====================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/teachers', [HomeController::class, 'teachers'])->name('public.teachers');
Route::get('/admission', [HomeController::class, 'admission'])->name('admission');
Route::post('/admission/apply', [HomeController::class, 'applyAdmission'])->name('admission.apply');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'sendContact'])->name('contact.send');
Route::get('/gallery', [HomeController::class, 'gallery'])->name('gallery');
Route::get('/news', [HomeController::class, 'news'])->name('news');
Route::get('/news/{slug}', [HomeController::class, 'newsDetail'])->name('news.detail');
Route::get('/events', [HomeController::class, 'events'])->name('events');
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/fee-structure', [HomeController::class, 'feeStructure'])->name('fee.structure');
Route::get('/privacy-policy', [HomeController::class, 'privacy'])->name('privacy');
Route::get('/terms', [HomeController::class, 'terms'])->name('terms');

// ==================== AUTH ROUTES ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
    Route::post('/register', [LoginController::class, 'register']);
    Route::get('/forgot-password', [LoginController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [LoginController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [LoginController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [LoginController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ==================== ADMIN & STAFF ROUTES ====================
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('index');

    // Users
    Route::resource('users', UserController::class)->middleware('role:super_admin|admin');

    // Students
    Route::resource('students', StudentController::class)->middleware('permission:view students');
    Route::get('students/{student}/id-card', [StudentController::class, 'idCard'])->name('students.id-card');
    Route::get('students/{student}/promote', [StudentController::class, 'promoteForm'])->name('students.promote.form');
    Route::post('students/{student}/promote', [StudentController::class, 'promote'])->name('students.promote');

    // Teachers
    Route::resource('teachers', TeacherController::class)->middleware('permission:view teachers');
    Route::get('teachers/{teacher}/salary', [TeacherController::class, 'salary'])->name('teachers.salary');

    // Classes
    Route::resource('classes', ClassController::class)->middleware('permission:view classes');
    Route::resource('sections', SectionController::class)->middleware('permission:view classes');
    Route::resource('subjects', SubjectController::class)->middleware('permission:view classes');

    // Attendance
    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index')->middleware('permission:view attendance');
    Route::get('attendance/mark', [AttendanceController::class, 'markForm'])->name('attendance.mark')->middleware('permission:mark attendance');
    Route::post('attendance/mark', [AttendanceController::class, 'store'])->name('attendance.store')->middleware('permission:mark attendance');
    Route::get('attendance/report', [AttendanceController::class, 'report'])->name('attendance.report');
    Route::get('attendance/ajax/students', [AttendanceController::class, 'getStudentsByClass'])->name('attendance.ajax.students');

    // Exams
    Route::resource('exams', ExamController::class)->middleware('permission:view exams');
    Route::get('marks/entry', [MarkController::class, 'entryForm'])->name('marks.entry');
    Route::post('marks/entry', [MarkController::class, 'store'])->name('marks.store');
    Route::get('marks/report-card/{student}/{exam}', [MarkController::class, 'reportCard'])->name('marks.report-card');

    // Fees
    Route::resource('fees', FeeController::class)->middleware('permission:view fees');
    Route::get('fees/{fee}/receipt', [FeeController::class, 'receipt'])->name('fees.receipt');
    Route::post('fees/{fee}/collect', [FeeController::class, 'collect'])->name('fees.collect')->middleware('permission:collect fees');

    // Library
    Route::prefix('library')->name('library.')->group(function () {
        Route::resource('books', LibraryController::class)->names([
            'index'   => 'books.index',
            'create'  => 'books.create',
            'store'   => 'books.store',
            'show'    => 'books.show',
            'edit'    => 'books.edit',
            'update'  => 'books.update',
            'destroy' => 'books.destroy',
        ]);
        Route::get('issue', [LibraryController::class, 'issueForm'])->name('issue.form');
        Route::post('issue', [LibraryController::class, 'issueBook'])->name('issue');
        Route::post('return/{issue}', [LibraryController::class, 'returnBook'])->name('return');
    });

    // Assignments
    Route::resource('assignments', AssignmentController::class)->middleware('permission:view assignments');
    Route::post('assignments/{assignment}/submit', [AssignmentController::class, 'submit'])->name('assignments.submit');

    // Notices
    Route::resource('notices', NoticeController::class)->middleware('permission:view notices');

    // Events
    Route::resource('events', EventController::class);

    // Messages
    Route::resource('messages', MessageController::class)->only(['index','show','destroy']);

    // Reports
    Route::get('reports/students', [ReportController::class, 'students'])->name('reports.students')->middleware('permission:view reports');
    Route::get('reports/attendance', [ReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('reports/fees', [ReportController::class, 'fees'])->name('reports.fees');
    Route::get('reports/exams', [ReportController::class, 'exams'])->name('reports.exams');
    Route::get('reports/export/{type}/{format}', [ReportController::class, 'export'])->name('reports.export');

    // Profile
    Route::get('profile', [ProfileController::class, 'index'])->name('profile');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/change-password', [ProfileController::class, 'changePassword'])->name('password.change');
    Route::put('profile/change-password', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Notifications
    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('notifications', [NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
    Route::get('notifications/count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
    Route::get('notifications/latest', [NotificationController::class, 'latest'])->name('notifications.latest');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index')->middleware('role:super_admin|admin');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update')->middleware('role:super_admin|admin');

    // ==================== LEAD GENERATION (AI Voice Agent) ====================
    Route::middleware('role:super_admin|admin')->prefix('lead-generation')->name('lead.')->group(function () {
        Route::get('/', [Lead\OverviewController::class, 'index'])->name('overview');

        Route::get('console', [Lead\ConsoleController::class, 'index'])->name('console');
        Route::post('console/send', [Lead\ConsoleController::class, 'send'])->name('console.send');
        Route::get('console/preview', [Lead\ConsoleController::class, 'preview'])->name('console.preview');

        Route::post('guardians/import', [Lead\GuardianController::class, 'import'])->name('guardians.import');
        Route::resource('guardians', Lead\GuardianController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::post('scripts/samples', [Lead\ScriptController::class, 'samples'])->name('scripts.samples');
        Route::resource('scripts', Lead\ScriptController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('calls', [Lead\CallController::class, 'index'])->name('calls.index');
        Route::post('calls', [Lead\CallController::class, 'store'])->name('calls.store');

        Route::get('logs', [Lead\LogController::class, 'index'])->name('logs');
        Route::get('logs/export', [Lead\LogController::class, 'export'])->name('logs.export');
        Route::post('alerts/{alert}/toggle', [Lead\LogController::class, 'handleAlert'])->name('alerts.toggle');
        Route::delete('blocked/{attempt}', [Lead\LogController::class, 'unblock'])->name('blocked.destroy');

        Route::get('settings', [Lead\SettingController::class, 'index'])->name('settings');
        Route::post('settings', [Lead\SettingController::class, 'update'])->name('settings.update');
    });

    // Ajax Routes
    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('sections/{classId}', [\App\Http\Controllers\Admin\AjaxController::class, 'getSections'])->name('sections');
        Route::get('students-attendance', [\App\Http\Controllers\Admin\AjaxController::class, 'getStudentsForAttendance'])->name('students.attendance');
        Route::get('students-marks', [\App\Http\Controllers\Admin\AjaxController::class, 'getStudentsForMarks'])->name('students.marks');
        Route::get('students-search', [\App\Http\Controllers\Admin\AjaxController::class, 'searchStudents'])->name('students.search');
    });
});

// ==================== LEAD GENERATION WEBHOOKS (Twilio) ====================
Route::prefix('webhooks/lead')->name('lead.webhook.')->middleware('twilio.signature')->group(function () {
    Route::post('whatsapp', [LeadWebhookController::class, 'whatsapp'])->name('whatsapp');
    Route::post('voice/incoming', [LeadWebhookController::class, 'voiceIncoming'])->name('voice-incoming');
    Route::post('voice/respond', [LeadWebhookController::class, 'voiceRespond'])->name('voice-respond');
    Route::post('voice/status/{call}', [LeadWebhookController::class, 'voiceStatus'])->name('voice-status');
    Route::post('voice/outbound/{call}', [LeadWebhookController::class, 'outbound'])->middleware('signed')->name('outbound');
    Route::post('voice/outbound/{call}/respond', [LeadWebhookController::class, 'outboundRespond'])->middleware('signed')->name('outbound-respond');
});
