<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('client_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('update_type')->default('general');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->date('update_date');
            $table->string('status')->default('active');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['invoice_id', 'update_date']);
            $table->index(['payment_id', 'update_date']);
            $table->index(['client_id', 'update_date']);
            $table->index('update_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_updates');
    }
};
