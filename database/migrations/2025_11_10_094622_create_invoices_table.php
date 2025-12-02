<?php
// database/migrations/2024_01_01_create_invoices_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Basic Information
            $table->string('number')->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');

            // Dates
            $table->date('generation_date');
            $table->date('last_generation_date');
            $table->date('due_date');
            $table->integer('grace_period_days')->default(30);
            $table->date('approval_date')->nullable();
            $table->date('payment_date')->nullable();

            // Workforce Details
            $table->integer('total_workers')->default(0);
            $table->integer('total_supervisors')->default(0);
            $table->integer('total_managers')->default(0);
            $table->integer('total_users')->default(0);

            // Work Details
            $table->integer('work_days')->default(1);
            $table->decimal('daily_rate', 15, 2)->default(0);

            // Financial Details
            $table->decimal('base_price', 15, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(15);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_price', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('amount_difference', 15, 2)->default(0);
            $table->enum('difference_type', ['increase', 'decrease'])->nullable();

            // Status & Tracking
            $table->enum('payment_status', ['pending', 'paid', 'overdue', 'late'])->default('pending');
            $table->enum('invoice_status', ['draft', 'issued', 'cancelled', 'completed'])->default('draft');
            $table->boolean('is_cancelled')->default(false);
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Delay & Difference Tracking
            $table->integer('issue_delay_days')->default(0);
            $table->integer('payment_delay_days')->default(0);
            $table->integer('employee_count_difference')->default(0);
            $table->integer('work_days_difference')->default(0);
            $table->enum('difference_indicator', ['green_up', 'red_down'])->nullable();

            // Credit Notes
            $table->integer('credit_notes_count')->default(0);
            $table->decimal('total_credit_notes', 15, 2)->default(0);

            // Service Type
            $table->string('service_type')->default('general');
            $table->boolean('requires_hr_details')->default(false);

            // Additional Fields
            $table->text('notes')->nullable();
            $table->json('additional_data')->nullable(); // For flexible data storage

            // Timestamps
            $table->timestamps();
            $table->softDeletes();

        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
};
