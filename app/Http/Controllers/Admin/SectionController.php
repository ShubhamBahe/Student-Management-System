<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SectionController extends Controller {
    public function index() {
        $sections = Section::with('class')->get();
        $classes  = SchoolClass::all();
        return view('sections.index', compact('sections', 'classes'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required', 'class_id' => 'required|exists:classes,id']);
        Section::create($request->all());
        return redirect()->route('dashboard.sections.index')->with('success', 'Section added!');
    }

    public function destroy(Section $section) {
        $section->delete();
        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('dashboard.sections.index')->with('success', 'Section deleted!');
    }

    public function create() { return redirect()->route('dashboard.sections.index'); }
    public function show($id){ return redirect()->route('dashboard.sections.index'); }
    public function edit(Section $section) {
        $classes = SchoolClass::all();
        $sections = Section::with('class')->get();
        return view('sections.edit', compact('section','classes','sections'));
    }
    public function update(Request $request, Section $section) {
        $section->update($request->all());
        return redirect()->route('dashboard.sections.index')->with('success', 'Section updated!');
    }
}