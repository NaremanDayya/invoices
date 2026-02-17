<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('revision_status', ['pending', 'revision_requested', 'revision_completed', 'approved'])->default('pending')->after('approval_status');
            $table->text('revision_notes')->nullable()->after('revision_status');
            $table->foreignId('revision_requested_by')->nullable()->constrained('users')->onDelete('set null')->after('revision_notes');
            $table->timestamp('revision_requested_at')->nullable()->after('revision_requested_by');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['revision_requested_by']);
            $table->dropColumn(['revision_status', 'revision_notes', 'revision_requested_by', 'revision_requested_at']);
        });
    }
};
