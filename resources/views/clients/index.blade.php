@extends('layouts.master')

@section('title', 'إدارة العملاء')
@section('page_title', 'العملاء')
@section('page_subtitle', 'إدارة قائمة العملاء وبياناتهم')

@section('page_actions')
    <button class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2" 
            data-bs-toggle="modal" data-bs-target="#createClientModal">
        <i class="bi bi-plus-lg"></i>
        <span>عميل جديد</span>
    </button>
@endsection

@section('content')
    <div class="row g-4">
        <!-- Stats Row -->
        <div class="col-12">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="bg-white p-4 rounded-xl border border-gray-100 shadow-sm h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="bg-primary-soft p-3 rounded-lg">
                                <i class="bi bi-people text-primary-accent fs-4"></i>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $clients->total() }}</h3>
                        <p class="text-muted small mb-0">إجمالي العملاء</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Clients Table -->
        <div class="col-12">
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3 border-0">اسم العميل</th>
                                <th class="px-4 py-3 border-0">البريد الإلكتروني</th>
                                <th class="px-4 py-3 border-0">الهاتف</th>
                                <th class="px-4 py-3 border-0">العنوان</th>
                                <th class="px-4 py-3 border-0 text-center">الفواتير</th>
                                <th class="px-4 py-3 border-0 text-end">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clients as $client)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="bg-light rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person text-muted"></i>
                                            </div>
                                            <a href="{{ route('clients.show', $client) }}" class="fw-bold text-dark text-decoration-none hover-primary">{{ $client->name }}</a>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-muted">{{ $client->email ?? '—' }}</td>
                                    <td class="px-4 py-3" dir="ltr text-end">{{ $client->phone ?? '—' }}</td>
                                    <td class="px-4 py-3 text-muted small">{{ Str::limit($client->address, 50) ?? '—' }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-light text-dark rounded-pill px-3">{{ $client->invoices_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-light-soft btn-sm rounded-lg" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editClientModal{{ $client->id }}"
                                                    title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light-soft btn-sm rounded-lg text-danger" 
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا العميل؟')"
                                                        title="حذف">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Client Modal -->
                                <div class="modal fade" id="editClientModal{{ $client->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg rounded-2xl">
                                            <div class="modal-header border-0 px-4 pt-4 pb-0">
                                                <h5 class="modal-title fw-bold">تعديل بيانات العميل</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('clients.update', $client) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">اسم العميل</label>
                                                            <input type="text" name="name" class="form-control rounded-xl py-2 px-3" value="{{ $client->name }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-muted">يوم الدفع</label>
                                                            <input type="number" name="default_payment_day" class="form-control rounded-xl py-2 px-3" value="{{ $client->default_payment_day }}" min="1" max="31">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold text-muted">أيام السماح</label>
                                                            <input type="number" name="grace_period_days" class="form-control rounded-xl py-2 px-3" value="{{ $client->grace_period_days }}" min="0">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">البريد الإلكتروني</label>
                                                            <input type="email" name="email" class="form-control rounded-xl py-2 px-3" value="{{ $client->email }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">الهاتف</label>
                                                            <input type="text" name="phone" class="form-control rounded-xl py-2 px-3" value="{{ $client->phone }}">
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">العنوان</label>
                                                            <textarea name="address" class="form-control rounded-xl py-2 px-3" rows="3">{{ $client->address }}</textarea>
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
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        لا يوجد عملاء مضافين حالياً
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($clients->hasPages())
                    <div class="px-4 py-3 border-top bg-light">
                        {{ $clients->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Client Modal -->
    <div class="modal fade" id="createClientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-2xl">
                <div class="modal-header border-0 px-4 pt-4 pb-0">
                    <h5 class="modal-title fw-bold">إضافة عميل جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">اسم العميل</label>
                                <input type="text" name="name" class="form-control rounded-xl py-2 px-3" required placeholder="أدخل اسم العميل أو الشركة">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">يوم الدفع</label>
                                <input type="number" name="default_payment_day" class="form-control rounded-xl py-2 px-3" min="1" max="31" value="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">أيام السماح</label>
                                <input type="number" name="grace_period_days" class="form-control rounded-xl py-2 px-3" min="0" value="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control rounded-xl py-2 px-3" placeholder="example@email.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">الهاتف</label>
                                <input type="text" name="phone" class="form-control rounded-xl py-2 px-3" placeholder="05xxxxxxxx">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">العنوان</label>
                                <textarea name="address" class="form-control rounded-xl py-2 px-3" rows="3" placeholder="العنوان التفصيلي للعميل"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-xl px-4" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold">حفظ العميل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
