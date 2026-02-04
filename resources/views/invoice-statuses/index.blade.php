@extends('layouts.master')

@section('title', 'حالات الفواتير')
@section('page_title', 'حالات الفواتير')
@section('page_subtitle', 'إدارة حالات الفواتير المخصصة')

@section('page_actions')
    <button type="button" class="btn btn-primary rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createStatusModal">
        <i class="bi bi-plus-circle"></i>
        <span>إضافة حالة جديدة</span>
    </button>
@endsection

@section('content')
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3 border-0">#</th>
                    <th class="px-4 py-3 border-0">الاسم بالعربية</th>
                    {{--                        <th class="px-4 py-3 border-0">الاسم بالإنجليزية</th>--}}
                    <th class="px-4 py-3 border-0 text-center">اللون</th>
                    {{--                        <th class="px-4 py-3 border-0 text-center">الأيقونة</th>--}}
                    {{--                        <th class="px-4 py-3 border-0 text-center">الترتيب</th>--}}
                    <th class="px-4 py-3 border-0 text-center">الحالة</th>
                    <th class="px-4 py-3 border-0 text-center">الإجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($statuses as $status)
                    <tr>
                        <td class="px-4 py-3">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 fw-bold">{{ $status->name }}</td>
                        {{-- Remove this cell since column is commented --}}
                        {{-- <td class="px-4 py-3 text-muted">{{ $status->name_en ?? '—' }}</td> --}}
                        <td class="px-4 py-3 text-center">
                                <span class="badge rounded-pill px-3 py-2" style="background-color: {{ $status->color }}; color: white;">
                                    {{ $status->color }}
                                </span>
                        </td>
                        {{-- Remove this cell since column is commented --}}
                        {{-- <td class="px-4 py-3 text-center">
                            @if($status->icon)
                                <i class="bi bi-{{ $status->icon }} fs-5"></i>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td> --}}
                        {{-- Remove this cell since column is commented --}}
                        {{-- <td class="px-4 py-3 text-center">
                            <span class="badge bg-light text-dark rounded-pill px-3">{{ $status->sort_order }}</span>
                        </td> --}}
                        <td class="px-4 py-3 text-center">
                            @if($status->is_active)
                                <span class="badge bg-success rounded-pill">نشط</span>
                            @else
                                <span class="badge bg-secondary rounded-pill">غير نشط</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="d-flex justify-content-center gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-xl"
                                        onclick="editStatus({{ $status->id }}, '{{ $status->name }}', '{{ $status->name_en ?? '' }}', '{{ $status->color }}', '{{ $status->icon ?? '' }}', {{ $status->is_active ? 'true' : 'false' }}, {{ $status->sort_order ?? 0 }})"
                                        title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form action="{{ route('invoice-statuses.destroy', $status) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الحالة؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-xl" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        {{-- Update colspan from 8 to 5 since you removed 3 columns --}}
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            لا توجد حالات فواتير مضافة
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Create Status Modal -->
    <div class="modal fade" id="createStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle text-primary me-2"></i>
                        إضافة حالة فاتورة جديدة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('invoice-statuses.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الاسم بالعربية <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        {{--                        <div class="mb-3">--}}
                        {{--                            <label class="form-label">الاسم بالإنجليزية</label>--}}
                        {{--                            <input type="text" name="name_en" class="form-control">--}}
                        {{--                        </div>--}}
                        <div class="mb-3">
                            <label class="form-label">اللون <span class="text-danger">*</span></label>
                            <input type="color" name="color" class="form-control form-control-color" value="#6c757d" required>
                        </div>
                        {{--                        <div class="mb-3">--}}
                        {{--                            <label class="form-label">الأيقونة (Bootstrap Icons)</label>--}}
                        {{--                            <input type="text" name="icon" class="form-control" placeholder="مثال: check-circle-fill">--}}
                        {{--                            <small class="text-muted">اختر من <a href="https://icons.getbootstrap.com/" target="_blank">Bootstrap Icons</a></small>--}}
                        {{--                        </div>--}}
                        {{--                        <div class="mb-3">--}}
                        {{--                            <label class="form-label">الترتيب</label>--}}
                        {{--                            <input type="number" name="sort_order" class="form-control" value="0" min="0">--}}
                        {{--                        </div>--}}
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active_create" checked>
                            <label class="form-check-label" for="is_active_create">نشط</label>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary rounded-xl" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-xl">حفظ</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Status Modal -->
    <div class="modal fade" id="editStatusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil text-warning me-2"></i>
                        تعديل حالة الفاتورة
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editStatusForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">الاسم بالعربية <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        {{-- Comment out or remove this field --}}
                        {{-- <div class="mb-3">
                            <label class="form-label">الاسم بالإنجليزية</label>
                            <input type="text" name="name_en" id="edit_name_en" class="form-control">
                        </div> --}}
                        <div class="mb-3">
                            <label class="form-label">اللون <span class="text-danger">*</span></label>
                            <input type="color" name="color" id="edit_color" class="form-control form-control-color" required>
                        </div>
                        {{-- Comment out or remove this field --}}
                        {{-- <div class="mb-3">
                            <label class="form-label">الأيقونة (Bootstrap Icons)</label>
                            <input type="text" name="icon" id="edit_icon" class="form-control" placeholder="مثال: check-circle-fill">
                        </div> --}}
                        {{-- Comment out or remove this field --}}
                        {{-- <div class="mb-3">
                            <label class="form-label">الترتيب</label>
                            <input type="number" name="sort_order" id="edit_sort_order" class="form-control" min="0">
                        </div> --}}
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="edit_is_active">
                            <label class="form-check-label" for="edit_is_active">نشط</label>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-secondary rounded-xl" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-xl">تحديث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function editStatus(id, name, nameEn, color, icon, isActive, sortOrder) {
            const form = document.getElementById('editStatusForm');
            form.action = `/invoice-statuses/${id}`;

            document.getElementById('edit_name').value = name;
            // Remove these lines since the fields are commented out
            // document.getElementById('edit_name_en').value = nameEn || '';
            document.getElementById('edit_color').value = color;
            // document.getElementById('edit_icon').value = icon || '';
            // document.getElementById('edit_sort_order').value = sortOrder;
            document.getElementById('edit_is_active').checked = isActive;

            const modal = new bootstrap.Modal(document.getElementById('editStatusModal'));
            modal.show();
        }
    </script>
@endpush
