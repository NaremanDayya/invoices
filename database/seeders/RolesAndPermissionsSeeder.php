<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'give_permissions_to_roles',
                'display_name_ar' => 'إدارة الصلاحيات والأدوار',
                'description' => 'القدرة على منح وإزالة الصلاحيات من الأدوار'
            ],
            [
                'name' => 'import_invoice_employees',
                'display_name_ar' => 'استيراد موظفي الفواتير',
                'description' => 'القدرة على استيراد بيانات الموظفين للفواتير'
            ],
            [
                'name' => 'approve_invoice_employees',
                'display_name_ar' => 'الموافقة على موظفي الفواتير',
                'description' => 'القدرة على الموافقة على بيانات موظفي الفواتير'
            ],
            [
                'name' => 'add_invoices',
                'display_name_ar' => 'إضافة الفواتير',
                'description' => 'القدرة على إنشاء فواتير جديدة'
            ],
            [
                'name' => 'add_clients',
                'display_name_ar' => 'إضافة العملاء',
                'description' => 'القدرة على إضافة عملاء جدد'
            ],
            [
                'name' => 'add_credit_note',
                'display_name_ar' => 'إضافة إشعارات دائنة',
                'description' => 'القدرة على إنشاء إشعارات دائنة'
            ],
            [
                'name' => 'add_invoice_payment',
                'display_name_ar' => 'إضافة دفعات الفواتير',
                'description' => 'القدرة على إضافة دفعات للفواتير'
            ],
            [
                'name' => 'add_invoice_employee_payment',
                'display_name_ar' => 'إضافة دفعات رواتب الموظفين',
                'description' => 'القدرة على إضافة دفعات رواتب للموظفين'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'display_name_ar' => $permission['display_name_ar'],
                    'description' => $permission['description']
                ]
            );
        }

        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'مدير النظام',
                'description' => 'لديه صلاحيات كاملة على النظام'
            ],
            [
                'name' => 'accountant',
                'display_name' => 'محاسب',
                'description' => 'مسؤول عن العمليات المحاسبية'
            ],
            [
                'name' => 'admin_assistant',
                'display_name' => 'مساعد إداري',
                'description' => 'يساعد في المهام الإدارية'
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name']],
                [
                    'display_name' => $roleData['display_name'],
                    'description' => $roleData['description']
                ]
            );

            if ($role->name === 'admin') {
                $allPermissions = Permission::all();
                $role->syncPermissions($allPermissions->pluck('id')->toArray());
            }
        }
    }
}
