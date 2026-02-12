<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_employee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_employee_id')->constrained('invoice_employees')->onDelete('cascade');
            $table->foreignId('invoice_id')->constrained('invoices')->onDelete('cascade');
            $table->decimal('payment_amount', 15, 2);
            $table->enum('payment_type', ['full', 'partial']);
            $table->enum('payment_mode', ['monthly', 'wps']);
            $table->date('payment_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['invoice_employee_id', 'payment_date']);
            $table->index('invoice_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_employee_payments');
    }
};
