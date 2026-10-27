@extends('layouts.dashboard')
@section('title', 'Book Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard.library.books.index') }}">Library</a></li>
    <li class="breadcrumb-item active">Book Details</li>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-4">
            <div class="page-card text-center">
                <div class="rounded mx-auto mb-3 d-flex align-items-center justify-content-center fw-bold text-white"
                    style="width:100px;height:120px;background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:8px;font-size:2.5rem">
                    {{ strtoupper(substr($book->title, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1" style="color:#1e293b">{{ $book->title }}</h5>
                <p class="text-muted mb-1" style="font-size:0.875rem">by {{ $book->author }}</p>
                <span class="badge" style="background:#dbeafe;color:#1d4ed8">{{ $book->category->name ?? '-' }}</span>
                <hr>
                <div class="row g-2 mt-1">
                    <div class="col-6">
                        <div style="background:#f8fafc;border-radius:8px;padding:10px">
                            <div class="fw-bold text-primary">{{ $book->total_copies }}</div>
                            <small class="text-muted">Total</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:#f8fafc;border-radius:8px;padding:10px">
                            <div class="fw-bold text-success">{{ $book->available_copies }}</div>
                            <small class="text-muted">Available</small>
                        </div>
                    </div>
                </div>
                <div class="mt-3">
                    <a href="{{ route('dashboard.library.books.edit', $book) }}" class="btn btn-warning btn-sm text-white me-2">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('dashboard.library.books.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="page-card mb-3">
                <h6 class="fw-bold mb-3" style="color:#1e293b">Book Information</h6>
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">ISBN</small>
                        <strong>{{ $book->isbn ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Publisher</small>
                        <strong>{{ $book->publisher ?? 'N/A' }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Price</small>
                        <strong>PKR {{ number_format($book->price) }}</strong>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Rack Number</small>
                        <strong>{{ $book->rack_number ?? 'N/A' }}</strong>
                    </div>
                    @if($book->description)
                        <div class="col-12">
                            <small class="text-muted d-block">Description</small>
                            <p class="text-muted mb-0" style="font-size:0.875rem">{{ $book->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
            <div class="page-card">
                <h6 class="fw-bold mb-3" style="color:#1e293b">Issue History</h6>
                @if($book->issues->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Issue Date</th>
                                    <th>Due Date</th>
                                    <th>Return Date</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($book->issues as $issue)
                                    <tr>
                                        <td>
                                            <small>{{ $issue->user->name ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($issue->issue_date)->format('d M Y') }}</small>
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($issue->due_date)->format('d M Y') }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $issue->return_date ? \Carbon\Carbon::parse($issue->return_date)->format('d M Y') : '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="badge {{ $issue->status === 'returned' ? 'badge-active' : ($issue->status === 'overdue' ? 'badge-inactive' : 'badge-pending') }}">
                                                {{ ucfirst($issue->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($issue->status === 'issued')
                                                <form method="POST" action="{{ route('dashboard.library.return', $issue) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success">Return</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3">No issue records found</p>
                @endif
            </div>
        </div>
    </div>
@endsection