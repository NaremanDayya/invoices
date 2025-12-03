<!DOCTYPE html>
<html lang="ar" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>افاق الخليج - إدارة الفواتير</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; }

        .background-image {
            background-image: url('{{asset('assets/img/BG.jpg')}}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }

        .background-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.65) 0%, rgba(5, 150, 105, 0.65) 50%, rgba(4, 120, 87, 0.65) 100%);
            z-index: 1;
        }

        .content-frame {
            position: relative;
            z-index: 2;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
    </style>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="h-full">
<div class="min-h-full flex">
    <!-- Left Section - Brand/Info -->
    <div class="hidden lg:flex lg:flex-1 lg:flex-col background-image">
        <div class="flex-1 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
            <div class="content-frame max-w-md w-full space-y-8 text-center">
                <!-- Logo -->
                <div class="flex justify-center">
                    <div>
                        <!-- يمكنك إضافة اللوجو هنا إذا أردت -->
                    </div>
                </div>

                <div class="space-y-4">
                    <h1 class="text-4xl font-bold text-white">نظام الفواتير</h1>
                    <p class="text-xl text-white/95 font-light">نظام إدارة الفواتير المتطور - شركة افاق الخليج</p>
                </div>

                <!-- Features -->
                <div class="grid grid-cols-1 gap-6 mt-12">
                    <div class="flex items-center space-x-3 text-white/95 rtl:space-x-reverse">
                        <div class="w-8 h-8 bg-white/25 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-right">إنشاء فواتير احترافية في ثواني</span>
                    </div>
                    <div class="flex items-center space-x-3 text-white/95 rtl:space-x-reverse">
                        <div class="w-8 h-8 bg-white/25 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-right">متابعة أوامر السداد والعملاء</span>
                    </div>
                    <div class="flex items-center space-x-3 text-white/95 rtl:space-x-reverse">
                        <div class="w-8 h-8 bg-white/25 rounded-full flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-right">تصدير تقارير إحصائية</span>
                    </div>
                </div>
                <!-- Testimonial -->
                <div class="mt-12 bg-white/20 backdrop-blur-sm rounded-2xl p-6 border border-white/30">
                    <p class="text-white/90 italic font-medium">"الإدارة السليمة للفواتير هي أساس الاستقرار المالي ووضوح المعاملات.!"</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Section - Login Form -->
    <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="mx-auto w-full max-w-sm lg:w-96">
            <div class="lg:hidden flex justify-center mb-8">
                <div class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-green-500 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>

            <div>
                <img src="{{asset('assets/img/logo.png')}}" class="h-16 w-26 px-20 pb-2 mx-auto">
                <h2 class="text-xl font-bold text-emerald-700 text-center mt-4">مرحباً بحماة المال ومهندسي الاستقرار المالي</h2>
            </div>

            <div class="mt-8">
                <form class="space-y-6" action="{{ route('login') }}" method="POST">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 text-right">البريد الإلكتروني</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required
                                   class="appearance-none text-right block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 @error('email') border-red-300 @enderror"
                                   placeholder="أدخل بريدك الإلكتروني"
                                   value="{{ old('email') }}">
                            @error('email')
                            <p class="mt-2 text-sm text-red-600 text-right">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 text-right">كلمة المرور</label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="current-password" required
                                   class="text-right appearance-none block w-full px-3 py-3 border border-gray-300 rounded-lg placeholder-gray-400 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 transition-colors duration-200 @error('password') border-red-300 @enderror"
                                   placeholder="أدخل كلمة المرور">
                            @error('password')
                            <p class="mt-2 text-sm text-red-600 text-right">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <button type="submit"
                                class="group relative w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200">
                                <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                    <svg class="h-5 w-5 text-emerald-500 group-hover:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                </span>
                            تسجيل الدخول
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>
