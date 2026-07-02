<?php

namespace Tests\Feature\Api;

use App\Models\AraucariaObservation;
use App\Models\AraucariaObservationReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AraucariaObservationTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    /**
     * Testa se é possível obter os detalhes de uma observação via API.
     */
    public function test_can_get_a_single_observation(): void
    {
        // 1. Preparação
        // Criamos um usuário e uma observação no banco de dados para testar:
        $user = \App\Models\User::factory()->create();

        $observation = \App\Models\AraucariaObservation::create([
            'user_id' => $user->id,
            'latitude' => -25.4284,
            'longitude' => -49.2733,
            'photo_path' => 'photos/test.jpg',
            'stage' => 'adult',
            'gender' => 'female',
            'observed_at' => now()
        ]);

        // 2. Ação
        // Fazemos a requisição GET para a rota com o ID da observação criada:
        $response = $this->getJson('/api/observations/' . $observation->id);

        // 3. Validação
        // Verificamos se a resposta teve sucesso (200 OK):
        $response->assertStatus(200);

        // E se o conteúdo JSON contém os dados corretos:
        $response->assertJsonPath('data.id', $observation->id);
        $response->assertJsonPath('data.latitude', -25.4284);
        $response->assertJsonPath('data.stage', 'adult');
        $response->assertJsonPath('data.gender', 'female');
    }

    public function test_user_can_report_an_observation_with_reason_and_details(): void
    {
        $owner = User::factory()->create();
        $reporter = User::factory()->create();
        $observation = AraucariaObservation::create([
            'user_id' => $owner->id,
            'latitude' => -25.4284,
            'longitude' => -49.2733,
            'photo_path' => 'photos/test.jpg',
            'stage' => 'adult',
            'gender' => 'female',
            'observed_at' => now()
        ]);

        $response = $this->actingAs($reporter)->postJson(route('observations.report', $observation), [
            'reason' => 'inappropriate_image',
            'details' => 'Imagem não parece ser uma araucária.',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('message', 'Observação denunciada com sucesso!');
        $this->assertDatabaseHas('araucaria_observation_reports', [
            'araucaria_observation_id' => $observation->id,
            'user_id' => $reporter->id,
            'reason' => 'inappropriate_image',
            'details' => 'Imagem não parece ser uma araucária.',
            'status' => 'pending',
        ]);
    }

    public function test_moderation_panel_can_delete_an_observation_and_assign_it_to_another_user(): void
    {
        $owner = User::factory()->create();
        $reporter = User::factory()->create();
        $moderator = User::factory()->create(['role' => 'admin']);
        $newOwner = User::factory()->create();

        $observation = AraucariaObservation::create([
            'user_id' => $owner->id,
            'latitude' => -25.4284,
            'longitude' => -49.2733,
            'photo_path' => 'photos/test.jpg',
            'stage' => 'adult',
            'gender' => 'female',
            'observed_at' => now()
        ]);

        $report = AraucariaObservationReport::create([
            'araucaria_observation_id' => $observation->id,
            'user_id' => $reporter->id,
            'reason' => 'ownership',
            'details' => 'A foto é de outra pessoa.',
            'status' => 'pending',
        ]);

        $this->actingAs($moderator)->get(route('observations.moderation.index'))->assertOk();

        $this->actingAs($moderator)->post(route('observations.moderation.delete', $report))->assertRedirect();
        $this->assertDatabaseMissing('araucaria_observations', ['id' => $observation->id]);

        $observation = AraucariaObservation::create([
            'user_id' => $owner->id,
            'latitude' => -25.4284,
            'longitude' => -49.2733,
            'photo_path' => 'photos/test.jpg',
            'stage' => 'adult',
            'gender' => 'female',
            'observed_at' => now()
        ]);

        $report = AraucariaObservationReport::create([
            'araucaria_observation_id' => $observation->id,
            'user_id' => $reporter->id,
            'reason' => 'ownership',
            'details' => 'A foto é de outra pessoa.',
            'status' => 'pending',
        ]);

        $this->actingAs($moderator)->post(route('observations.moderation.assign', $report), [
            'user_id' => $newOwner->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('araucaria_observations', [
            'id' => $observation->id,
            'user_id' => $newOwner->id,
        ]);
    }
}