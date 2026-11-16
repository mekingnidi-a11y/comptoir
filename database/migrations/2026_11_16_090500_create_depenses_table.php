<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journee_id')->constrained('journees')->cascadeOnDelete();
            $table->foreignId('categorie_depense_id')->nullable()->constrained('categorie_depenses')->nullOnDelete();
            $table->string('libelle');
            $table->unsignedBigInteger('montant');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('depenses');
    }
};
