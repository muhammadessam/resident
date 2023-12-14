<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            if (!Schema::hasColumn('residents', 'is_out_to_hospital')) {
                $table->boolean('is_out_to_hospital')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            if (Schema::hasColumn('residents', 'is_out_to_hospital')) {
                $table->dropColumn('is_out_to_hospital');
            }
        });
    }
};
