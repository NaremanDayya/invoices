@extends('layouts.master')

@section('title', 'لوحة تحليل الفواتير والعمالة')
@push('styles')
    <style>
        .stat-card {
            transition: all 0.3s ease;
            border-radius: 12px;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        .stat-icon {
            transition: all 0.3s ease;
        }
        .stat-card:hover .stat-icon {
            transform: scale(1.1);
        }
        .section-title {
            position: relative;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .section-title:after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 60px;
            height: 3px;
            border-radius: 2px;
        }

        /* Section Background Colors */
        .invoices-section {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
            border: 1px solid #bae6fd;
            border-radius: 16px;
        }

        .employees-section {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 1px solid #bbf7d0;
            border-radius: 16px;
        }

        .financial-section {
            background: linear-gradient(135deg, #fef7ff 0%, #f3e8ff 100%);
            border: 1px solid #e9d5ff;
            border-radius: 16px;
        }

        .actions-section {
            background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);
            border: 1px solid #fdba74;
            border-radius: 16px;
        }

        .section-header {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            border-radius: 12px 12px 0 0;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            padding: 1.5rem;
            border-bottom: 2px solid;
        }

        .invoices-section .section-header {
            border-bottom-color: #0ea5e9;
        }

        .employees-section .section-header {
            border-bottom-color: #10b981;
        }

        .financial-section .section-header {
            border-bottom-color: #8b5cf6;
        }

        .actions-section .section-header {
            border-bottom-color: #f59e0b;
        }
    </style>
@endpush

@section('content')
    <div class="container mx-auto py-8 px-4" dir="rtl">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">لوحة تحليل الفواتير والعمالة</h1>
                <p class="text-gray-600 font-light">
                    مرحباً بك، <span class="font-bold text-gray-800">{{ Auth::user()->name }}</span>
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="bg-gradient-to-l from-blue-50 to-cyan-50 p-4 rounded-xl shadow-sm border border-blue-100">
                    <p class="text-blue-800 font-medium">
                        <i class="fas fa-chart-line ml-2"></i>
                        إحصائيات حية - آخر تحديث: {{ now()->format('Y-m-d H:i') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Invoices Statistics -->
        <div class="invoices-section p-6 mb-8">
            <div class="section-header">
                <h2 class="section-title text-xl font-bold text-gray-800">
                    <i class="fas fa-file-invoice ml-2" style="color: #0ea5e9;"></i>
                    إحصائيات الفواتير
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- الصادرة -->
                <a href="{{ route('dashboard.reports.issued-invoices') }}" class="stat-card group">
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500 h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-medium">الفواتير الصادرة</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistics['issued_invoices'] }}</p>
                                <p class="text-sm text-gray-400 mt-1">من إجمالي {{ $statistics['total_invoices'] }}</p>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full group-hover:bg-green-200 transition-all">
                                <i class="fas fa-file-export text-green-600 text-xl stat-icon"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-green-600 text-sm font-medium">
                            <span>عرض التقرير</span>
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- الملغية -->
                <a href="{{ route('dashboard.reports.cancelled-invoices') }}" class="stat-card group">
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-red-500 h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-medium">الفواتير الملغية</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistics['cancelled_invoices'] }}</p>
                                <p class="text-sm text-gray-400 mt-1">{{ number_format(($statistics['cancelled_invoices'] / max($statistics['total_invoices'], 1)) * 100, 1) }}% من الإجمالي</p>
                            </div>
                            <div class="bg-red-100 p-3 rounded-full group-hover:bg-red-200 transition-all">
                                <i class="fas fa-times-circle text-red-600 text-xl stat-icon"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-red-600 text-sm font-medium">
                            <span>عرض التقرير</span>
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- المتأخرة -->
                <a href="{{ route('dashboard.reports.late-invoices') }}" class="stat-card group">
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500 h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-medium">الفواتير المتأخرة</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistics['late_invoices'] }}</p>
                                <div class="text-xs text-gray-500 mt-1 space-y-1">
                                    <div>تأخر سداد: {{ $statistics['late_payment_invoices'] }}</div>
                                    <div>تأخر إصدار: {{ $statistics['late_generation_invoices'] }}</div>
                                </div>
                            </div>
                            <div class="bg-orange-100 p-3 rounded-full group-hover:bg-orange-200 transition-all">
                                <i class="fas fa-clock text-orange-600 text-xl stat-icon"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-orange-600 text-sm font-medium">
                            <span>عرض التقرير</span>
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- إجمالي الفواتير -->
                <div class="stat-card">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-md p-6 text-white h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-medium opacity-90">إجمالي الفواتير</h3>
                                <p class="text-3xl font-bold mt-2">{{ $statistics['total_invoices'] }}</p>
                                <div class="text-sm opacity-80 mt-2 space-y-1">
                                    <div>مدفوعة: {{ $statistics['paid_invoices'] }}</div>
                                    <div>قيد الانتظار: {{ $statistics['pending_invoices'] }}</div>
                                </div>
                            </div>
                            <div class="bg-white bg-opacity-20 p-3 rounded-full">
                                <i class="fas fa-receipt text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Employees Statistics -->
        <div class="employees-section p-6 mb-8">
            <div class="section-header">
                <h2 class="section-title text-xl font-bold text-gray-800">
                    <i class="fas fa-users ml-2" style="color: #10b981;"></i>
                    إحصائيات العمالة
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- اليوزرات -->
                <a href="{{ route('dashboard.reports.users') }}" class="stat-card group">
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500 h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-medium">اليوزرات</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistics['users_count'] }}</p>
                                <p class="text-sm text-gray-400 mt-1">رواتب شهرية</p>
                            </div>
                            <div class="bg-purple-100 p-3 rounded-full group-hover:bg-purple-200 transition-all">
                                <i class="fas fa-user-tie text-purple-600 text-xl stat-icon"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-purple-600 text-sm font-medium">
                            <span>عرض التقرير</span>
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- العمال -->
                <a href="{{ route('dashboard.reports.workers') }}" class="stat-card group">
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-cyan-500 h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-medium">العمال</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistics['workers_count'] }}</p>
                                <p class="text-sm text-gray-400 mt-1">حماية أجور</p>
                            </div>
                            <div class="bg-cyan-100 p-3 rounded-full group-hover:bg-cyan-200 transition-all">
                                <i class="fas fa-hard-hat text-cyan-600 text-xl stat-icon"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-cyan-600 text-sm font-medium">
                            <span>عرض التقرير</span>
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- المشرفين -->
                <a href="{{ route('dashboard.reports.supervisors') }}" class="stat-card group">
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500 h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-medium">المشرفين</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistics['supervisors_count'] }}</p>
                                <p class="text-sm text-gray-400 mt-1">من إجمالي الموظفين</p>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full group-hover:bg-yellow-200 transition-all">
                                <i class="fas fa-user-shield text-yellow-600 text-xl stat-icon"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-yellow-600 text-sm font-medium">
                            <span>عرض التقرير</span>
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- المدراء -->
                <a href="{{ route('dashboard.reports.managers') }}" class="stat-card group">
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-indigo-500 h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-medium">المدراء</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistics['managers_count'] }}</p>
                                <p class="text-sm text-gray-400 mt-1">من إجمالي الموظفين</p>
                            </div>
                            <div class="bg-indigo-100 p-3 rounded-full group-hover:bg-indigo-200 transition-all">
                                <i class="fas fa-user-cog text-indigo-600 text-xl stat-icon"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-indigo-600 text-sm font-medium">
                            <span>عرض التقرير</span>
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Financial Differences -->
        <div class="financial-section p-6 mb-8">
            <div class="section-header">
                <h2 class="section-title text-xl font-bold text-gray-800">
                    <i class="fas fa-money-bill-wave ml-2" style="color: #8b5cf6;"></i>
                    الفروق المالية وأيام العمل
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- لصالحنا -->
                <a href="{{ route('dashboard.reports.financial-for-us') }}" class="stat-card group">
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-emerald-500 h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-medium">لصالحنا</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($statistics['financial_for_us'], 2) }} ﷼</p>
                                <p class="text-sm text-gray-400 mt-1">فروق إيجابية</p>
                            </div>
                            <div class="bg-emerald-100 p-3 rounded-full group-hover:bg-emerald-200 transition-all">
                                <i class="fas fa-arrow-up text-emerald-600 text-xl stat-icon"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-emerald-600 text-sm font-medium">
                            <span>عرض التقرير</span>
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- ضدنا -->
                <a href="{{ route('dashboard.reports.financial-against-us') }}" class="stat-card group">
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-rose-500 h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-medium">ضدنا</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ number_format($statistics['financial_against_us'], 2) }} ﷼</p>
                                <p class="text-sm text-gray-400 mt-1">فروق سلبية</p>
                            </div>
                            <div class="bg-rose-100 p-3 rounded-full group-hover:bg-rose-200 transition-all">
                                <i class="fas fa-arrow-down text-rose-600 text-xl stat-icon"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-rose-600 text-sm font-medium">
                            <span>عرض التقرير</span>
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>

                <!-- إجمالي أيام العمل -->
                <a href="{{ route('dashboard.reports.work-days') }}" class="stat-card group">
                    <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500 h-full">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-gray-500 font-medium">إجمالي أيام العمل</h3>
                                <p class="text-3xl font-bold text-gray-800 mt-2">{{ $statistics['total_work_days'] }}</p>
                                <p class="text-sm text-gray-400 mt-1">لجميع الموظفين</p>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full group-hover:bg-blue-200 transition-all">
                                <i class="fas fa-calendar-alt text-blue-600 text-xl stat-icon"></i>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center text-blue-600 text-sm font-medium">
                            <span>عرض التقرير</span>
                            <i class="fas fa-arrow-left mr-2 transform group-hover:-translate-x-1 transition-transform"></i>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="actions-section p-6">
            <div class="section-header">
                <h2 class="text-xl font-bold text-gray-800 mb-6">
                    <i class="fas fa-bolt ml-2" style="color: #f59e0b;"></i>
                    إجراءات سريعة
                </h2>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="{{ route('invoices.create') }}" class="group">
                    <div class="bg-white hover:bg-blue-50 border-l-4 border-blue-500 rounded-lg p-4 transition-all duration-300 h-full flex items-center shadow-md">
                        <div class="bg-blue-100 p-3 rounded-full mr-4 group-hover:bg-blue-200 transition-all">
                            <i class="fas fa-plus text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">فاتورة جديدة</h3>
                            <p class="text-sm text-gray-600">إنشاء فاتورة جديدة</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('employees.index') }}" class="group">
                    <div class="bg-white hover:bg-green-50 border-l-4 border-green-500 rounded-lg p-4 transition-all duration-300 h-full flex items-center shadow-md">
                        <div class="bg-green-100 p-3 rounded-full mr-4 group-hover:bg-green-200 transition-all">
                            <i class="fas fa-user-plus text-green-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">إضافة موظف</h3>
                            <p class="text-sm text-gray-600">إضافة موظف جديد</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('invoices.index') }}" class="group">
                    <div class="bg-white hover:bg-purple-50 border-l-4 border-purple-500 rounded-lg p-4 transition-all duration-300 h-full flex items-center shadow-md">
                        <div class="bg-purple-100 p-3 rounded-full mr-4 group-hover:bg-purple-200 transition-all">
                            <i class="fas fa-list text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">عرض الفواتير</h3>
                            <p class="text-sm text-gray-600">عرض جميع الفواتير</p>
                        </div>
                    </div>
                </a>

                <a href="#" class="group">
                    <div class="bg-white hover:bg-orange-50 border-l-4 border-orange-500 rounded-lg p-4 transition-all duration-300 h-full flex items-center shadow-md">
                        <div class="bg-orange-100 p-3 rounded-full mr-4 group-hover:bg-orange-200 transition-all">
                            <i class="fas fa-building text-orange-600"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">إدارة العملاء</h3>
                            <p class="text-sm text-gray-600">عرض وإدارة العملاء</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
