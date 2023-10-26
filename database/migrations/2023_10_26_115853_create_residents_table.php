<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('number')->unique();
            $table->enum('type', ['male', 'female'])->default('male');
            $table->enum('mental_disability_degree', ['simple', 'moderate', 'strong', 'deep']);
            $table->date('dob');
            $table->date('doe');
            $table->string('building');
            $table->boolean('ability_to_external_visit')->nullable()->default(null);
            $table->longText('external_visit_authorized')->nullable()->default(null);
            $table->longText('internal_visit_authorized')->nullable()->default(null);
            $table->longText('notes')->nullable()->default(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
