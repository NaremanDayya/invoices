<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_revision_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->enum('revision_status', ['requested', 'rejected', 'completed', 'approved'])->default('requested');
            $table->text('revision_notes')->nullable();
            $table->foreignId('revised_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index('invoice_id');
            $table->index('revision_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_revision_statuses');
    }
};
