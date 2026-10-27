<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;

class SubjectController extends Controller {
    public function index() {
        $subjects = Subject::all();
        return view('subjects.index', compact('subjects'));
    }

    public function store(Request $request) {
        $request->validate(['name' => 'required', 'code' => 'required|unique:subjects,code']);
        Subject::create($request->all());
        return redirect()->route('dashboard.subjects.index')->with('success', 'Subject added!');
    }

    public function destroy(Subject $subject) {
        $subject->delete();
        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('dashboard.subjects.index')->with('success', 'Subject deleted!');
    }

    public function create() { return redirect()->route('dashboard.subjects.index'); }
    public function show($id){ return redirect()->route('dashboard.subjects.index'); }
    public function edit(Subject $subject) {
        $subjects = Subject::all();
        return view('subjects.edit', compact('subject','subjects'));
    }

    public function update(Request $request, Subject $subject) {
        $subject->update($request->all());
        return redirect()->route('dashboard.subjects.index')->with('success', 'Subject updated!');
    }
}