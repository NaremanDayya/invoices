<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->decimal('monthly_amount', 15, 2)->default(0)->after('wps_amount');
            $table->decimal('wps_percentage_applied', 5, 2)->nullable()->after('monthly_amount');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->dropColumn(['monthly_amount', 'wps_percentage_applied']);
        });
    }
};
