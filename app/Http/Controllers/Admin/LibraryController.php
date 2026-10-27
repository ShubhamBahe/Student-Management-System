<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\User;
use Illuminate\Http\Request;

class LibraryController extends Controller {
    public function index() {
        $books = Book::with('category')->latest()->paginate(15);
        return view('library.index', compact('books'));
    }

    public function create() {
        $categories = BookCategory::all();
        return view('library.create', compact('categories'));
    }

    public function store(Request $request) {
        $request->validate([
            'title'       => 'required|string|max:255',
            'author'      => 'required|string|max:255',
            'category_id' => 'required|exists:book_categories,id',
        ]);

        Book::create($request->all());
        return redirect()->route('dashboard.library.books.index')
            ->with('success', 'Book added successfully!');
    }

    public function show(Book $book) {
        $book->load('category', 'issues.user');
        return view('library.show', compact('book'));
    }

    public function edit(Book $book) {
        $categories = BookCategory::all();
        return view('library.edit', compact('book', 'categories'));
    }

    public function update(Request $request, Book $book) {
        $book->update($request->all());
        return redirect()->route('dashboard.library.books.index')
            ->with('success', 'Book updated successfully!');
    }

    public function destroy(Book $book) {
        $book->delete();
        if(request()->ajax()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('dashboard.library.books.index')
            ->with('success', 'Book deleted successfully!');
    }

    public function issueForm() {
        $books = Book::where('available_copies', '>', 0)->get();
        $users = User::all();
        return view('library.issue', compact('books', 'users'));
    }

    public function issueBook(Request $request) {
        $request->validate([
            'book_id'    => 'required|exists:books,id',
            'user_id'    => 'required|exists:users,id',
            'issue_date' => 'required|date',
            'due_date'   => 'required|date|after:issue_date',
        ]);
        BookIssue::create([
            'book_id'    => $request->book_id,
            'user_id'    => $request->user_id,
            'issue_date' => $request->issue_date,
            'due_date'   => $request->due_date,
            'status'     => 'issued',
        ]);
        // Decrease available copies
        $book = Book::find($request->book_id);
        $book->decrement('available_copies');
        return redirect()->route('dashboard.library.books.index')
            ->with('success', 'Book issued successfully!');
    }

    public function returnBook(BookIssue $issue) {
        $issue->update([
            'return_date' => now()->toDateString(),
            'status'      => 'returned',
        ]);
        // Increase available copies
        $issue->book->increment('available_copies');
        return redirect()->back()->with('success', 'Book returned successfully!');
    }
}