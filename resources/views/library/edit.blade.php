@extends('layouts.dashboard')
@section('title', 'Edit Book')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.library.books.index') }}">Library</a></li>
    <li class="breadcrumb-item active">Edit Book</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Edit Book</h6>
        <form method="POST" action="{{ route('dashboard.library.books.update', $book) }}">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Book Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ $book->title }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Author <span class="text-danger">*</span></label>
                    <input type="text" name="author" class="form-control" value="{{ $book->author }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ $book->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Total Copies</label>
                    <input type="number" name="total_copies" class="form-control" value="{{ $book->total_copies }}" min="1">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Available Copies</label>
                    <input type="number" name="available_copies" class="form-control" value="{{ $book->available_copies }}" min="0">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Rack Number</label>
                    <input type="text" name="rack_number" class="form-control" value="{{ $book->rack_number }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Price (PKR)</label>
                    <input type="number" name="price" class="form-control" value="{{ $book->price }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Update Book
                    </button>
                    <a href="{{ route('dashboard.library.books.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection