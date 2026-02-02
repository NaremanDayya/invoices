<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientsTableSeeder extends Seeder
{
    public function run()
    {
        $clients = [
            // Client with multiple invoices and payments
            [
                'name' => 'شركة النور للمقاولات',
                'email' => 'info@alnoor.com',
                'phone' => '+966501234567',
                'address' => 'الرياض، حي الملك فهد، شارع العليا',
                'default_payment_day' => 25,
                'grace_period_days' => 30,
            ],
            // Client with overdue invoices
            [
                'name' => 'مؤسسة الفجر التجارية',
                'email' => 'contact@alfajr.com',
                'phone' => '+966502345678',
                'address' => 'جدة، حي الروضة، طريق الملك عبدالعزيز',
                'default_payment_day' => 15,
                'grace_period_days' => 45,
            ],
            // Client with credit notes
            [
                'name' => 'شركة البناء الحديث',
                'email' => 'admin@modernbuild.sa',
                'phone' => '+966503456789',
                'address' => 'الدمام، حي الفيصلية، شارع الأمير محمد',
                'default_payment_day' => 30,
                'grace_period_days' => 60,
            ],
            // Client with partial payments
            [
                'name' => 'مجموعة الأمل للاستثمار',
                'email' => 'info@alamal-group.sa',
                'phone' => '+966504567890',
                'address' => 'مكة المكرمة، حي العزيزية، شارع الحج',
                'default_payment_day' => 20,
                'grace_period_days' => 30,
            ],
            // Client with cancelled invoices
            [
                'name' => 'شركة التطوير العقاري',
                'email' => 'support@realestate-dev.com',
                'phone' => '+966505678901',
                'address' => 'المدينة المنورة، حي العيون، طريق الملك فيصل',
                'default_payment_day' => 10,
                'grace_period_days' => 30,
            ],
            // Client with fully paid invoices
            [
                'name' => 'مؤسسة الخليج للتجارة',
                'email' => 'info@gulf-trade.sa',
                'phone' => '+966506789012',
                'address' => 'الخبر، حي الثقبة، شارع الأمير تركي',
                'default_payment_day' => 28,
                'grace_period_days' => 30,
            ],
            // Client with mixed payment statuses
            [
                'name' => 'شركة الرواد للخدمات',
                'email' => 'contact@alrawad.com',
                'phone' => '+966507890123',
                'address' => 'أبها، حي المنسك، طريق الملك عبدالله',
                'default_payment_day' => 5,
                'grace_period_days' => 15,
            ],
            // Client with large invoices
            [
                'name' => 'مجموعة النجاح التجارية',
                'email' => 'admin@success-group.sa',
                'phone' => '+966508901234',
                'address' => 'تبوك، حي السليمانية، شارع الملك خالد',
                'default_payment_day' => 15,
                'grace_period_days' => 30,
            ],
            // Client with small invoices
            [
                'name' => 'مؤسسة الشروق للمقاولات',
                'email' => 'info@alshorouk.com',
                'phone' => '+966509012345',
                'address' => 'الطائف، حي الفيصلية، شارع الجيش',
                'default_payment_day' => 30,
                'grace_period_days' => 45,
            ],
            // Client with recent activity
            [
                'name' => 'شركة الإبداع للتطوير',
                'email' => 'contact@ibdaa.sa',
                'phone' => '+966500123456',
                'address' => 'بريدة، حي الصفراء، طريق الملك فهد',
                'default_payment_day' => 25,
                'grace_period_days' => 30,
            ],
        ];

        foreach ($clients as $client) {
            Client::create($client);
        }
    }
}
