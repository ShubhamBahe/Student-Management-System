@extends('layouts.dashboard')
@section('title', 'Issue Book')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.library.books.index') }}">Library</a></li>
    <li class="breadcrumb-item active">Issue Book</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Issue Book to Student/Staff</h6>
        <form method="POST" action="{{ route('dashboard.library.issue') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Select Book <span class="text-danger">*</span></label>
                    <select name="book_id" class="form-select" required>
                        <option value="">Select Available Book</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}">
                                {{ $book->title }} — {{ $book->author }} ({{ $book->available_copies }} available)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Select User <span class="text-danger">*</span></label>
                    <select name="user_id" class="form-select" required>
                        <option value="">Select Student/Staff</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Issue Date <span class="text-danger">*</span></label>
                    <input type="date" name="issue_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Due Date <span class="text-danger">*</span></label>
                    <input type="date" name="due_date" class="form-control"
                        value="{{ date('Y-m-d', strtotime('+14 days')) }}" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="fas fa-check me-2"></i>Issue Book
                    </button>
                    <a href="{{ route('dashboard.library.books.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection