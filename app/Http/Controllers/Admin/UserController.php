<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller {
    public function index() {
        $users = User::with('roles')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create() {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request) {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role'     => 'required',
        ]);
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'phone'    => $request->phone,
            'gender'   => $request->gender,
            'status'   => 'active',
        ]);
        $user->assignRole($request->role);
        return redirect()->route('dashboard.users.index')
            ->with('success', 'User created successfully! Password: ' . $request->password);
    }

    public function show(User $user) {
        return redirect()->route('dashboard.users.edit', $user);
    }

    public function edit(User $user) {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user) {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);
        $user->update([
            'name'   => $request->name,
            'email'  => $request->email,
            'phone'  => $request->phone,
            'status' => $request->status,
        ]);
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }
        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        }
        return redirect()->route('dashboard.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function destroy(User $user) {
        if (auth()->id() === $user->id) {
            if (request()->ajax()) {
                return response()->json(['success' => false, 'message' => 'Cannot delete your own account!'], 403);
            }
            return redirect()->back()->with('error', 'You cannot delete your own account!');
        }

        $user->roles()->detach();
        $user->delete();

        if (request()->ajax()) return response()->json(['success' => true]);
        return redirect()->route('dashboard.users.index')->with('success', 'User deleted successfully!');
    }
}