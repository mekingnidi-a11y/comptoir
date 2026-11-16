<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journees', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('gerant_id')->constrained('gerants')->cascadeOnDelete();
            $table->string('statut', 20)->default('ouverte');
            $table->unsignedInteger('fond_caisse')->default(0);
            $table->decimal('pourcentage_applique', 5, 2)->default(0);
            $table->unsignedBigInteger('recette')->default(0);
            $table->unsignedBigInteger('total_depenses')->default(0);
            $table->unsignedBigInteger('commission')->default(0);
            $table->bigInteger('benefice')->default(0);
            $table->boolean('commission_versee')->default(false);
            $table->timestamp('cloturee_at')->nullable();
            $table->foreignId('saisie_par_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['date', 'gerant_id']);
            $table->index(['statut', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journees');
    }
};
