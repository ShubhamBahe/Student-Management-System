# School Management System

A complete School Management System built with **Laravel 12**, **PHP 8.2**, **MySQL**, **Bootstrap 5**, **JavaScript**, and **Ajax**.

## Live Demo
- **URL:** http://localhost:8000
- **Admin Email:** admin@school.com
- **Password:** password123

## Features

### Modules
- Dashboard with live Charts (Chart.js)
- Students Management (CRUD + ID Card + Promotion)
- Teachers Management (CRUD + Profile)
- Classes, Sections, Subjects Management
- Attendance (Mark + Monthly Report + Ajax)
- Examinations & Marks Entry (Report Card PDF)
- Fee Management (Collection + Receipt + Print)
- Library Management (Books + Issue + Return)
- Assignments Management
- Reports (Students, Fees, Attendance, Exams + CSV Export)
- Notice Board & Events
- Contact Messages (Email + MySQL)
- Live Notifications System
- Users Management
- Profile & Change Password
- System Settings
- Public Website (10+ pages)

### Technical Features
- **Laravel 12** + **PHP 8.2**
- **Role Based Access Control** (7 Roles)
- **Spatie Laravel Permission**
- **Ajax** — Live search, Dynamic dropdowns, Delete without reload
- **Bootstrap 5** — Responsive design
- **Chart.js** — Dashboard charts
- **dompdf** — PDF generation
- **phpoffice/phpspreadsheet** — Excel/CSV export
- **SweetAlert2** — Beautiful alerts
- **UI Avatars** — Auto-generated profile photos

### 7 User Roles
| Role | Access |
|------|--------|
| Super Admin | Full system access |
| Admin | Manage all school operations |
| Teacher | Classes, attendance, marks, assignments |
| Student | View marks, attendance, assignments |
| Parent | View child's records |
| Accountant | Fee management |
| Librarian | Library management |

### Database
- **43 Tables** covering all school operations
- Properly indexed and optimized

## Installation

### Requirements
- PHP 8.2+
- MySQL 5.7+
- Composer
- Node.js (optional)

### Steps

**1. Clone the repository**
```bash
git clone https://github.com/fayazahmedsaand123/school-management-system.git
cd school-management-system
```

**2. Install dependencies**
```bash
composer install
```

**3. Environment setup**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Configure database in `.env`**
```env
DB_DATABASE=school_db
DB_USERNAME=root
DB_PASSWORD=
```

**5. Run migrations and seeders**
```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=AcademicYearSeeder
php artisan db:seed --class=SettingsSeeder
php artisan db:seed --class=DemoDataSeeder
```

**6. Storage link**
```bash
php artisan storage:link
```

**7. Run the application**
```bash
php artisan serve
```

**8. Open browser**
<img width="1908" height="907" alt="Screenshot 2026-08-07 100551" src="https://github.com/user-attachments/assets/5c13c92d-7f7d-40b3-9b63-4e58962cb5a7" />
<img width="1904" height="916" alt="Screenshot 2026-08-07 100604" src="https://github.com/user-attachments/assets/de9757df-bfad-4adc-89ce-2c702c4b1a60" />

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Super Admin | superadmin@school.com | password123 |
| Admin | admin@school.com | password123 |

## Screenshots

### Dashboard
<img width="1903" height="913" alt="Screenshot 2026-08-07 100725" src="https://github.com/user-attachments/assets/28a9484f-84dc-40dd-9b42-5fdf00cd01a9" />

### Students
<img width="1906" height="913" alt="Screenshot 2026-08-13 093640" src="https://github.com/user-attachments/assets/6c113195-891c-46ef-a1fb-c3292a22e0f6" />

### Public Website
<img width="1908" height="907" alt="Screenshot 2026-08-07 100551" src="https://github.com/user-attachments/assets/4a12c19c-b555-44d4-89c6-358e6d486e64" />

## Tech Stack

| Technology | Version |
|-----------|---------|
| Laravel | 12.x |
| PHP | 8.2 |
| MySQL | 8.0 |
| Bootstrap | 5.3 |
| Chart.js | 4.x |
| jQuery | 3.7 |
| SweetAlert2 | 11.x |

## Developer

**Fayaz Ahmed Saand**
- GitHub: [@fayazahmedsaand123](https://github.com/fayazahmedsaand123)
- Freelancer: [freelancer.com/u/fayazahmeds9](https://www.freelancer.com/u/fayazahmeds9)
- Upwork: [upwork.com/freelancers/~014a51348826db402e](https://www.upwork.com/freelancers/~014a51348826db402e)

## License
This project is open-source and available under the [MIT License](LICENSE).
