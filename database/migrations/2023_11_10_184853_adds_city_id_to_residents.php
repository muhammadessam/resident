<?php

use App\Models\City;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->foreignIdFor(City::class, 'city_id')->nullable()->default(null)->constrained('cities', 'id');
        });
    }

    public function down(): void
    {
        Schema::table('residents', function (Blueprint $table) {
            $table->dropForeignIdFor(City::class, 'city_id');
            $table->dropColumn('city_id');
        });
    }
};
