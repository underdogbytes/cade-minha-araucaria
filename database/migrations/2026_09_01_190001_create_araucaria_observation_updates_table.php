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
        Schema::create('araucaria_observation_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('araucaria_observation_id')->constrained('araucaria_observations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->longText('photo_path');
            $table->text('notes')->nullable();
            $table->string('stage')->nullable();
            $table->dateTime('observed_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('araucaria_observation_updates');
    }
};
