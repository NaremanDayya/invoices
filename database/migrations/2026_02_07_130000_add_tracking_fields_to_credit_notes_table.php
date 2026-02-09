<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('credit_notes', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('invoice_id')->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('credit_notes', 'credit_note_number')) {
                $table->string('credit_note_number')->nullable()->after('number');
            }
            
            if (!Schema::hasColumn('credit_notes', 'type')) {
                $table->enum('type', ['internal', 'client'])->default('internal')->after('credit_note_number');
            }
            
            if (!Schema::hasColumn('credit_notes', 'previous_values')) {
                $table->json('previous_values')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('credit_notes', 'new_values')) {
                $table->json('new_values')->nullable()->after('previous_values');
            }
            
            if (!Schema::hasColumn('credit_notes', 'amount_difference')) {
                $table->decimal('amount_difference', 15, 2)->default(0)->after('new_values');
            }
            
            if (!Schema::hasColumn('credit_notes', 'previous_total')) {
                $table->decimal('previous_total', 15, 2)->default(0)->after('amount_difference');
            }
            
            if (!Schema::hasColumn('credit_notes', 'new_total')) {
                $table->decimal('new_total', 15, 2)->default(0)->after('previous_total');
            }
            
            if (!Schema::hasColumn('credit_notes', 'notes')) {
                $table->text('notes')->nullable()->after('reason');
            }
        });
    }

    public function down()
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'created_by',
                'credit_note_number',
                'type',
                'previous_values',
                'new_values',
                'amount_difference',
                'previous_total',
                'new_total',
                'notes'
            ]);
        });
    }
};
