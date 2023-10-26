<?php

use App\Models\Resident;
use App\Models\SubHealthProblem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('resident_sub_health_problem', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Resident::class, 'resident_id')->constrained('residents', 'id');
            $table->foreignIdFor(SubHealthProblem::class, 'sub_health_problem_id')->constrained('sub_health_problems', 'id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_sub_health_problem');
    }
};
