@extends('layouts.master')

@section('title', 'إدارة الأدوار والصلاحيات')
@section('page_title', 'إدارة الأدوار والصلاحيات')
@section('page_subtitle', 'إدارة صلاحيات المستخدمين والأدوار')

@section('content')
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">الأدوار المتاحة</h4>
        <a href="{{ route('roles.permissions') }}" class="btn btn-outline-primary rounded-pill">
            <i class="bi bi-shield-check me-2"></i>
            عرض جميع الصلاحيات
        </a>
    </div>

    <div class="row g-4">
        @foreach($roles as $role)
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="bi bi-person-badge fs-4 text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $role->display_name }}</h5>
                            <small class="text-muted">{{ $role->name }}</small>
                        </div>
                    </div>

                    @if($role->description)
                    <p class="text-muted small mb-3">{{ $role->description }}</p>
                    @endif

                    {{-- Permissions --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-muted">الصلاحيات</span>
                            <span class="badge bg-primary rounded-pill">
                                {{ $role->isAdmin() ? 'جميع الصلاحيات' : $role->permissions->count() . ' صلاحية' }}
                            </span>
                        </div>

                        @if($role->isAdmin())
                        <div class="alert alert-info alert-sm mb-0 py-2 px-3">
                            <i class="bi bi-info-circle me-1"></i>
                            <small>المدير لديه جميع الصلاحيات تلقائياً</small>
                        </div>
                        @else
                        <div class="permissions-list" style="max-height: 120px; overflow-y: auto;">
                            @forelse($role->permissions as $permission)
                            <div class="d-flex align-items-center py-1">
                                <i class="bi bi-check-circle-fill text-success me-2" style="font-size: 0.8rem;"></i>
                                <small>{{ $permission->display_name_ar }}</small>
                            </div>
                            @empty
                            <small class="text-muted">لا توجد صلاحيات محددة</small>
                            @endforelse
                        </div>
                        @endif
                    </div>

                    {{-- Users assigned to this role --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small fw-semibold text-muted">المستخدمون</span>
                            <span class="badge bg-secondary rounded-pill">{{ $role->users->count() }}</span>
                        </div>
                        <div style="max-height: 120px; overflow-y: auto;">
                            @forelse($role->users as $roleUser)
                            <div class="d-flex align-items-center justify-content-between py-1">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-fill text-muted" style="font-size: 0.8rem;"></i>
                                    <small>{{ $roleUser->name }}</small>
                                </div>
                                <form action="{{ route('roles.remove-user', [$role, $roleUser]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-link btn-sm text-danger p-0" title="إزالة"
                                            onclick="return confirm('إزالة {{ $roleUser->name }} من هذا الدور؟')">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                </form>
                            </div>
                            @empty
                            <small class="text-muted">لا يوجد مستخدمون</small>
                            @endforelse
                        </div>

                        {{-- Assign user form --}}
                        <form action="{{ route('roles.assign-user', $role) }}" method="POST" class="mt-2 d-flex gap-2">
                            @csrf
                            <select name="user_id" class="form-select form-select-sm rounded-pill no-select2" required>
                                <option value="">اختر مستخدماً...</option>
                                @foreach($users as $u)
                                    @if(!$role->users->contains($u->id))
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </form>
                    </div>

                    <div class="d-grid mt-auto">
                        @if($role->isAdmin())
                        <button class="btn btn-secondary btn-sm rounded-pill" disabled>
                            <i class="bi bi-lock-fill me-1"></i>
                            لا يمكن التعديل
                        </button>
                        @else
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary btn-sm rounded-pill">
                            <i class="bi bi-pencil-square me-1"></i>
                            تعديل الصلاحيات
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
