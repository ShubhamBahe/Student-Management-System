<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller {
    public function index() {
        return view('profile.index');
    }

    public function update(Request $request) {
        $user = auth()->user();
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete('photos/' . $user->photo);
            }
            $photoName = time() . '.' . $request->photo->extension();
            $request->photo->storeAs('photos', $photoName, 'public');
            $user->update(['photo' => $photoName]);
        }
        $user->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'gender'        => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'blood_group'   => $request->blood_group,
            'address'       => $request->address,
        ]);
        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    public function changePassword() {
        return view('profile.change-password');
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ]);
        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return redirect()->back()->with('error', 'Current password is incorrect!');
        }
        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);
        return redirect()->back()->with('success', 'Password changed successfully!');
    }
}