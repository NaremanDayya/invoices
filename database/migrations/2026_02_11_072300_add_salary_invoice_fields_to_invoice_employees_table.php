<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->string('employee_name')->nullable();
            $table->string('project')->nullable();
            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->decimal('bonuses', 15, 2)->default(0);
            $table->decimal('monthly_deductions', 15, 2)->default(0);
            $table->decimal('advance_deductions', 15, 2)->default(0);
            $table->integer('work_days_count')->default(0);
            $table->integer('absence_days_count')->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->string('iban')->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->enum('payment_method', ['wps', 'monthly'])->default('monthly');
            $table->decimal('wps_percentage', 5, 2)->nullable();
            $table->decimal('wps_amount', 15, 2)->nullable();
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid'])->default('unpaid');
            $table->date('payment_date')->nullable();
            $table->decimal('paid_amount', 15, 2)->default(0);
        });
        
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->unsignedBigInteger('employee_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->dropColumn([
                'employee_name',
                'project',
                'basic_salary',
                'bonuses',
                'monthly_deductions',
                'advance_deductions',
                'work_days_count',
                'absence_days_count',
                'net_salary',
                'iban',
                'account_holder_name',
                'bank_name',
                'payment_method',
                'wps_percentage',
                'wps_amount',
                'payment_status',
                'payment_date',
                'paid_amount'
            ]);
        });
        
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
        });
    }
};
