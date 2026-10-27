<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller {
    public function index() {
        $settings = Setting::pluck('value', 'key');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request) {
        $request->validate([
            'school_name'  => 'required|string|max:255',
            'school_email' => 'required|email',
        ]);
        $skip = ['_token', '_method', 'logo'];
        foreach ($request->except($skip) as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        // Handle logo upload
        if ($request->hasFile('logo')) {
            $request->validate(['logo' => 'image|mimes:jpg,jpeg,png|max:2048']);
            // Delete old logo
            $oldLogo = Setting::where('key', 'school_logo')->first();
            if ($oldLogo && $oldLogo->value && $oldLogo->value !== 'logo.png') {
                Storage::disk('public')->delete('logos/' . $oldLogo->value);
            }
            $logoName = 'logo_' . time() . '.' . $request->logo->extension();
            $request->logo->storeAs('logos', $logoName, 'public');
            Setting::updateOrCreate(['key' => 'school_logo'], ['value' => $logoName]);
        }
        return redirect()->back()->with('success', 'Settings updated successfully!');
    }
}