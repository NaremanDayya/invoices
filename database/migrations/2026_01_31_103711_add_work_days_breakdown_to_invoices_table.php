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
        Schema::table('invoices', function (Blueprint $table) {
            $table->integer('workers_days')->default(0)->after('total_workers');
            $table->integer('supervisors_days')->default(0)->after('total_supervisors');
            $table->integer('managers_days')->default(0)->after('total_managers');
            $table->integer('users_days')->default(0)->after('total_users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['workers_days', 'supervisors_days', 'managers_days', 'users_days']);
        });
    }
};
