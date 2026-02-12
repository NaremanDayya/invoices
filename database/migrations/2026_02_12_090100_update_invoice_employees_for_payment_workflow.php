<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->decimal('total_salary', 15, 2)->default(0)->after('net_salary');
            $table->decimal('total_paid', 15, 2)->default(0)->after('total_salary');
            $table->decimal('remaining_amount', 15, 2)->default(0)->after('total_paid');
            $table->enum('salary_type', ['monthly', 'wps'])->default('monthly')->after('payment_method');
            $table->date('last_payment_date')->nullable()->after('payment_date');
        });

        DB::statement("UPDATE invoice_employees SET total_salary = net_salary, total_paid = paid_amount, remaining_amount = (net_salary - paid_amount) WHERE net_salary > 0");
    }

    public function down(): void
    {
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->dropColumn(['total_salary', 'total_paid', 'remaining_amount', 'salary_type', 'last_payment_date']);
        });
    }
};
