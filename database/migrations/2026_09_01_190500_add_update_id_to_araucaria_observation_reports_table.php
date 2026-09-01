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
        if (! Schema::hasColumn('araucaria_observation_reports', 'araucaria_observation_update_id')) {
            Schema::table('araucaria_observation_reports', function (Blueprint $table) {
                $table->foreignId('araucaria_observation_update_id')
                    ->nullable()
                    ->after('araucaria_observation_id')
                    ->constrained('araucaria_observation_updates', 'id', 'fk_reports_obs_update')
                    ->cascadeOnDelete();
            });
        } else {
            Schema::table('araucaria_observation_reports', function (Blueprint $table) {
                $table->foreign('araucaria_observation_update_id', 'fk_reports_obs_update')
                    ->references('id')
                    ->on('araucaria_observation_updates')
                    ->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('araucaria_observation_reports', function (Blueprint $table) {
            $table->dropForeign('fk_reports_obs_update');
            $table->dropColumn('araucaria_observation_update_id');
        });
    }
};
