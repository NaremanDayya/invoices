<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insertOrIgnore([
            'key'         => 'accepted_salary_delay_days',
            'value'       => '0',
            'type'        => 'integer',
            'description' => 'عدد أيام التأخير المسموح بها بعد نهاية الشهر قبل احتساب التأخير في صرف الرواتب',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'accepted_salary_delay_days')->delete();
    }
};
