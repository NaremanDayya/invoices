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
        Schema::table('conversations', function (Blueprint $table) {
            // Drop FK first to be safe, though ->change() might handle it depending on DB
            $table->dropForeign(['receiver_id']);
            $table->unsignedBigInteger('receiver_id')->nullable()->change();
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['receiver_id']);
            $table->unsignedBigInteger('receiver_id')->nullable()->change();
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
             // We can't easily reverse if there are nulls now, but we can try
             // Typically we leaving it nullable is fine or we delete nullable rows
             $table->dropForeign(['receiver_id']);
             $table->unsignedBigInteger('receiver_id')->nullable(false)->change();
             $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('messages', function (Blueprint $table) {
             $table->dropForeign(['receiver_id']);
             $table->unsignedBigInteger('receiver_id')->nullable(false)->change();
             $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
