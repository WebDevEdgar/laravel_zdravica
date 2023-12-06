<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('PATIENTS', static function (Blueprint $table){
            $table->id();
            $table->string('NOM');
            $table->string('PRENOM');
            $table->string('PATRONYME');
            $table->string('EMAIL');
            $table->string('POL');
            $table->string('GOROD');
            $table->string('NE_LE');
            $table->string('RAYON_VYBORKA');
            $table->string('ULICA');
            $table->string('DOM');
            $table->string('KVARTIRA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('PATIENTS');
    }
};
