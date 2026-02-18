<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate existing messages to chat_receivers table
        // For each message, create a chat_receiver entry for the receiver_id if it exists
        DB::statement("
            INSERT INTO chat_receivers (message_id, receiver_id, is_read, read_at, created_at, updated_at)
            SELECT 
                id as message_id,
                receiver_id,
                CASE WHEN read_at IS NOT NULL THEN 1 ELSE 0 END as is_read,
                read_at,
                created_at,
                updated_at
            FROM messages
            WHERE receiver_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear chat_receivers table
        DB::table('chat_receivers')->truncate();
    }
};
