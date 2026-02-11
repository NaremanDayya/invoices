<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ Auth::id() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نظام إدارة الفواتير')</title>

    <!-- Google Fonts: Tajawal & Outfit -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- In your scripts section -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <style>
        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            height: auto !important;
            padding: 0.5rem 0.75rem !important;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: normal !important;
            padding: 0 !important;
            color: #334155 !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100% !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--open .select2-selection--single {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
        }

        .select2-dropdown {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.75rem !important;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        }

        .select2-results__option--highlighted {
            background-color: #10b981 !important;
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #e2e8f0 !important;
            border-radius: 0.5rem !important;
            padding: 0.5rem !important;
        }

        .select2-search--dropdown .select2-search__field:focus {
            border-color: #10b981 !important;
            outline: none !important;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --sidebar-bg: #1e4a46;
            --sidebar-hover: #2a635e;
            --primary-accent: #fbbd08;
            --text-muted: #a0aec0;
            --body-bg: #f4f7f6;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: var(--body-bg);
            margin: 0;
            overflow-x: hidden;
            display: flex;
        }

        /* Sidebar Styles */
        #sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            min-height: 100vh;
            color: white;
            transition: all var(--transition-speed);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            position: fixed;
            right: 0; /* Changed to right for RTL */
            top: 0;
            bottom: 0;
        }

        #sidebar.collapsed {
            width: 80px;
        }

        .sidebar-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-brand {
            font-weight: 800;
            font-size: 1.25rem;
            white-space: nowrap;
            overflow: hidden;
            transition: opacity 0.2s;
        }

        #sidebar.collapsed .sidebar-brand {
            opacity: 0;
            width: 0;
        }

        .nav-section-title {
            padding: 15px 25px 5px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-muted);
            letter-spacing: 1px;
            white-space: nowrap;
        }

        #sidebar.collapsed .nav-section-title {
            display: none;
        }

        .sidebar-nav {
            flex: 1;
            padding: 10px 0;
        }

        .nav-item {
            padding: 2px 15px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .nav-link:hover {
            background-color: var(--sidebar-hover);
            color: white;
        }

        .nav-link i {
            width: 24px;
            font-size: 1.1rem;
            margin-left: 15px; /* Margin left for icon in RTL */
            text-align: center;
        }

        .nav-link.active {
            background-color: var(--primary-accent);
            color: #1e4a46 !important;
            font-weight: 700;
        }

        .nav-link span {
            transition: opacity 0.2s;
        }

        #sidebar.collapsed .nav-link span {
            opacity: 0;
            width: 0;
            display: none;
        }

        /* Profile Section in Sidebar */
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: rgba(0, 0, 0, 0.2);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: white;
        }

        .user-avatar-sm {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            object-fit: cover;
        }

        .user-info-text {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: opacity 0.2s;
        }

        #sidebar.collapsed .user-info-text {
            opacity: 0;
            width: 0;
            display: none;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        /* Main Content Wrapper */
        #content-wrapper {
            flex: 1;
            margin-right: 260px; /* Responsive to sidebar width */
            transition: all var(--transition-speed);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #content-wrapper.expanded {
            margin-right: 80px;
        }

        /* Top Bar */
        .topbar {
            height: 70px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .search-box {
            background: #f8fafc;
            border: 1px solid #eef2f7;
            border-radius: 12px;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            width: 350px;
        }

        .search-box input {
            border: none;
            background: transparent;
            outline: none;
            width: 100%;
            font-size: 0.9rem;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .action-icon {
            color: #64748b;
            font-size: 1.2rem;
            text-decoration: none;
            position: relative;
        }

        .badge-dot {
            position: absolute;
            top: 0;
            right: 0;
            width: 8px;
            height: 8px;
            background: #ef4444;
            border: 2px solid white;
            border-radius: 50%;
        }

        /* Page Header */
        .page-header {
            padding: 25px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1a202c;
            margin: 0;
        }

        .page-title p {
            color: #718096;
            margin: 0;
            font-size: 0.9rem;
        }

        /* Content Area */
        .main-content {
            padding: 0 30px 30px;
            flex: 1;
        }

        .content-card {
            background: white;
            border-radius: 20px;
            padding: 25px;
            box-shadow: var(--card-shadow);
            border: 1px solid #edf2f7;
        }

        /* Utilities */
        .rounded-xl { border-radius: 16px; }
        .text-primary-accent { color: var(--primary-accent); }
        .bg-primary-accent { background-color: var(--primary-accent); }

        /* Mobile Overlay */
        @media (max-width: 991.98px) {
            #sidebar {
                right: -260px;
            }
            #sidebar.show {
                right: 0;
            }
            #content-wrapper {
                margin-right: 0 !important;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
    <!-- Mobile Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <aside id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <i class="fas fa-file-invoice-dollar text-primary-accent me-2"></i>
                <span>نظام فواتيرك</span>
            </div>
            <button class="btn btn-link text-white p-0 d-none d-lg-block" id="toggle-sidebar">
                <i class="bi bi-list fs-4"></i>
            </button>
            <button class="btn btn-link text-white p-0 d-lg-none" id="close-sidebar">
                <i class="bi bi-x-lg fs-4"></i>
            </button>
        </div>

        <div class="sidebar-nav">
            <div class="nav-section-title">القائمة الرئيسية</div>
            <div class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-grid-fill"></i>
                    <span>لوحة التحكم</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('invoices.index') }}" class="nav-link {{ request()->routeIs('invoices.*') && !request()->routeIs('invoices.salary.*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-text-fill"></i>
                    <span>الفواتير</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('invoices.index', ['type' => 'salary_invoice']) }}" class="nav-link {{ request()->get('type') === 'salary_invoice' ? 'active' : '' }}">
                    <i class="bi bi-cash-stack"></i>
                    <span>فواتير الرواتب</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('clients.index') }}" class="nav-link {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>العملاء</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('payments.index') }}" class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2"></i>
                    <span>أوامر الدفع</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('employees.index') }}" class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>الموظفين</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                    <i class="bi bi-gear-fill"></i>
                    <span>الخدمات</span>
                </a>
            </div>

            <div class="nav-section-title">إدارة النظام</div>
            <div class="nav-item">
                <a href="{{ route('invoice-statuses.index') }}" class="nav-link {{ request()->routeIs('invoice-statuses.*') ? 'active' : '' }}">
                    <i class="bi bi-tags-fill"></i>
                    <span>حالات الفواتير</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="{{ route('dashboard.reports.issued-invoices') }}" class="nav-link {{ request()->routeIs('dashboard.reports.*') ? 'active' : '' }}">
                    <i class="bi bi-bar-chart-fill"></i>
                    <span>التقارير</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-gear-fill"></i>
                    <span>الإعدادات</span>
                </a>
            </div>
        </div>

        <div class="sidebar-footer">
            <div class="user-profile">
                <img src="{{ asset(Auth::user()->personal_image ?? 'assets/img/default-avatar.png') }}" alt="User" class="user-avatar-sm">
                <div class="user-info-text">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role">مدير النظام</span>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                    @csrf
                    <button type="submit" class="btn btn-link text-white p-0">
                        <i class="bi bi-box-arrow-left fs-5"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div id="content-wrapper">
        <!-- Top Bar -->
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-link text-dark p-0 d-lg-none" id="mobile-sidebar-toggle">
                    <i class="bi bi-list fs-3"></i>
                </button>
                <div class="search-box d-none d-md-flex">
                    <i class="bi bi-search text-muted"></i>
                    <input type="text" placeholder="بحث في النظام...">
                </div>
            </div>

            <div class="topbar-actions">
                <div class="d-none d-sm-block text-end">
                    <div class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">{{ now()->translatedFormat('l, d F Y') }}</div>
                </div>
                <a href="#" class="action-icon">
                    <i class="bi bi-bell"></i>
                    <span class="badge-dot"></span>
                </a>
                <livewire:unread-messages-count />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <div class="page-header">
                <div class="page-title">
                    <h1>@yield('page_title', 'لوحة التحكم')</h1>
                    <p>@yield('page_subtitle', 'مرحباً بك في نظام إدارة الفواتير')</p>
                </div>
                <div class="page-actions">
                    @yield('page_actions')
                </div>
            </div>

            <div class="main-content">
                <!-- Messages -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show rounded-xl mb-4 border-0 shadow-sm" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show rounded-xl mb-4 border-0 shadow-sm" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Export Libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <script>
        $(document).ready(function() {
            // Sidebar Toggle
            $('#toggle-sidebar').click(function() {
                $('#sidebar').toggleClass('collapsed');
                $('#content-wrapper').toggleClass('expanded');
            });

            // Mobile Sidebar Toggle
            $('#mobile-sidebar-toggle').click(function() {
                $('#sidebar').addClass('show');
                $('#sidebar-overlay').addClass('show');
            });

            $('#close-sidebar, #sidebar-overlay').click(function() {
                $('#sidebar').removeClass('show');
                $('#sidebar-overlay').removeClass('show');
            });

            // Flatpickr localization
            flatpickr.localize(flatpickr.l10ns.ar);

            // Initialize Select2 on all select elements
            $('select').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                language: {
                    noResults: function() {
                        return "لا توجد نتائج";
                    },
                    searching: function() {
                        return "جاري البحث...";
                    }
                },
                placeholder: function() {
                    return $(this).data('placeholder') || 'اختر...';
                },
                allowClear: true,
                width: '100%'
            });

            // Auto-hide alert
            setTimeout(function() {
                $(".alert").fadeOut('slow');
            }, 5000);
        });
    </script>

    @livewireScripts
    @stack('scripts')
</body>
</html>
