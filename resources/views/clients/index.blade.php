@extends('layouts.master')

@section('title', 'إدارة العملاء')
@section('page_title', 'العملاء')
@section('page_subtitle', 'إدارة قائمة العملاء وبياناتهم')
@push('styles')
    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-mini-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 1px solid #edf2f7;
            transition: all 0.3s;
        }
        .stat-mini-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        .stat-icon-box {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        .stat-info h3 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0;
            color: #1a202c;
        }
        .stat-info p {
            font-size: 0.85rem;
            color: #718096;
            margin: 0;
            font-weight: 500;
        }
    </style>
@endpush
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

                    <div class="stat-mini-card">
                        <div class="stat-info">
                            <h3>{{ $clients->total() }}</h3>
                            <p>إجمالي العملاء</p>
                        </div>
                        <div class="stat-icon-box" style="background: #fff5f5; color: #e53e3e;">
                            <i class="bi bi-people text-primary-accent fs-4"></i>
                        </div>
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
                                                            <label class="form-label small fw-bold text-muted">الرقم الضريبي <span class="text-danger">*</span></label>
                                                            <input type="text" name="tax_number" class="form-control rounded-xl py-2 px-3" value="{{ $client->tax_number }}" required maxlength="15" pattern="[0-9]{15}">
                                                            <small class="text-muted">يجب أن يكون 15 رقم بالضبط</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">العنوان الوطني</label>
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
            <div class="modal-content border-0 shadow-2xl rounded-2xl overflow-hidden">
                <div class="modal-header bg-gradient-to-r from-emerald-600 to-teal-600 text-white p-4">
                    <h5 class="modal-title fw-bold text-white flex items-center gap-2">
                        <i class="bi bi-person-plus-fill"></i>
                        إضافة عميل جديد
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('clients.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 bg-slate-50">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">اسم العميل <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required placeholder="أدخل اسم العميل">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">يوم الدفع</label>
                                <input type="number" name="default_payment_day" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" min="1" max="31" value="1">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-slate-600">أيام السماح</label>
                                <input type="number" name="grace_period_days" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" min="0" value="0">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">البريد الإلكتروني</label>
                                <input type="email" name="email" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" placeholder="example@email.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">الهاتف</label>
                                <input type="text" name="phone" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" placeholder="05xxxxxxxx">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">الرقم الضريبي <span class="text-danger">*</span></label>
                                <input type="text" name="tax_number" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" required placeholder="أدخل 15 رقم" maxlength="15" pattern="[0-9]{15}">
                                <small class="text-muted">يجب أن يكون 15 رقم بالضبط</small>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">العنوان الوطني</label>
                                <textarea name="address" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" rows="3" placeholder="العنوان الوطني"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-t border-slate-100 p-3 bg-white">
                        <button type="button" class="btn btn-light rounded-xl px-4 font-bold text-slate-600" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl px-6 py-2 fw-bold shadow-lg shadow-emerald-500/30 border-0 hover:scale-105 transition-transform">حفظ العميل</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
