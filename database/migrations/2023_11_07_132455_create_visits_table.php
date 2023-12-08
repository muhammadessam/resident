<?php

use App\Models\Relative;
use App\Models\Resident;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Resident::class, 'resident_id')->constrained('residents', 'id');
            $table->foreignIdFor(Relative::class, 'relative_id')->constrained('relatives', 'id');
            $table->enum('type', ['internal', 'external']);
            $table->enum('duration_type', ['days', 'hours'])->nullable()->default(null);
            $table->unsignedTinyInteger('duration')->nullable()->default(null);
            $table->unsignedTinyInteger('companion_no');
            $table->timestamp('date_time');
            $table->timestamp('end_date')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
