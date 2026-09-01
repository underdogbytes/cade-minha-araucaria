<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('araucaria_observation_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('araucaria_observation_id')->constrained('araucaria_observations')->cascadeOnDelete();
            $table->longText('photo_path');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        // Copia fotos já existentes na tabela araucaria_observations para a nova tabela de fotos
        $observations = DB::table('araucaria_observations')->whereNotNull('photo_path')->get();
        foreach ($observations as $obs) {
            if ($obs->photo_path) {
                DB::table('araucaria_observation_photos')->insert([
                    'araucaria_observation_id' => $obs->id,
                    'photo_path' => $obs->photo_path,
                    'is_primary' => true,
                    'created_at' => $obs->created_at ?? now(),
                    'updated_at' => $obs->updated_at ?? now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('araucaria_observation_photos');
    }
};
