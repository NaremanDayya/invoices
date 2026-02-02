<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    public function run()
    {
        $services = [
            [
                'name' => 'خدمات الموارد البشرية',
                'description' => 'توفير العمالة والموارد البشرية للشركات والمؤسسات',
                'service_type' => 'human_resource',
            ],
            [
                'name' => 'خدمات الأمن والحراسة',
                'description' => 'توفير خدمات الأمن والحراسة للمنشآت',
                'service_type' => 'security',
            ],
            [
                'name' => 'خدمات النظافة والصيانة',
                'description' => 'خدمات التنظيف والصيانة الدورية',
                'service_type' => 'cleaning',
            ],
            [
                'name' => 'الاستشارات الإدارية',
                'description' => 'تقديم الاستشارات الإدارية والتطويرية',
                'service_type' => 'consulting',
            ],
            [
                'name' => 'خدمات تقنية المعلومات',
                'description' => 'حلول تقنية المعلومات والدعم الفني',
                'service_type' => 'it_services',
            ],
            [
                'name' => 'التدريب والتطوير',
                'description' => 'برامج تدريبية وتطويرية للموظفين',
                'service_type' => 'training',
            ],
            [
                'name' => 'خدمات المقاولات',
                'description' => 'خدمات المقاولات والبناء',
                'service_type' => 'construction',
            ],
            [
                'name' => 'خدمات النقل واللوجستيات',
                'description' => 'حلول النقل والخدمات اللوجستية',
                'service_type' => 'logistics',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
