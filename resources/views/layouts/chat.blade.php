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
            --chat-teal: #2d5f5d;
            --chat-teal-dark: #1e4a46;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: inherit;
            filter: blur(80px);
            opacity: 0.8;
            z-index: 0;
        }

        /* Chat Container */
        .chat-main-wrapper {
            position: relative;
            z-index: 1;
            width: 90%;
            max-width: 1400px;
            height: 90vh;
            background: white;
            border-radius: 30px;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Header Styles */
        .main-header {
            background: linear-gradient(135deg, var(--chat-teal-dark) 0%, var(--chat-teal) 100%);
            box-shadow: 0 4px 20px rgba(30, 74, 70, 0.3);
            position: relative;
            overflow: hidden;
            flex-shrink: 0;
        }

        .main-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.05)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,112C672,96,768,96,864,112C960,128,1056,160,1152,160C1248,160,1344,128,1392,112L1440,96L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z"></path></svg>');
            background-size: cover;
            opacity: 0.3;
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
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
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

        /* Main Content Area */
        .chat-content-wrapper {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* Navigation */
        .main-nav {
            background: white;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
            display: none; /* Hide navigation in chat layout */
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
            padding: 0;
            background: white;
            overflow: hidden;
        }

        .content-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e2e8f0;
        }
        /* Footer */
        .main-footer {
            display: none; /* Hide footer in chat layout */
        }

        .footer-text {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Chat Header Actions */
        .header-chat-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chat-icon-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: white;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            cursor: pointer;
        }

        .chat-icon-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .chat-main-wrapper {
                width: 95%;
                height: 95vh;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 0;
            }

            .chat-main-wrapper {
                width: 100%;
                height: 100vh;
                border-radius: 0;
            }

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

            .system-subtitle {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>
<div class="chat-main-wrapper">
    <!-- Header Section -->
    <header class="main-header">
        <div class="container-fluid px-4">
            <div class="header-content">
                <!-- Left Side: Back Button & Title -->
                <div class="brand-info">
                    <div class="d-flex align-items-center gap-3">
                        <button class="chat-icon-btn" onclick="window.location.href='{{ route('dashboard') }}'" title="العودة">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                        <button class="chat-icon-btn" onclick="window.location.href='{{ route('dashboard') }}'" title="الرئيسية">
                            <i class="bi bi-house-door"></i>
                        </button>
                        <div>
                            <h1 class="system-name mb-0">المحادثات</h1>
                            <p class="system-subtitle mb-0">تواصل مع فريق الدعم والإدارة</p>
                        </div>
                    </div>
                </div>

                <!-- Right Side: User Profile & Actions -->
                <div class="user-info">
                    <div class="header-chat-actions">
                        <!-- Messages Icon -->
                        <button class="chat-icon-btn" title="الرسائل">
                            <i class="bi bi-chat-dots"></i>
                        </button>
                    </div>

                    <!-- User Profile -->
                    <div class="user-details">
                        <img src="{{ asset(Auth::user()->personal_image) }}" alt="User Avatar" class="user-avatar">
                        <div class="user-text">
                            <div class="user-name">{{ Auth::user()->name ?? 'المستخدم' }}</div>
                            <div class="user-role">{{ Auth::user()->getRoleName() ?? 'مدير النظام' }}</div>
                        </div>
                    </div>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="chat-icon-btn" title="تسجيل الخروج">
                            <i class="bi bi-box-arrow-left"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        {{ $slot }}
    </main>
</div>
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
