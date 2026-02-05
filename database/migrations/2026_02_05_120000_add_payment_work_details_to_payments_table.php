<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->integer('employees_count')->nullable()->after('amount');
            $table->integer('work_days')->nullable()->after('employees_count');
            $table->integer('late_days')->default(0)->after('payment_date');
        });
    }

    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['employees_count', 'work_days', 'late_days']);
        });
    }
};
