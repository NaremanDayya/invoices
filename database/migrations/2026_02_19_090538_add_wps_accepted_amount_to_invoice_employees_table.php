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
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->decimal('wps_accepted_amount', 12, 2)->default(0)->after('wps_amount')
                ->comment('Remaining allowed WPS amount for this employee. Decremented on each WPS payment.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoice_employees', function (Blueprint $table) {
            $table->dropColumn('wps_accepted_amount');
        });
    }
};
