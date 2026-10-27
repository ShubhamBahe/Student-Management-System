@extends('layouts.dashboard')
@section('title', 'Add Book')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.library.books.index') }}">Library</a></li>
    <li class="breadcrumb-item active">Add Book</li>
@endsection

@section('content')
    <div class="page-card">
        <h6 class="fw-bold mb-4" style="color:#1e293b">Add New Book</h6>
        <form method="POST" action="{{ route('dashboard.library.books.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Book Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Author <span class="text-danger">*</span></label>
                    <input type="text" name="author" class="form-control" value="{{ old('author') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Category <span class="text-danger">*</span></label>
                    <select name="category_id" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control" value="{{ old('isbn') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Publisher</label>
                    <input type="text" name="publisher" class="form-control" value="{{ old('publisher') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Total Copies</label>
                    <input type="number" name="total_copies" class="form-control" value="{{ old('total_copies', 1) }}" min="1">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Available Copies</label>
                    <input type="number" name="available_copies" class="form-control" value="{{ old('available_copies', 1) }}" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Price (PKR)</label>
                    <input type="number" name="price" class="form-control" value="{{ old('price', 0) }}" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rack Number</label>
                    <input type="text" name="rack_number" class="form-control" value="{{ old('rack_number') }}" placeholder="e.g. R-1">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-save me-2"></i>Add Book
                    </button>
                    <a href="{{ route('dashboard.library.books.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
@endsection