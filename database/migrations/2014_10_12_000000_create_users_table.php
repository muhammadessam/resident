<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('user_name')->nullable()->default(null);
            $table->enum('default_screen', ['MALE_SCREEN', 'FEMALE_SCREEN'])->nullable()->default(null);
            $table->boolean('is_super_admin')->default(false);
            $table->json('permissions')->nullable()->default(null);
            $table->string('email')->unique()->nullable()->default(null);
            $table->string('password')->nullable()->default(null);

            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
