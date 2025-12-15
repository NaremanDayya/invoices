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
        Schema::create('conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->uuid('conversation_id');
            $table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();

            $table->unique(['conversation_id', 'user_id']);
        });

        Schema::create('message_mentions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // The user mentioned
            $table->timestamps();
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->string('type')->default('private')->after('id'); // private, group, invoice
            $table->string('label')->nullable()->after('type'); // For group names
        });

        // Migrate existing 1-on-1 conversations to participants
        $conversations = DB::table('conversations')->get();
        foreach ($conversations as $conversation) {
            $participants = [];
            
            if ($conversation->sender_id) {
                $participants[] = [
                    'conversation_id' => $conversation->id,
                    'user_id' => $conversation->sender_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if ($conversation->receiver_id && $conversation->receiver_id != $conversation->sender_id) {
                $participants[] = [
                    'conversation_id' => $conversation->id,
                    'user_id' => $conversation->receiver_id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($participants)) {
                DB::table('conversation_participants')->insert($participants);
            }
            
            // Tag invoice conversations
            if (!empty($conversation->invoice_id)) {
                 DB::table('conversations')
                    ->where('id', $conversation->id)
                    ->update(['type' => 'invoice']);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_mentions');
        Schema::dropIfExists('conversation_participants');
        
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn(['type', 'label']);
        });
    }
};
