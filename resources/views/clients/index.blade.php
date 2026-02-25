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
        .table-card {
            background: white;
            border-radius: 20px;
            border: 1px solid #edf2f7;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }
        .custom-table {
            width: 100%;
            margin-bottom: 0;
        }
        .custom-table th {
            background: #f8fafc;
            padding: 18px 15px;
            font-size: 0.85rem;
            font-weight: 700;
            color: #4a5568;
            border-bottom: 1px solid #edf2f7;
            text-align: right;
        }
        .custom-table td {
            padding: 18px 15px;
            vertical-align: middle;
            font-size: 0.9rem;
            color: #2d3748;
            border-bottom: 1px solid #f7fafc;
        }
        .custom-table tbody tr:hover {
            background: #f8fafc;
        }
        .client-avatar {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }
        .client-avatar-placeholder {
            width: 45px;
            height: 45px;
            border-radius: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .client-name {
            font-weight: 700;
            color: #1e293b;
            text-decoration: none;
            transition: color 0.2s;
        }
        .client-name:hover {
            color: #1e4a46;
        }
        .invoice-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .invoice-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: 1px solid #e2e8f0;
            background: white;
        }
        .btn-action:hover {
            background: #f8fafc;
            border-color: #cbd5e0;
            transform: translateY(-2px);
        }
        .search-wrapper {
            position: relative;
        }
        .search-wrapper i {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }
        .search-wrapper input {
            padding-right: 42px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s;
        }
        .search-wrapper input:focus {
            border-color: #1e4a46;
            box-shadow: 0 0 0 3px rgba(30, 74, 70, 0.1);
        }
    </style>
@endpush
@section('page_actions')
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2"
                onclick="exportClientsToPDF()">
            <i class="bi bi-file-pdf"></i>
            <span>تصدير PDF</span>
        </button>
        <button class="btn btn-outline-primary rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2"
                onclick="exportClientsToExcel()">
            <i class="bi bi-file-earmark-excel"></i>
            <span>تصدير Excel</span>
        </button>
        @if(auth()->user()->hasPermission('add_clients'))
        <button class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold d-flex align-items-center gap-2"
                data-bs-toggle="modal" data-bs-target="#createClientModal">
            <i class="bi bi-plus-lg"></i>
            <span>عميل جديد</span>
        </button>
        @endif
    </div>
@endsection

@section('content')
    <!-- Stats Section -->
    <div class="stats-grid">
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $clients->total() }}</h3>
                <p>إجمالي العملاء</p>
            </div>
            <div class="stat-icon-box" style="background: #e6fffa; color: #319795;">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $clients->where('invoices_count', '>', 0)->count() }}</h3>
                <p>عملاء نشطين</p>
            </div>
            <div class="stat-icon-box" style="background: #d1fae5; color: #059669;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $clients->sum('invoices_count') }}</h3>
                <p>إجمالي الفواتير</p>
            </div>
            <div class="stat-icon-box" style="background: #dbeafe; color: #2563eb;">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
        </div>
        <div class="stat-mini-card">
            <div class="stat-info">
                <h3>{{ $clients->filter(fn($c) => $c->email)->count() }}</h3>
                <p>لديهم بريد إلكتروني</p>
            </div>
            <div class="stat-icon-box" style="background: #fef3c7; color: #d97706;">
                <i class="bi bi-envelope-fill"></i>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-xl border border-gray-100 p-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="search-wrapper flex-grow-1">
                <i class="bi bi-search"></i>
                <input type="text" id="clientLiveSearch" value="{{ request('search') }}"
                       class="form-control" placeholder="البحث عن عميل بالاسم، البريد، الهاتف، أو العنوان..."
                       style="font-size: 0.9rem;" autocomplete="off">
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="table-card">
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>العميل</th>
                        <th>العنوان الوطني</th>
                        <th>البريد الإلكتروني</th>
                        <th>الهاتف</th>
                        <th>الرقم الضريبي</th>
                        <th class="text-center">الفواتير</th>
                        <th class="text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $client)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    @if($client->logo)
                                        <img src="{{ asset('storage/' . $client->logo) }}" alt="{{ $client->name }}" class="client-avatar">
                                    @else
                                        <div class="client-avatar-placeholder">
                                            {{ mb_substr($client->name, 0, 1) }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('clients.show', $client) }}" class="client-name d-block">{{ $client->name }}</a>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($client->address)
                                    <span class="text-muted" title="{{ $client->address }}">{{ Str::limit($client->address, 50) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($client->email)
                                    <span class="text-muted">{{ $client->email }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td dir="ltr" style="text-align: right;">
                                @if($client->phone)
                                    <span class="text-muted">{{ $client->phone }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($client->tax_number)
                                    <code class="bg-light px-2 py-1 rounded" style="font-size: 0.85rem;">{{ $client->tax_number }}</code>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('invoices.index', ['client_id' => $client->id]) }}"
                                   class="invoice-badge"
                                   title="عرض فواتير {{ $client->name }}">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                    <span>{{ $client->invoices_count ?? 0 }}</span>
                                </a>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('clients.monthly-report', $client) }}"
                                       class="btn-action"
                                       title="التقرير الشهري">
                                        <i class="bi bi-file-earmark-bar-graph text-info"></i>
                                    </a>
                                    @if(auth()->user()->hasPermission('add_clients'))
                                    <button class="btn-action"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editClientModal{{ $client->id }}"
                                            title="تعديل">
                                        <i class="bi bi-pencil text-primary"></i>
                                    </button>
                                    <form action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا العميل؟')"
                                                title="حذف">
                                            <i class="bi bi-trash text-danger"></i>
                                        </button>
                                    </form>
                                    @endif
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
                                            <form action="{{ route('clients.update', $client) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4">
                                                    <div class="row g-3">
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">اسم العميل</label>
                                                            <input type="text" name="name" class="form-control rounded-xl py-2 px-3" value="{{ $client->name }}" required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="form-label small fw-bold text-muted">شعار العميل</label>
                                                            @if($client->logo)
                                                                <div class="mb-2">
                                                                    <img src="{{ asset('storage/' . $client->logo) }}" alt="{{ $client->name }}" class="rounded" style="max-width: 100px; max-height: 100px; object-fit: contain;">
                                                                </div>
                                                            @endif
                                                            <input type="file" name="logo" class="form-control rounded-xl py-2 px-3" accept="image/*">
                                                            <small class="text-muted">صيغ مدعومة: JPG, PNG, GIF, SVG (حد أقصى 2MB)</small>
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
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                                    <div class="mb-3" style="width: 80px; height: 80px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-people fs-1 text-muted"></i>
                                    </div>
                                    <h5 class="text-muted mb-2">لا يوجد عملاء</h5>
                                    <p class="text-muted small mb-3">ابدأ بإضافة عميل جديد لإدارة فواتيرك</p>
                                    <button class="btn bg-primary-accent border-0 rounded-xl px-4 py-2 fw-bold"
                                            data-bs-toggle="modal" data-bs-target="#createClientModal">
                                        <i class="bi bi-plus-lg me-2"></i>إضافة عميل جديد
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="px-4 py-3 border-top d-flex justify-content-between align-items-center" style="background: #f8fafc;">
                <div class="text-muted small">
                    عرض {{ $clients->firstItem() ?? 0 }} إلى {{ $clients->lastItem() ?? 0 }} من {{ $clients->total() ?? 0 }} عميل
                </div>
                {{ $clients->links() }}
            </div>
        @endif
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
                <form action="{{ route('clients.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body p-4 bg-slate-50">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">اسم العميل <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm focus:border-emerald-500 focus:ring-emerald-500" required placeholder="أدخل اسم العميل">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-slate-600">شعار العميل</label>
                                <input type="file" name="logo" class="form-control rounded-xl py-2 px-3 border-slate-200 shadow-sm" accept="image/*">
                                <small class="text-muted">صيغ مدعومة: JPG, PNG, GIF, SVG (حد أقصى 2MB)</small>
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

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.sheetjs.com/xlsx-0.18.5/package/dist/xlsx.full.min.js"></script>
    <script>
        function exportClientsToPDF() {
            if (typeof html2pdf === 'undefined') {
                alert('جاري تحميل مكتبة PDF، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
                return;
            }

            const companyLogoSrc = '{{ asset("assets/img/logo.png") }}';
            const today = new Date().toLocaleDateString('ar-SA', { year: 'numeric', month: 'long', day: 'numeric' });
            const todayShort = new Date().toISOString().split('T')[0];

            // Convert logo to white version via canvas (avoids CORS taint in html2canvas)
            function getWhiteLogoDataUrl(src, callback) {
                const img = new Image();
                img.crossOrigin = 'anonymous';
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;
                    const ctx = canvas.getContext('2d');
                    // Draw original image
                    ctx.drawImage(img, 0, 0);
                    // Overlay white using source-in to make all opaque pixels white
                    ctx.globalCompositeOperation = 'source-in';
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                    callback(canvas.toDataURL('image/png'));
                };
                img.onerror = function() { callback(''); };
                img.src = src + '?v=' + Date.now();
            }

            getWhiteLogoDataUrl(companyLogoSrc, function(whiteLogoDataUrl) {
            const companyLogo = whiteLogoDataUrl || companyLogoSrc;

            const stats = {
                total: {{ $clients->total() ?? 0 }},
                active: {{ $clients->where('invoices_count', '>', 0)->count() }},
                totalInvoices: {{ $clients->sum('invoices_count') }},
                withEmail: {{ $clients->filter(fn($c) => $c->email)->count() }}
            };

            // Build table rows from visible table
            let tableRows = '';
            document.querySelectorAll('.custom-table tbody tr').forEach((row, i) => {
                const cells = row.querySelectorAll('td');
                if (!cells.length || cells.length < 6) return;

                const bg = i % 2 === 0 ? '#ffffff' : '#f8fafc';

                // Extract client name and location from the data structure
                const clientCell = cells[0];
                let clientName = '-';
                let clientLogoSrc = '';

                if (clientCell) {
                    const clientNameEl = clientCell.querySelector('.client-name');
                    clientName = clientNameEl?.innerText.trim() || '-';

                    // Extract logo src if present
                    const logoImg = clientCell.querySelector('img.client-avatar');
                    if (logoImg) {
                        clientLogoSrc = logoImg.src || '';
                    }
                }

                const clientLocation = cells[1]?.innerText.trim() || '-';
                const email = cells[2]?.innerText.trim() || '-';
                const phone = cells[3]?.innerText.trim() || '-';
                const taxNumber = cells[4]?.querySelector('code')?.innerText.trim() || cells[4]?.innerText.trim() || '-';
                const invoicesCount = cells[5]?.innerText.trim() || '0';

                const logoHtml = clientLogoSrc
                    ? `<img src="${clientLogoSrc}" style="width:28px;height:28px;border-radius:6px;object-fit:cover;margin-left:8px;vertical-align:middle;" onerror="this.style.display='none'">`
                    : `<span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:6px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;font-weight:700;font-size:11px;margin-left:8px;vertical-align:middle;">${clientName.charAt(0)}</span>`;

                const td = 'padding:8px 10px;border-bottom:1px solid #e2e8f0;font-size:11px;vertical-align:middle;text-align:center;';
                tableRows += `
                <tr style="background:${bg};">
                    <td style="${td}text-align:right;">
                        <div style="display:flex;align-items:center;gap:6px;">
                            ${logoHtml}
                            <span style="font-weight:700;color:#1e293b;white-space:nowrap;">${clientName}</span>
                        </div>
                    </td>
                    <td style="${td}color:#64748b;">${clientLocation}</td>
                    <td style="${td}color:#64748b;">${email}</td>
                    <td style="${td}color:#64748b;" dir="ltr">${phone}</td>
                    <td style="${td}"><code style="background:#f1f5f9;padding:2px 6px;border-radius:4px;font-size:10px;">${taxNumber}</code></td>
                    <td style="${td}"><span style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;padding:4px 10px;border-radius:12px;font-size:10px;font-weight:600;">${invoicesCount}</span></td>
                </tr>`;
            });

            const html = `<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
<meta charset="UTF-8">
<title>تقرير العملاء</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body { font-family:'Tahoma','Arial',sans-serif; direction:rtl; background:#fff; color:#1e293b; font-size:12px; padding:16px; word-spacing:normal; letter-spacing:normal; }
.pdf-header { background:linear-gradient(135deg,#1e4a46,#2d6a65); color:white; padding:18px 24px; border-radius:12px; margin-bottom:16px; display:flex; justify-content:space-between; align-items:center; }
.pdf-header-title { text-align:right; }
.pdf-header-title h1 { font-size:22px; font-weight:700; margin-bottom:6px; }
.pdf-header-title p { font-size:12px; opacity:0.85; }
.logo-box { display:flex; align-items:center; }
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; margin-bottom:14px; }
.stat-box { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; text-align:center; }
.stat-box .sl { font-size:10px; color:#64748b; margin-bottom:4px; }
.stat-box .sv { font-size:18px; font-weight:700; }
table { width:100%; border-collapse:collapse; font-size:10px; }
thead th { background:#1e4a46; color:#fff; padding:9px 8px; font-weight:600; white-space:nowrap; font-size:10px; }
tbody td { padding:8px; border-bottom:1px solid #e2e8f0; vertical-align:middle; }
.logo-white {
    filter: brightness(0) invert(1) !important;
    -webkit-filter: brightness(0) invert(1) !important;
}
.pdf-footer { margin-top:16px; padding:12px 20px; background:#f8fafc; border-radius:8px; display:flex; justify-content:space-between; align-items:center; color:#64748b; font-size:10px; }
</style>
</head>
<body>
<div class="pdf-header">
  <div class="pdf-header-title">
    <h1>تقرير العملاء</h1>
    <p>نظام إدارة الفواتير — قائمة شاملة بجميع العملاء</p>
  </div>
  <div class="logo-box">
    ${companyLogo ? `<img src="${companyLogo}" style="height:42px;">` : ''}
  </div>
</div>

<div class="stats-grid">
  <div class="stat-box"><div class="sl">إجمالي العملاء</div><div class="sv" style="color:#319795;">${stats.total}</div></div>
  <div class="stat-box"><div class="sl">عملاء نشطين</div><div class="sv" style="color:#059669;">${stats.active}</div></div>
  <div class="stat-box"><div class="sl">إجمالي الفواتير</div><div class="sv" style="color:#2563eb;">${stats.totalInvoices}</div></div>
  <div class="stat-box"><div class="sl">لديهم بريد إلكتروني</div><div class="sv" style="color:#d97706;">${stats.withEmail}</div></div>
</div>

<table>
  <thead>
    <tr>
      <th style="text-align:center;">اسم العميل</th>
      <th style="text-align:center;">العنوان الوطني</th>
      <th style="text-align:center;">البريد الإلكتروني</th>
      <th style="text-align:center;">الهاتف</th>
      <th style="text-align:center;">الرقم الضريبي</th>
      <th style="text-align:center;">الفواتير</th>
    </tr>
  </thead>
  <tbody>${tableRows}</tbody>
</table>

<div class="pdf-footer">
  <span style="font-weight:700;color:#1e4a46;">نظام إدارة الفواتير</span>
  <span>تقرير العملاء — تاريخ التصدير: ${today}</span>
</div>
</body>
</html>`;

            const container = document.createElement('div');
            container.innerHTML = html;
            document.body.appendChild(container);

            html2pdf().set({
                margin: [10, 10, 10, 10],
                filename: `تقرير_العملاء_${todayShort}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true, logging: false },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            }).from(container).save().then(() => {
                document.body.removeChild(container);
                if (window.toastr) toastr.success('تم تصدير العملاء إلى PDF بنجاح');
            });
            }); // end getWhiteLogoDataUrl
        }
        function exportClientsToExcel() {
            if (typeof XLSX === 'undefined') {
                alert('جاري تحميل مكتبة Excel، يرجى المحاولة مرة أخرى بعد ثوانٍ...');
                return;
            }

            const todayShort = new Date().toISOString().split('T')[0];

            // Clone the table
            const originalTable = document.querySelector('.custom-table');
            const clonedTable = originalTable.cloneNode(true);

            // Remove the last header (actions column)
            clonedTable.querySelectorAll('thead tr').forEach(row => {
                const cells = row.querySelectorAll('th');
                if (cells.length) cells[cells.length - 1].remove();
            });

            // Process body rows: flatten name+address cell, remove actions cell
            clonedTable.querySelectorAll('tbody tr').forEach(row => {
                const cells = row.querySelectorAll('td');
                if (!cells.length || cells.length < 6) return;

                // Replace first cell (name+address combined) with just the name text
                const nameEl = cells[0].querySelector('.client-name');
                cells[0].innerHTML = nameEl ? nameEl.innerText.trim() : cells[0].innerText.trim();

                // Remove last cell (actions)
                cells[cells.length - 1].remove();

                // Clean invoice badge cell - keep just the number
                const badge = row.querySelectorAll('td')[4];
                if (badge) badge.innerText = badge.innerText.trim();
            });

            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(clonedTable);

            // Auto-size columns
            const range = XLSX.utils.decode_range(ws['!ref']);
            const colWidths = [];
            for (let C = range.s.c; C <= range.e.c; C++) {
                let maxLen = 10;
                for (let R = range.s.r; R <= range.e.r; R++) {
                    const cell = ws[XLSX.utils.encode_cell({ r: R, c: C })];
                    if (cell && cell.v) maxLen = Math.max(maxLen, String(cell.v).length + 4);
                }
                colWidths.push({ wch: Math.min(maxLen, 40) });
            }
            ws['!cols'] = colWidths;

            XLSX.utils.book_append_sheet(wb, ws, 'العملاء');
            XLSX.writeFile(wb, `تقرير_العملاء_${todayShort}.xlsx`);

            if (window.toastr) toastr.success('تم تصدير العملاء إلى Excel بنجاح');
        }

        // Live Search for Clients Table
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('clientLiveSearch');
            if (!searchInput) return;

            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                const rows = document.querySelectorAll('.custom-table tbody tr');

                rows.forEach(function(row) {
                    if (row.querySelector('td[colspan]')) return; // skip empty-state row
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(query) ? '' : 'none';
                });
            });
        });
    </script>
@endpush
