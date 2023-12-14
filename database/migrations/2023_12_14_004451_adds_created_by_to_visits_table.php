<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            if (!Schema::hasColumn('visits', 'created_by')) {
                $table->foreignIdFor(User::class, 'created_by')->nullable()->default(null)->constrained('users', 'id')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropForeignIdFor(User::class, 'created_by');
            $table->dropColumn('created_by');
        });
    }
};
