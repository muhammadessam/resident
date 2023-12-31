<?php

use App\Models\Resident;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resident_resident', function (Blueprint $table) {
            $table->foreignIdFor(Resident::class, 'resident_id')->constrained('residents', 'id')->cascadeOnDelete();
            $table->foreignIdFor(Resident::class, 'relative_id')->constrained('residents', 'id')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_resident');
    }
};
