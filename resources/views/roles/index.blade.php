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
                <div class="card-body">
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
                        <div class="permissions-list" style="max-height: 150px; overflow-y: auto;">
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

                    <div class="d-grid">
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
