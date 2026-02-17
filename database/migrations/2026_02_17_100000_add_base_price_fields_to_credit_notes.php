<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('credit_notes', 'previous_base_price')) {
                $table->decimal('previous_base_price', 15, 2)->default(0)->after('previous_total');
            }
            
            if (!Schema::hasColumn('credit_notes', 'new_base_price')) {
                $table->decimal('new_base_price', 15, 2)->default(0)->after('new_total');
            }
        });
    }

    public function down()
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->dropColumn(['previous_base_price', 'new_base_price']);
        });
    }
};
