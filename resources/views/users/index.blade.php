@extends('layouts.dashboard')
@section('title', 'Users')

@section('breadcrumb')
    <li class="breadcrumb-item active">Users</li>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1" style="color:#1e293b">System Users</h5>
            <p class="text-muted mb-0" style="font-size:0.875rem">Manage all system users and their roles</p>
        </div>
        <a href="{{ route('dashboard.users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add User
        </a>
    </div>
    <div class="page-card">
        <div class="mb-3">
            <span style="font-size:0.875rem;color:#64748b">
                Total: <strong id="userCount">{{ $users->total() }}</strong> users
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">   
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $user)
                        <tr id="row-u{{ $user->id }}">
                            <td>{{ $users->firstItem() + $i }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $user->photo_url }}"
                                        class="rounded-circle"
                                        style="width:36px;height:36px;object-fit:cover;"
                                        onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background={{ $user->gender === 'female' ? 'db2777' : '2563eb' }}&color=fff&size=100&bold=true'">
                                    <div>
                                        <div style="font-weight:500;font-size:0.875rem">{{ $user->name }}</div>
                                        <small class="text-muted">{{ $user->created_at->format('d M Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <small>{{ $user->email }}</small>
                            </td>
                            <td>
                                <small>{{ $user->phone ?? '-' }}</small>
                            </td>
                            <td>
                                @foreach($user->getRoleNames() as $role)
                                    <span class="badge" style="background:#dbeafe;color:#1d4ed8;font-size:0.75rem">
                                        {{ str_replace('_', ' ', ucfirst($role)) }}
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                <span class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('dashboard.users.edit', $user) }}"
                                        class="btn btn-sm btn-warning text-white">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if(auth()->id() !== $user->id)
                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                id="del-btn-u{{ $user->id }}"
                                                onclick="ajaxDelete('{{ route('dashboard.users.destroy', $user) }}', 'u{{ $user->id }}', '{{ $user->name }}', 'userCount')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                No users found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
@endsection