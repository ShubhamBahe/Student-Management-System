<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller {
    public function index(Request $request) {
        $query = Teacher::with('user');
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"))
                ->orWhere('employee_id', 'like', "%$search%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $teachers = $query->latest()->paginate(15);
        return view('teachers.index', compact('teachers'));
    }

    public function create() {
        return view('teachers.create');
    }

    public function store(Request $request) {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'phone'         => 'nullable|string|max:20',
            'gender'        => 'required|in:male,female,other',
            'joining_date'  => 'required|date',
            'qualification' => 'required|string|max:255',
            'salary'        => 'required|numeric|min:0',
            'photo'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        $photoName = null;
        if ($request->hasFile('photo')) {
            $photoName = time() . '.' . $request->photo->extension();
            $request->photo->storeAs('photos', $photoName, 'public');
        }
        $user = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make('password123'),
            'phone'         => $request->phone,
            'gender'        => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'address'       => $request->address,
            'photo'         => $photoName,
            'status'        => 'active',
        ]);
        $user->assignRole('teacher');
        $employeeId = 'EMP-' . date('Y') . '-' . str_pad(Teacher::count() + 1, 3, '0', STR_PAD_LEFT);
        Teacher::create([
            'user_id'         => $user->id,
            'employee_id'     => $employeeId,
            'joining_date'    => $request->joining_date,
            'qualification'   => $request->qualification,
            'specialization'  => $request->specialization,
            'salary'          => $request->salary,
            'status'          => 'active',
        ]);
        return redirect()->route('dashboard.teachers.index')
            ->with('success', 'Teacher added successfully! Default password: password123');
    }

    public function show(Teacher $teacher) {
        $teacher->load('user', 'classes');
        return view('teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher) {
        $teacher->load('user');
        return view('teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher) {
        $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email,' . $teacher->user_id,
            'qualification'=> 'required|string|max:255',
            'salary'       => 'required|numeric|min:0',
        ]);
        if ($request->hasFile('photo')) {
            if ($teacher->user->photo) {
                Storage::disk('public')->delete('photos/' . $teacher->user->photo);
            }
            $photoName = time() . '.' . $request->photo->extension();
            $request->photo->storeAs('photos', $photoName, 'public');
            $teacher->user->update(['photo' => $photoName]);
        }
        $teacher->user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'phone'   => $request->phone,
            'gender'  => $request->gender,
            'address' => $request->address,
        ]);
        $teacher->update([
            'joining_date'   => $request->joining_date,
            'qualification'  => $request->qualification,
            'specialization' => $request->specialization,
            'salary'         => $request->salary,
            'status'         => $request->status,
        ]);
        return redirect()->route('dashboard.teachers.index')->with('success', 'Teacher updated successfully!');
    }

    public function destroy(Teacher $teacher) {
        if ($teacher->user->photo) {
            Storage::disk('public')->delete('photos/' . $teacher->user->photo);
        }
        $teacher->user->delete();
        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('dashboard.teachers.index')
            ->with('success', 'Teacher deleted successfully!');
    }
}