<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoice_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('work_days')->default(0);
            $table->decimal('daily_rate', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->integer('absence_days')->default(0);
            $table->decimal('absence_deduction', 15, 2)->default(0);
            $table->json('deductions')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

        });
    }

    public function down()
    {
        Schema::dropIfExists('invoice_employees');
    }
};
