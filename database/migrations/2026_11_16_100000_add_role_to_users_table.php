<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('proprietaire')->after('password');
            $table->foreignId('gerant_id')->nullable()->after('role')->constrained('gerants')->nullOnDelete();
            $table->boolean('actif')->default(true)->after('gerant_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('gerant_id');
            $table->dropColumn(['role', 'actif']);
        });
    }
};
