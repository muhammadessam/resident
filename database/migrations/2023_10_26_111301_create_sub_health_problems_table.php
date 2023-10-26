<?php

use App\Models\MainHealthProblem;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sub_health_problems', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(MainHealthProblem::class, 'main_health_problem_id')
                ->nullable()->constrained('main_health_problems', 'id')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_health_problems');
    }
};
