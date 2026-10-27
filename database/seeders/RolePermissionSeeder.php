<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder {
    public function run(): void {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions = [
            // Students
            'view students', 'create students', 'edit students', 'delete students',
            // Teachers
            'view teachers', 'create teachers', 'edit teachers', 'delete teachers',
            // Classes
            'view classes', 'create classes', 'edit classes', 'delete classes',
            // Attendance
            'view attendance', 'mark attendance',
            // Exams
            'view exams', 'create exams', 'edit exams', 'delete exams',
            'enter marks', 'view marks',
            // Fees
            'view fees', 'collect fees', 'manage fees',
            // Library
            'view library', 'manage library', 'issue books',
            // Reports
            'view reports', 'export reports',
            // Settings
            'manage settings',
            // Users
            'manage users',
            // Assignments
            'create assignments', 'view assignments', 'submit assignments',
            // Notices
            'create notices', 'view notices',
            // Lead generation
            'manage leads',
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
        // Super Admin - all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());
        // Admin
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([
            'view students', 'create students', 'edit students', 'delete students',
            'view teachers', 'create teachers', 'edit teachers', 'delete teachers',
            'view classes', 'create classes', 'edit classes', 'delete classes',
            'view attendance', 'mark attendance',
            'view fees', 'collect fees', 'manage fees',
            'view reports', 'export reports',
            'manage settings', 'manage users',
            'view notices', 'create notices',
            'view exams', 'create exams', 'edit exams', 'delete exams',
            'enter marks', 'view marks',      
            'view library', 'manage library', 
            'view assignments', 'create assignments', 
            'manage leads',
        ]);
        // Teacher
        $teacher = Role::firstOrCreate(['name' => 'teacher']);
        $teacher->givePermissionTo([
            'view students', 'view attendance', 'mark attendance',
            'view exams', 'enter marks', 'view marks',
            'create assignments', 'view assignments',
            'view notices', 'create notices',
        ]);
        // Student
        $student = Role::firstOrCreate(['name' => 'student']);
        $student->givePermissionTo([
            'view attendance', 'view marks', 'view assignments',
            'submit assignments', 'view notices', 'view library',
        ]);
        // Parent
        $parent = Role::firstOrCreate(['name' => 'parent']);
        $parent->givePermissionTo([
            'view attendance', 'view marks', 'view fees', 'view notices',
        ]);
        // Accountant
        $accountant = Role::firstOrCreate(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'view fees', 'collect fees', 'manage fees',
            'view students', 'view reports', 'export reports',
        ]);
        // Librarian
        $librarian = Role::firstOrCreate(['name' => 'librarian']);
        $librarian->givePermissionTo([
            'view library', 'manage library', 'issue books',
            'view students', 'view notices',
        ]);
        // Create Super Admin User
        $superAdminUser = User::firstOrCreate(
            ['email' => 'superadmin@school.com'],
            [
                'name'     => 'Super Admin',
                'password' => bcrypt('password123'),
                'status'   => 'active',
            ]
        );
        $superAdminUser->assignRole('super_admin');
        // Create Admin User
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name'     => 'School Admin',
                'password' => bcrypt('password123'),
                'status'   => 'active',
            ]
        );
        $adminUser->assignRole('admin');
        echo "Roles, Permissions and Default Users Created!\n";
    }
}