<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="{{ Auth::id() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'نظام إدارة الفواتير')</title>

    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        :root {
            --primary: #10b981;
            --primary-light: #34d399;
            --primary-dark: #059669;
            --secondary: #047857;
            --light: #f0fdf4;
            --dark: #064e3b;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8fafc;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Modern Topbar Styles */
        .modern-topbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid #e2e8f0;
        }

        .topbar-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            min-height: 70px;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
        }

        .brand-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: #1e4a46;
            margin: 0;
            line-height: 1.2;
        }

        .brand-subtitle {
            font-size: 0.75rem;
            color: #64748b;
            margin: 0;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .current-date {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
        }

        .topbar-icon-wrapper {
            position: relative;
        }

        .topbar-icon-btn {
            background: transparent;
            border: none;
            color: #64748b;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            position: relative;
            cursor: pointer;
        }

        .topbar-icon-btn:hover {
            background: #f1f5f9;
            color: #1e4a46;
        }

        .icon-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            border: 2px solid white;
        }

        .user-profile-section {
            position: relative;
        }

        .user-profile-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 0.5rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.2s;
            cursor: pointer;
        }

        .user-profile-btn:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }

        .profile-avatar {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }

        .profile-info {
            display: flex;
            flex-direction: column;
            text-align: right;
        }

        .profile-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
            line-height: 1.2;
        }

        .profile-role {
            font-size: 0.75rem;
            color: #64748b;
        }

        .dropdown-menu {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            padding: 0.5rem;
            min-width: 200px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        .dropdown-item:hover {
            background: #f1f5f9;
        }

        .dropdown-item.text-danger:hover {
            background: #fee2e2;
        }

        /* Navigation */
        .main-nav {
            background: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
        }

        .nav-container {
            display: flex;
            justify-content: center;
        }

        .nav-list {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 0.5rem;
        }

        .nav-item {
            position: relative;
        }

        .nav-link {
            color: #64748b;
            text-decoration: none;
            font-weight: 500;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            transition: all 0.3s ease;
            border-radius: 10px;
            margin: 0.25rem;
        }

        .nav-link i {
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary);
            background: var(--light);
        }

        .nav-link:hover i {
            transform: scale(1.1);
        }

        .nav-link.active {
            color: var(--primary);
            background: var(--light);
            font-weight: 600;
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            bottom: 0;
            right: 50%;
            transform: translateX(50%);
            width: 30px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px 3px 0 0;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 2rem 0;
            background: #f8fafc;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        /* Footer */
        .main-footer {
            background: linear-gradient(135deg, var(--dark) 0%, #0f766e 100%);
            color: white;
            padding: 1.5rem 0;
            text-align: center;
            margin-top: auto;
            width: 100%;
            position: relative;
        }

        .footer-text {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-list {
                flex-direction: column;
                gap: 0;
            }

            .nav-link {
                justify-content: center;
                border-radius: 0;
            }

            .brand-text {
                display: none;
            }

            .topbar-wrapper {
                padding: 0.75rem 0;
            }

            .topbar-actions {
                gap: 0.75rem;
            }

            .current-date {
                display: none !important;
            }

            .user-profile-btn {
                padding: 0.5rem;
            }

            .profile-info {
                display: none !important;
            }
        }

        @media (max-width: 576px) {
            .topbar-icon-wrapper {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
<!-- Modern Header Section -->
<header class="modern-topbar">
    <div class="container-fluid px-4">
        <div class="topbar-wrapper">
            <!-- Left Section: Brand -->
            <div class="topbar-brand">
                <i class="fas fa-file-invoice-dollar text-warning me-2" style="font-size: 1.5rem;"></i>
                <div class="brand-text">
                    <h1 class="brand-title">نظام فواتيرك</h1>
                    <p class="brand-subtitle">إدارة الفواتير والمحادثات</p>
                </div>
            </div>

            <!-- Right Section: User Controls -->
            <div class="topbar-actions">
                <!-- Current Date -->
                <div class="current-date d-none d-md-block">
                    <i class="bi bi-calendar3 me-2"></i>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>

                <!-- Notifications -->
                <div class="topbar-icon-wrapper">
                    <button class="topbar-icon-btn">
                        <i class="bi bi-bell"></i>
                        <span class="icon-badge">3</span>
                    </button>
                </div>

                <!-- Messages Counter -->
                <livewire:unread-messages-count />

                <!-- User Profile Dropdown -->
                <div class="user-profile-section">
                    <div class="dropdown">
                        <button class="user-profile-btn dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset(Auth::user()->personal_image ?? 'assets/img/default-avatar.png') }}" 
                                 alt="User Avatar" 
                                 class="profile-avatar">
                            <div class="profile-info d-none d-lg-block">
                                <span class="profile-name">{{ Auth::user()->name ?? 'المستخدم' }}</span>
                                <span class="profile-role">{{ Auth::user()->getRoleName() ?? 'مدير النظام' }}</span>
                            </div>
                            <i class="bi bi-chevron-down ms-2"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="#" onclick="document.getElementById('profilePhotoInput').click(); return false;">
                                    <i class="bi bi-person-circle me-2"></i>
                                    تحديث الصورة الشخصية
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-left me-2"></i>
                                        تسجيل الخروج
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    
                    <!-- Hidden Photo Upload Form -->
                    <form id="avatarUploadForm" action="{{ route('admin.updatePhoto') }}" method="POST" enctype="multipart/form-data" style="display: none;">
                        @csrf
                        <input type="file" id="profilePhotoInput" name="personal_image" accept="image/jpeg,image/png,image/gif">
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<!-- Navigation -->
<nav class="main-nav">
    <div class="container">
        <div class="nav-container">
            <ul class="nav-list">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        لوحة التحكم
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('invoices.index') }}"
                       class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                        <i class="fas fa-file-invoice"></i>
                        إدارة الفواتير
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('payments.index') }}"
                       class="nav-link {{ request()->routeIs('payments.*') ? 'active' : '' }}">
                        <i class="fas fa-credit-card"></i>
                        أوامر السداد
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}"
                       class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        إدارة العمالة
                    </a>
                </li>
                {{--                <li class="nav-item">--}}
                {{--                    <a href="{{ route('welcome') }}"--}}
                {{--                       class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">--}}
                {{--                        <i class="fas fa-chart-bar"></i>--}}
                {{--                        التقارير المالية--}}
                {{--                    </a>--}}
                {{--                </li>--}}
                <li class="nav-item">
                    <a href="{{ route('chat.index') }}"
                       class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i>
                        المحادثات
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main>
    {{ $slot }}
</main>
<!-- Footer -->
{{--<footer class="main-footer">--}}
{{--    <div class="container">--}}
{{--        <p class="footer-text">--}}
{{--            جميع الحقوق محفوظة &copy; {{ date('Y') }} نظام إدارة الفواتير - شركة افاق الخليج--}}
{{--        </p>--}}
{{--    </div>--}}
{{--</footer>--}}
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

<script>
    // Auto-dismiss alerts after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Flatpickr
    flatpickr.localize(flatpickr.l10ns.ar);
</script>
<script>
    document.getElementById('profilePhotoInput').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            const maxSize = 2048 * 1024; // 2MB
            const editBtn = document.querySelector(
                '[onclick="document.getElementById(\'profilePhotoInput\').click()"]');

            // Validation
            if (!validTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'الرجاء اختيار صورة بصيغة JPEG أو PNG أو GIF',
                    confirmButtonText: 'حسناً'
                });
                return;
            }

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ',
                    text: 'حجم الملف يجب أن لا يتجاوز 2MB',
                    confirmButtonText: 'حسناً'
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileImage').src = e.target.result;
            }
            reader.readAsDataURL(file);

            const originalContent = editBtn.innerHTML;
            editBtn.innerHTML = '<i class="bi bi-arrow-clockwise animate-spin"></i>';
            editBtn.disabled = true;

            document.getElementById('avatarUploadForm').submit();
        }
    });
</script>

@livewireScripts
@stack('scripts')
</body>
</html>
