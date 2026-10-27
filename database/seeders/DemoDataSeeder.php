<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Notice;
use App\Models\Event;
use App\Models\News;
use App\Models\Testimonial;
use App\Models\ParentModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder {
    public function run(): void {
        $year = AcademicYear::where('is_current', true)->first();
        // ==================== CLASSES ====================
        $classNames = ['Class 1','Class 2','Class 3','Class 4','Class 5','Class 6','Class 7','Class 8','Class 9','Class 10'];
        $classes = [];
        foreach ($classNames as $i => $name) {
            $classes[] = SchoolClass::firstOrCreate(
                ['name' => $name, 'academic_year_id' => $year->id],
                ['numeric_name' => $i + 1]
            );
        }
        // ==================== SECTIONS ====================
        $sections = [];
        foreach ($classes as $class) {
            foreach (['A', 'B'] as $sec) {
                $sections[] = Section::firstOrCreate(
                    ['class_id' => $class->id, 'name' => $sec],
                    ['capacity' => 40]
                );
            }
        }
        // ==================== SUBJECTS ====================
        $subjectData = [
            ['name' => 'Mathematics',       'code' => 'MATH', 'full_marks' => 100, 'pass_marks' => 33],
            ['name' => 'English',           'code' => 'ENG',  'full_marks' => 100, 'pass_marks' => 33],
            ['name' => 'Urdu',              'code' => 'URD',  'full_marks' => 100, 'pass_marks' => 33],
            ['name' => 'Science',           'code' => 'SCI',  'full_marks' => 100, 'pass_marks' => 33],
            ['name' => 'Social Studies',    'code' => 'SS',   'full_marks' => 100, 'pass_marks' => 33],
            ['name' => 'Islamiyat',         'code' => 'ISL',  'full_marks' => 50,  'pass_marks' => 17],
            ['name' => 'Computer Science',  'code' => 'CS',   'full_marks' => 100, 'pass_marks' => 33],
            ['name' => 'Physics',           'code' => 'PHY',  'full_marks' => 100, 'pass_marks' => 33],
            ['name' => 'Chemistry',         'code' => 'CHEM', 'full_marks' => 100, 'pass_marks' => 33],
            ['name' => 'Biology',           'code' => 'BIO',  'full_marks' => 100, 'pass_marks' => 33],
        ];
        foreach ($subjectData as $sub) {
            Subject::firstOrCreate(['code' => $sub['code']], $sub);
        }
        // ==================== TEACHERS ====================
        $teacherData = [
            ['name' => 'Muhammad Ali Khan',    'email' => 'ali.teacher@school.com',    'gender' => 'male',   'qual' => 'M.Sc Mathematics',   'spec' => 'Mathematics', 'salary' => 35000],
            ['name' => 'Fatima Zahra',         'email' => 'fatima.teacher@school.com', 'gender' => 'female', 'qual' => 'M.A English',        'spec' => 'English',     'salary' => 32000],
            ['name' => 'Ahmed Hassan',         'email' => 'ahmed.teacher@school.com',  'gender' => 'male',   'qual' => 'M.Sc Physics',       'spec' => 'Physics',     'salary' => 38000],
            ['name' => 'Sara Malik',           'email' => 'sara.teacher@school.com',   'gender' => 'female', 'qual' => 'M.Sc Chemistry',     'spec' => 'Chemistry',   'salary' => 36000],
            ['name' => 'Usman Tariq',          'email' => 'usman.teacher@school.com',  'gender' => 'male',   'qual' => 'M.Sc Computer Sci',  'spec' => 'CS',          'salary' => 40000],
            ['name' => 'Ayesha Siddiqui',      'email' => 'ayesha.teacher@school.com', 'gender' => 'female', 'qual' => 'M.A Urdu',           'spec' => 'Urdu',        'salary' => 30000],
            ['name' => 'Bilal Mahmood',        'email' => 'bilal.teacher@school.com',  'gender' => 'male',   'qual' => 'M.Sc Biology',       'spec' => 'Biology',     'salary' => 34000],
            ['name' => 'Zainab Hussain',       'email' => 'zainab.teacher@school.com', 'gender' => 'female', 'qual' => 'M.A Social Studies', 'spec' => 'Social Studies','salary' => 28000],
        ];
        foreach ($teacherData as $i => $td) {
            $user = User::firstOrCreate(['email' => $td['email']], [
                'name'     => $td['name'],
                'password' => Hash::make('password123'),
                'gender'   => $td['gender'],
                'phone'    => '0300' . rand(1000000, 9999999),
                'status'   => 'active',
            ]);
            if (!$user->hasRole('teacher')) $user->assignRole('teacher');
            Teacher::firstOrCreate(['user_id' => $user->id], [
                'employee_id'    => 'EMP-2025-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
                'joining_date'   => '2023-04-01',
                'qualification'  => $td['qual'],
                'specialization' => $td['spec'],
                'salary'         => $td['salary'],
                'status'         => 'active',
            ]);
        }
        // ==================== STUDENTS ====================
        $maleNames   = ['Muhammad Usman','Ali Hassan','Ahmed Raza','Bilal Khan','Omar Farooq','Hamza Sheikh','Zaid Malik','Yasir Ahmed','Faisal Nawaz','Tariq Mehmood','Rehan Siddiqui','Kamran Ali','Shahzaib Qureshi','Adeel Akhtar','Waqas Hussain','Saad Butt','Imran Riaz','Asif Javed','Rizwan Haider','Salman Iqbal'];
        $femaleNames = ['Fatima Noor','Ayesha Bibi','Zainab Khatoon','Sara Jabeen','Hina Malik','Sana Tariq','Maria Hassan','Nadia Akhtar','Rabia Zahid','Amna Sheikh','Iqra Hussain','Mehwish Ali','Sobia Qamar','Farah Zafar','Noor ul Ain','Aliza Khan','Bushra Mahmood','Kiran Aslam','Shazia Butt','Nasreen Ahmed'];
        $studentCount = 0;
        foreach (array_slice($classes, 0, 5) as $class) {
            $classSections = Section::where('class_id', $class->id)->get();
            foreach ($classSections as $section) {
                $count = rand(15, 20);
                for ($i = 0; $i < $count; $i++) {
                    $isMale = rand(0, 1);
                    $names  = $isMale ? $maleNames : $femaleNames;
                    $name   = $names[array_rand($names)];
                    $email  = strtolower(str_replace(' ', '.', $name)) . rand(100, 999) . '@student.school.com';
                    if (User::where('email', $email)->exists()) continue;
                    $user = User::create([
                        'name'          => $name,
                        'email'         => $email,
                        'password'      => Hash::make('password123'),
                        'gender'        => $isMale ? 'male' : 'female',
                        'date_of_birth' => Carbon::now()->subYears(rand(8, 16))->toDateString(),
                        'phone'         => '0300' . rand(1000000, 9999999),
                        'status'        => 'active',
                    ]);
                    $user->assignRole('student');
                    $studentCount++;
                    $admNo = 'ADM-2025-' . str_pad($studentCount, 4, '0', STR_PAD_LEFT);
                    $rollNo = $class->id . $section->id . str_pad($studentCount, 3, '0', STR_PAD_LEFT);
                    Student::create([
                        'user_id'          => $user->id,
                        'roll_number'      => $rollNo,
                        'admission_number' => $admNo,
                        'admission_date'   => '2025-04-01',
                        'class_id'         => $class->id,
                        'section_id'       => $section->id,
                        'academic_year_id' => $year->id,
                        'status'           => 'active',
                    ]);
                }
            }
        }
        // ==================== ATTENDANCE (Last 30 days) ====================
        $students = Student::all();
        for ($day = 30; $day >= 1; $day--) {
            $date = Carbon::now()->subDays($day)->toDateString();
            $dayOfWeek = Carbon::parse($date)->dayOfWeek;
            if ($dayOfWeek == 0 || $dayOfWeek == 6) continue; // skip weekends
            foreach ($students->take(50) as $student) {
                $rand = rand(1, 10);
                $status = $rand <= 8 ? 'present' : ($rand == 9 ? 'absent' : 'late');
                Attendance::firstOrCreate(
                    ['student_id' => $student->id, 'date' => $date],
                    [
                        'class_id'   => $student->class_id,
                        'section_id' => $student->section_id,
                        'status'     => $status,
                        'marked_by'  => 1,
                    ]
                );
            }
        }
        // ==================== EXAMS ====================
        $exams = [
            ['name' => 'First Term Exam',  'start_date' => '2025-08-01', 'end_date' => '2025-08-10', 'status' => 'completed'],
            ['name' => 'Mid Term Exam',    'start_date' => '2025-11-01', 'end_date' => '2025-11-10', 'status' => 'completed'],
            ['name' => 'Final Term Exam',  'start_date' => '2026-03-01', 'end_date' => '2026-03-12', 'status' => 'upcoming'],
            ['name' => 'Pre-Board Exam',   'start_date' => '2026-02-01', 'end_date' => '2026-02-10', 'status' => 'upcoming'],
        ];
        foreach ($exams as $exam) {
            Exam::firstOrCreate(['name' => $exam['name']], array_merge($exam, ['academic_year_id' => $year->id]));
        }
        // ==================== MARKS ====================
        $exam     = Exam::where('status', 'completed')->first();
        $subjects = Subject::take(5)->get();
        foreach ($students->take(30) as $student) {
            foreach ($subjects as $subject) {
                Mark::firstOrCreate(
                    ['student_id' => $student->id, 'exam_id' => $exam->id, 'subject_id' => $subject->id],
                    [
                        'marks_obtained' => rand(40, 98),
                        'full_marks'     => $subject->full_marks,
                        'grade'          => ['A+','A','B','C','D'][rand(0,4)],
                    ]
                );
            }
        }
        // ==================== FEE TYPES ====================
        $feeTypes = [
            ['name' => 'Tuition Fee',    'amount' => 3500,  'frequency' => 'monthly'],
            ['name' => 'Exam Fee',       'amount' => 1500,  'frequency' => 'yearly'],
            ['name' => 'Library Fee',    'amount' => 500,   'frequency' => 'yearly'],
            ['name' => 'Sports Fee',     'amount' => 300,   'frequency' => 'monthly'],
            ['name' => 'Transport Fee',  'amount' => 2000,  'frequency' => 'monthly'],
        ];
        foreach ($feeTypes as $ft) {
            FeeType::firstOrCreate(['name' => $ft['name']], $ft);
        }
        // ==================== FEES ====================
        $tuitionFee = FeeType::where('name', 'Tuition Fee')->first();
        $months = ['January','February','March','April','May','June'];
        foreach ($students->take(40) as $student) {
            foreach ($months as $month) {
                $isPaid = rand(0, 3) > 0;
                Fee::firstOrCreate(
                    ['student_id' => $student->id, 'fee_type_id' => $tuitionFee->id, 'month' => $month],
                    [
                        'amount'         => 3500,
                        'discount'       => 0,
                        'fine'           => 0,
                        'paid_amount'    => $isPaid ? 3500 : 0,
                        'status'         => $isPaid ? 'paid' : 'pending',
                        'due_date'       => '2026-' . str_pad(array_search($month, $months) + 1, 2, '0', STR_PAD_LEFT) . '-10',
                        'paid_date'      => $isPaid ? '2026-' . str_pad(array_search($month, $months) + 1, 2, '0', STR_PAD_LEFT) . '-08' : null,
                        'receipt_number' => 'RCP-' . rand(10000, 99999),
                    ]
                );
            }
        }
        // ==================== BOOK CATEGORIES ====================
        $bookCats = [
            ['name' => 'Science & Technology'],
            ['name' => 'Mathematics'],
            ['name' => 'Literature & Fiction'],
            ['name' => 'History & Geography'],
            ['name' => 'Religion & Ethics'],
            ['name' => 'Reference Books'],
        ];
        foreach ($bookCats as $cat) {
            BookCategory::firstOrCreate(['name' => $cat['name']]);
        }
        // ==================== BOOKS ====================
        $bookData = [
            ['title' => 'Mathematics for Class 9',     'author' => 'Punjab Textbook Board', 'isbn' => '978-0001'],
            ['title' => 'Physics Principles',          'author' => 'Halliday & Resnick',    'isbn' => '978-0002'],
            ['title' => 'English Grammar in Use',      'author' => 'Raymond Murphy',        'isbn' => '978-0003'],
            ['title' => 'General Science',             'author' => 'PTB Authors',           'isbn' => '978-0004'],
            ['title' => 'Pakistan Studies',            'author' => 'PTB Authors',           'isbn' => '978-0005'],
            ['title' => 'Urdu Adab',                   'author' => 'PTB Authors',           'isbn' => '978-0006'],
            ['title' => 'Computer Science Class 10',   'author' => 'PTB Authors',           'isbn' => '978-0007'],
            ['title' => 'Chemistry Made Easy',         'author' => 'Dr. Atkins',            'isbn' => '978-0008'],
            ['title' => 'Biology for Students',        'author' => 'Campbell',              'isbn' => '978-0009'],
            ['title' => 'Islamiyat Class 8',           'author' => 'PTB Authors',           'isbn' => '978-0010'],
        ];
        $cat = BookCategory::first();
        foreach ($bookData as $book) {
            Book::firstOrCreate(['isbn' => $book['isbn']], array_merge($book, [
                'category_id'      => $cat->id,
                'publisher'        => 'Punjab Textbook Board',
                'total_copies'     => rand(5, 20),
                'available_copies' => rand(3, 10),
                'price'            => rand(200, 800),
                'rack_number'      => 'R-' . rand(1, 10),
            ]));
        }
        // ==================== NOTICES ====================
        $notices = [
            ['title' => 'Annual Day Celebration',      'content' => 'Annual Day will be celebrated on 15th August 2026. All students and parents are invited.', 'audience' => 'all'],
            ['title' => 'Final Exam Schedule Released', 'content' => 'Final term exam schedule has been released. Please check the notice board.', 'audience' => 'students'],
            ['title' => 'Holiday on Eid ul Adha',      'content' => 'School will remain closed from June 16 to June 20 on account of Eid ul Adha.', 'audience' => 'all'],
            ['title' => 'Parent Teacher Meeting',      'content' => 'PTM will be held on Saturday 2nd August. All parents are requested to attend.', 'audience' => 'parents'],
            ['title' => 'Result Cards Distribution',   'content' => 'Mid term result cards will be distributed on Monday. Students must come with parents.', 'audience' => 'students'],
        ];
        foreach ($notices as $notice) {
            Notice::firstOrCreate(['title' => $notice['title']], array_merge($notice, [
                'published_by' => 1,
                'publish_date' => now()->toDateString(),
                'status'       => 'active',
            ]));
        }
        // ==================== EVENTS ====================
        $events = [
            ['title' => 'Annual Sports Day',        'start_date' => '2026-08-15', 'end_date' => '2026-08-15', 'location' => 'School Ground'],
            ['title' => 'Science Exhibition',       'start_date' => '2026-09-05', 'end_date' => '2026-09-06', 'location' => 'School Hall'],
            ['title' => 'Quran Recitation Contest', 'start_date' => '2026-09-20', 'end_date' => '2026-09-20', 'location' => 'Mosque'],
            ['title' => 'Parent Teacher Meeting',   'start_date' => '2026-08-02', 'end_date' => '2026-08-02', 'location' => 'Classrooms'],
            ['title' => 'Annual Prize Distribution','start_date' => '2026-10-10', 'end_date' => '2026-10-10', 'location' => 'School Auditorium'],
        ];
        foreach ($events as $event) {
            Event::firstOrCreate(['title' => $event['title']], array_merge($event, [
                'description' => 'Join us for ' . $event['title'] . ' at ' . $event['location'],
                'status'      => 'active',
            ]));
        }
        // ==================== NEWS ====================
        $newsData = [
            ['title' => 'School Wins Inter-District Science Competition', 'content' => 'Our school won first prize in the inter-district science competition held in Hyderabad.'],
            ['title' => 'New Computer Lab Inaugurated',                   'content' => 'A modern computer lab with 30 new computers has been inaugurated by the principal.'],
            ['title' => 'Students Excel in Board Exams',                  'content' => 'Our Class 10 students achieved outstanding results with 85% obtaining A grade.'],
        ];
        foreach ($newsData as $news) {
            News::firstOrCreate(['title' => $news['title']], array_merge($news, [
                'slug'         => \Illuminate\Support\Str::slug($news['title']),
                'author_id'    => 1,
                'status'       => 'published',
                'published_at' => now(),
            ]));
        }
        // ==================== TESTIMONIALS ====================
        $testimonials = [
            ['name' => 'Mr. Khalid Mahmood', 'role' => 'Parent of Class 8 Student',    'message' => 'Excellent school with dedicated teachers. My child has improved significantly.'],
            ['name' => 'Mrs. Nasreen Ahmed', 'role' => 'Parent of Class 5 Student',    'message' => 'The management system is very professional. We are very satisfied.'],
            ['name' => 'Mr. Tariq Hussain',  'role' => 'Parent of Class 10 Student',   'message' => 'Best school in Hyderabad. Our children are in safe hands.'],
            ['name' => 'Mrs. Saima Akhtar',  'role' => 'Parent of Class 3 Student',    'message' => 'Wonderful environment for learning. Teachers are very caring.'],
        ];
        foreach ($testimonials as $t) {
            Testimonial::firstOrCreate(['name' => $t['name']], array_merge($t, ['status' => 'active']));
        }
        echo "Demo Data Seeded Successfully!\n";
        echo "Students: " . Student::count() . "\n";
        echo "Teachers: " . Teacher::count() . "\n";
        echo "Books: " . Book::count() . "\n";
        echo "Fees: " . Fee::count() . "\n";
    }
}