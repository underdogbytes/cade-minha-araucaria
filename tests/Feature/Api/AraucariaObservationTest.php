<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\RateLimiter;
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

    public function test_api_requests_are_rate_limited_after_excessive_attempts(): void
    {
        RateLimiter::clear('api');

        for ($attempt = 0; $attempt < 61; $attempt++) {
            $response = $this->getJson('/api/observations');

            if ($attempt === 60) {
                $response->assertStatus(429);
                $response->assertJsonPath('message', 'Muitas requisições. Tente novamente em alguns instantes.');
                return;
            }
        }

        $this->fail('A API não retornou 429 após exceder o limite de requisições.');
    }
}
