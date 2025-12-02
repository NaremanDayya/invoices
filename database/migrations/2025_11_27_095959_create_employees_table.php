<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone_number');
            $table->decimal('monthly_salary', 10, 2)->default(0);
            $table->decimal('wage_salary', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2)->default(0);
            $table->string('bank_name');
            $table->string('bank_account_number')->nullable();
            $table->string('iban');
            $table->integer('account_change_count')->default(0);
            $table->integer('work_days')->nullable();
            $table->enum('file_type', ['رواتب شهرية', 'حماية أجور']);
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number');
            $table->string('account_holder_name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
