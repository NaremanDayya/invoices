@extends('layouts.master')

@section('title', 'تعديل صلاحيات الدور')
@section('page_title', 'تعديل صلاحيات الدور')
@section('page_subtitle', 'إدارة صلاحيات ' . $role->display_name)

@section('content')
<div class="content-card">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">{{ $role->display_name }}</h4>
            <p class="text-muted mb-0">{{ $role->description }}</p>
        </div>
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary rounded-pill">
            <i class="bi bi-arrow-right me-2"></i>
            العودة للقائمة
        </a>
    </div>

    <form action="{{ route('roles.update-permissions', $role) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-12">
                <div class="card border-0 bg-light">
                    <div class="card-body">
                        <h5 class="card-title mb-3 fw-bold">
                            <i class="bi bi-shield-check text-primary me-2"></i>
                            الصلاحيات المتاحة
                        </h5>
                        <p class="text-muted small mb-4">اختر الصلاحيات التي تريد منحها لهذا الدور</p>

                        <div class="row g-3">
                            @foreach($permissions as $permission)
                                <div class="col-md-6">
                                    <div class="form-check form-switch p-3 bg-white rounded-3 border d-flex flex-row-reverse justify-content-end gap-3">
                                        <input
                                            class="form-check-input cursor-pointer"
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            id="permission_{{ $permission->id }}"
                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                            style="float: none; margin-right: 0;"
                                        >
                                        <label class="form-check-label fw-semibold cursor-pointer" for="permission_{{ $permission->id }}" style="margin-right: 0;">
                                            {{ $permission->display_name_ar }}
                                            @if($permission->description)
                                                <br>
                                                <small class="text-muted fw-normal">{{ $permission->description }}</small>
                                            @endif
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="{{ route('roles.index') }}" class="btn btn-light rounded-pill px-4">
                إلغاء
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-check-circle me-2"></i>
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        $('form').on('submit', function(e) {
            const checkedCount = $('input[name="permissions[]"]:checked').length;
            if (checkedCount === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'يجب اختيار صلاحية واحدة على الأقل',
                    confirmButtonText: 'حسناً'
                });
            }
        });
    });
</script>
@endpush
@endsection
