<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->enum('salary_pay_status', ['full_paid', 'partial_paid', 'pended'])
                  ->nullable()
                  ->after('payment_status');
        });
    }

    public function down(): void
    {
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->dropColumn('salary_pay_status');
        });
    }
};
