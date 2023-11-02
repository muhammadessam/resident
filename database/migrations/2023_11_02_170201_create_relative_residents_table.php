<?php

use App\Models\Relative;
use App\Models\Resident;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('relative_resident', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Resident::class, 'resident_id')->constrained('residents', 'id');
                $table->foreignIdFor(Relative::class, 'relative_id')->constrained('relatives', 'id');
            $table->string('relation');
            $table->boolean('is_guardian')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relative_resident');
    }
};
