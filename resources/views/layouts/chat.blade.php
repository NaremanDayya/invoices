<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        /* Header Styles */
        .main-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 4px 20px rgba(16, 185, 129, 0.3);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .brand-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo-container {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .system-name {
            font-weight: 700;
            color: white;
            font-size: 1.5rem;
            margin: 0;
        }

        .system-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .user-details {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.15);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            backdrop-filter: blur(10px);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .user-text {
            color: white;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
        }

        .user-role {
            font-size: 0.8rem;
            opacity: 0.9;
        }

        .icon-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            position: relative;
        }

        .icon-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 0.7rem;
            display: flex;
            align-items: center;
            justify-content: center;
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

            .user-details {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
<!-- Header Section -->
<header class="main-header">
    <div class="container">
        <div class="header-content">
            <!-- Brand Info -->
            <div class="brand-info">
                <div class="logo-container">
                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-10 w-auto">
                </div>
                <div>
                    <h1 class="system-name">نظام إدارة الفواتير</h1>
                    <p class="system-subtitle">الحل المتكامل لإدارة الفواتير والمستحقات</p>
                </div>
            </div>

            <!-- User Controls -->
            <div class="user-info">
                <!-- Notifications -->
                <button class="icon-btn">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>

                <!-- Messages -->
                <button class="icon-btn">
                    <i class="fas fa-envelope"></i>
                    <span class="notification-badge">5</span>
                </button>

                <!-- User Profile -->
                <div class="user-details">
                    <img src="{{ Auth::user()->personal_image ?? asset('assets/img/default-avatar.png') }}"
                         alt="User Avatar" class="user-avatar">
                    <div class="user-text">
                        <div class="user-name">{{ Auth::user()->name ?? 'المستخدم' }}</div>
                        <div class="user-role">مدير النظام</div>
                    </div>
                </div>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="icon-btn" title="تسجيل الخروج">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
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
                <li class="nav-item">
                    <a href="{{ route('welcome') }}"
                       class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        التقارير المالية
                    </a>
                </li>
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

@livewireScripts
@stack('scripts')
</body>
</html>
