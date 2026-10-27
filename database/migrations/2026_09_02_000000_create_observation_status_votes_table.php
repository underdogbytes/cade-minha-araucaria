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
        // Adiciona contadores e status computado na tabela araucaria_observations
        Schema::table('araucaria_observations', function (Blueprint $table) {
            $table->unsignedInteger('votes_normal_count')->default(0)->after('is_shared');
            $table->unsignedInteger('votes_doente_count')->default(0)->after('votes_normal_count');
            $table->unsignedInteger('votes_risco_count')->default(0)->after('votes_doente_count');
            $table->unsignedInteger('votes_protegida_count')->default(0)->after('votes_risco_count');
            $table->unsignedInteger('votes_morta_count')->default(0)->after('votes_protegida_count');
            $table->string('computed_status', 64)->default('normal')->after('votes_morta_count');
        });

        // Tabela de votos rápidos da comunidade (1 voto por usuário por categoria)
        Schema::create('observation_status_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('araucaria_observation_id')
                ->constrained('araucaria_observations')
                ->cascadeOnDelete();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('status', 32); // normal, doente, risco, protegida, morta
            $table->timestamps();

            $table->unique(['araucaria_observation_id', 'user_id', 'status'], 'obs_user_status_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observation_status_votes');

        Schema::table('araucaria_observations', function (Blueprint $table) {
            $table->dropColumn([
                'votes_normal_count',
                'votes_doente_count',
                'votes_risco_count',
                'votes_protegida_count',
                'votes_morta_count',
                'computed_status',
            ]);
        });
    }
};
