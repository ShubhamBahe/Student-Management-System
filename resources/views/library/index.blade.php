@extends('layouts.dashboard')
@section('title', 'Library')

@section('breadcrumb')
    <li class="breadcrumb-item active">Library</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">Library Management</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Manage books and issue records</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('dashboard.library.issue.form') }}" class="btn btn-success btn-sm">
                <i class="fas fa-hand-holding-open me-1"></i>Issue Book
            </a>
            @can('manage library')
                <a href="{{ route('dashboard.library.books.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Add Book
                </a>
            @endcan
        </div>
    </div>
    <div class="page-card">
        <div class="table-responsive">
            <table class="table table-hover datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Available</th>
                        <th>Rack No</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $i => $book)
                        <tr id="row-b{{ $book->id }}">
                            <td>{{ $books->firstItem() + $i }}</td>
                            <td>
                                <strong style="font-size:0.875rem">{{ $book->title }}</strong>
                            </td>
                            <td>
                                <small>{{ $book->author }}</small>
                            </td>
                            <td>
                                <small>{{ $book->category->name ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge" style="background:#dbeafe;color:#1d4ed8">{{ $book->total_copies }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $book->available_copies > 0 ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $book->available_copies }}
                                </span>
                            </td>
                            <td>
                                <small>{{ $book->rack_number ?? '-' }}</small>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('dashboard.library.books.show', $book) }}"
                                    class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i></a>
                                    @can('manage library')
                                        <a href="{{ route('dashboard.library.books.edit', $book) }}"
                                            class="btn btn-sm btn-warning text-white">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm btn-danger"
                                            id="del-btn-b{{ $book->id }}"
                                            onclick="ajaxDelete('{{ route('dashboard.library.books.destroy', $book) }}', 'b{{ $book->id }}', '{{ $book->title }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-book fa-2x mb-2 d-block"></i>No books found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $books->links() }}
    </div>
@endsection