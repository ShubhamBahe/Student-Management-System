<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class ClassController extends Controller {
    public function index() {
        $classes       = SchoolClass::with('academicYear', 'sections', 'students')->get();
        $academicYears = AcademicYear::all();
        return view('classes.index', compact('classes', 'academicYears'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required|string|max:255']);
        SchoolClass::create([
            'name'             => $request->name,
            'numeric_name'     => $request->numeric_name,
            'academic_year_id' => $request->academic_year_id,
        ]);
        return redirect()->route('dashboard.classes.index')->with('success', 'Class added successfully!');
    }

    public function edit(SchoolClass $class) {
        $academicYears = AcademicYear::all();
        $classes       = SchoolClass::with('academicYear', 'sections', 'students')->get();
        return view('classes.edit', compact('class', 'academicYears', 'classes'));
    }

    public function update(Request $request, SchoolClass $class) {
        $request->validate(['name' => 'required|string|max:255']);
        $class->update($request->all());
        return redirect()->route('dashboard.classes.index')->with('success', 'Class updated successfully!');
    }

    public function destroy(SchoolClass $class) {
        $class->delete();
        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('dashboard.classes.index')->with('success', 'Class deleted successfully!');
    }   

    public function create() { return redirect()->route('dashboard.classes.index'); }
    public function show($id) { return redirect()->route('dashboard.classes.index'); }
}