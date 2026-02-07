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
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('invoice_id')->nullable()->after('conversation_id')->constrained()->onDelete('cascade');
            $table->enum('message_type', ['text', 'invoice_info', 'system', 'image'])->default('text')->after('message');
            $table->json('metadata')->nullable()->after('message_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropColumn(['invoice_id', 'message_type', 'metadata']);
        });
    }
};
