@extends('layouts.master')

@section('title', 'إدارة الخدمات')
@section('page_title', 'الخدمات')
@section('page_subtitle', 'إدارة قائمة الخدمات وتفاصيلها')

@section('page_actions')
    <button class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2"
            data-bs-toggle="modal" data-bs-target="#createServiceModal">
        <i class="bi bi-plus-lg"></i>
        <span>خدمة جديدة</span>
    </button>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">اسم الخدمة</th>
                                <th class="px-4 py-3 border-0">الوصف</th>
                                <th class="px-4 py-3 border-0">نوع الخدمة</th>
                                <th class="px-4 py-3 border-0 text-center">عدد التفاصيل</th>
                                <th class="px-4 py-3 border-0 text-end">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $service)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-gear text-muted"></i>
                                            </div>
                                            <span class="fw-bold text-dark">{{ $service->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-muted">{{ Str::limit($service->description, 50) ?? '—' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="badge bg-light text-dark rounded-pill px-3">{{ $service->service_type }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-primary text-white rounded-pill px-3">{{ $service->service_details_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-light-soft btn-sm rounded-lg"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editServiceModal{{ $service->id }}"
                                                    title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light-soft btn-sm rounded-lg text-danger"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')"
                                                        title="حذف">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Service Modal -->
                                <div class="modal fade" id="editServiceModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow-lg rounded-2xl">
                                            <div class="modal-header border-0 px-4 pt-4 pb-0">
                                                <h5 class="modal-title fw-bold">تعديل بيانات الخدمة</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('services.update', $service) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">اسم الخدمة</label>
                                                            <input type="text" name="name" class="form-control rounded-xl py-2 px-3" value="{{ $service->name }}" required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">الوصف</label>
                                                            <textarea name="description" class="form-control rounded-xl py-2 px-3" rows="3">{{ $service->description }}</textarea>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">نوع الخدمة</label>
                                                            <input type="text" name="service_type" class="form-control rounded-xl py-2 px-3" value="{{ $service->service_type }}" required>
                                                        </div>
                                                        
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">تفاصيل الخدمة</label>
                                                            <div id="editDetailsContainer{{ $service->id }}">
                                                                @foreach($service->serviceDetails as $index => $detail)
                                                                    <div class="detail-item border rounded-xl p-3 mb-2">
                                                                        <div class="row g-2">
                                                                            <div class="col-md-6">
                                                                                <input type="text" name="details[{{ $index }}][name]" class="form-control rounded-xl" value="{{ $detail->name }}" placeholder="اسم التفصيل" required>
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <select name="details[{{ $index }}][has_work_days]" class="form-control rounded-xl has-work-days-select" required>
                                                                                    <option value="0" {{ !$detail->has_work_days ? 'selected' : '' }}>بدون أيام عمل</option>
                                                                                    <option value="1" {{ $detail->has_work_days ? 'selected' : '' }}>مع أيام عمل</option>
                                                                                </select>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <input type="number" name="details[{{ $index }}][work_days]" class="form-control rounded-xl work-days-input" value="{{ $detail->work_days }}" placeholder="الأيام" min="1" {{ $detail->has_work_days ? '' : 'disabled' }}>
                                                                            </div>
                                                                            <div class="col-md-1">
                                                                                <button type="button" class="btn btn-danger btn-sm w-100 remove-detail">
                                                                                    <i class="bi bi-trash"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-xl mt-2" onclick="addEditDetail{{ $service->id }}()">
                                                                <i class="bi bi-plus-lg"></i> إضافة تفصيل
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 p-4 pt-0">
                                                    <button type="button" class="btn btn-light rounded-xl px-4" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold">حفظ التغييرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <script>
                                let editDetailIndex{{ $service->id }} = {{ $service->serviceDetails->count() }};
                                function addEditDetail{{ $service->id }}() {
                                    const container = document.getElementById('editDetailsContainer{{ $service->id }}');
                                    const detailHtml = `
                                        <div class="detail-item border rounded-xl p-3 mb-2">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <input type="text" name="details[${editDetailIndex{{ $service->id }}}][name]" class="form-control rounded-xl" placeholder="اسم التفصيل" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <select name="details[${editDetailIndex{{ $service->id }}}][has_work_days]" class="form-control rounded-xl has-work-days-select" required>
                                                        <option value="0">بدون أيام عمل</option>
                                                        <option value="1">مع أيام عمل</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="details[${editDetailIndex{{ $service->id }}}][work_days]" class="form-control rounded-xl work-days-input" placeholder="الأيام" min="1" disabled>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-danger btn-sm w-100 remove-detail">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                    container.insertAdjacentHTML('beforeend', detailHtml);
                                    editDetailIndex{{ $service->id }}++;
                                }
                                </script>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        لا يوجد خدمات مضافة حالياً
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($services->hasPages())
                    <div class="px-4 py-3 border-top bg-light">
                        {{ $services->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Service Modal -->
    <div class="modal fade" id="createServiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
                <div class="modal-header bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-4">
                    <h5 class="modal-title fw-bold text-white flex items-center gap-2">
                        <i class="bi bi-gear-fill"></i>
                        إضافة خدمة جديدة
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('services.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-slate-50">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">اسم الخدمة <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" required placeholder="أدخل اسم الخدمة">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">الوصف</label>
                                <textarea name="description" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" rows="3" placeholder="وصف الخدمة"></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">نوع الخدمة <span class="text-danger">*</span></label>
                                <input type="text" name="service_type" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" required placeholder="مثال: general, human_resource">
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">تفاصيل الخدمة</label>
                                <div id="detailsContainer"></div>
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-xl mt-2" onclick="addDetail()">
                                    <i class="bi bi-plus-lg"></i> إضافة تفصيل
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-t border-slate-100 p-3 bg-white">
                        <button type="button" class="btn btn-light rounded-xl px-4 font-bold text-slate-600" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl px-6 py-2 fw-bold shadow-lg shadow-emerald-500/30 border-0 hover:scale-105 transition-transform">حفظ الخدمة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
let detailIndex = 0;

function addDetail() {
    const container = document.getElementById('detailsContainer');
    const detailHtml = `
        <div class="detail-item border rounded-xl p-3 mb-2">
            <div class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="details[${detailIndex}][name]" class="form-control rounded-xl" placeholder="اسم التفصيل" required>
                </div>
                <div class="col-md-3">
                    <select name="details[${detailIndex}][has_work_days]" class="form-control rounded-xl has-work-days-select" required>
                        <option value="0">بدون أيام عمل</option>
                        <option value="1">مع أيام عمل</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="number" name="details[${detailIndex}][work_days]" class="form-control rounded-xl work-days-input" placeholder="الأيام" min="1" disabled>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm w-100 remove-detail">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', detailHtml);
    detailIndex++;
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-detail') || e.target.closest('.remove-detail')) {
        const button = e.target.closest('.remove-detail');
        button.closest('.detail-item').remove();
    }
});

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('has-work-days-select')) {
        const workDaysInput = e.target.closest('.row').querySelector('.work-days-input');
        if (e.target.value === '1') {
            workDaysInput.disabled = false;
            workDaysInput.required = true;
        } else {
            workDaysInput.disabled = true;
            workDaysInput.required = false;
            workDaysInput.value = '';
        }
    }
});
</script>
@endpush
