<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAraucariaObservationRequest;
use App\Http\Requests\UpdateAraucariaObservationRequest;
use App\Http\Resources\AraucariaObservationResource;
use App\Models\AraucariaObservation;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AraucariaObservationController extends Controller
{
    /**
     * Retorna todas as observações cadastradas com paginação.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(
            (int) $request->query('per_page', 15),
            100
        );

        $observations = AraucariaObservation::with(['user', 'photos'])
            ->latest()
            ->paginate($perPage);

        return AraucariaObservationResource::collection($observations);
    }

    /**
     * Retorna os detalhes de uma observação específica.
     */
    public function show(AraucariaObservation $observation): AraucariaObservationResource
    {
        return new AraucariaObservationResource($observation->load(['user', 'photos']));
    }

    /**
     * Armazena uma nova observação.
     */
    public function store(
        StoreAraucariaObservationRequest $request
    ): JsonResponse {

        $validated = $request->validated();
        $validated['observed_at'] = $validated['observed_at'] ?? now();

        DB::beginTransaction();

        try {

            $files = [];
            if ($request->hasFile('photos')) {
                $rawFiles = $request->file('photos');
                $files = is_array($rawFiles) ? $rawFiles : [$rawFiles];
            }
            if ($request->hasFile('photo_path')) {
                $files[] = $request->file('photo_path');
            }

            $processedPhotos = [];
            foreach ($files as $index => $file) {
                $processedPhotos[] = [
                    'path' => $this->processImage($file),
                    'is_primary' => $index === 0,
                ];
            }

            if (!empty($processedPhotos)) {
                $validated['photo_path'] = $processedPhotos[0]['path'];
            } else {
                $validated['photo_path'] = '';
            }

            unset($validated['photos']);

            $observation = $request
                ->user()
                ->araucariaObservations()
                ->create($validated);

            foreach ($processedPhotos as $photoData) {
                $observation->photos()->create([
                    'photo_path' => $photoData['path'],
                    'is_primary' => $photoData['is_primary'],
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Observação de Araucária registrada com sucesso!',
                'data' => new AraucariaObservationResource($observation->load('photos')),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();

            report($e);

            return response()->json([
                'message' => 'Erro ao registrar observação.',
            ], 500);
        }
    }

    /**
     * Atualiza uma observação existente.
     */
    public function update(
        UpdateAraucariaObservationRequest $request,
        AraucariaObservation $observation
    ): JsonResponse {

        if ($observation->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Esta ação não é autorizada.',
            ], 403);
        }

        $validated = $request->validated();

        DB::beginTransaction();

        try {

            $files = [];
            if ($request->hasFile('photos')) {
                $rawFiles = $request->file('photos');
                $files = is_array($rawFiles) ? $rawFiles : [$rawFiles];
            }
            if ($request->hasFile('photo_path')) {
                $files[] = $request->file('photo_path');
            }

            if (!empty($files)) {
                $processedPhotos = [];
                foreach ($files as $index => $file) {
                    $processedPhotos[] = [
                        'path' => $this->processImage($file),
                        'is_primary' => $index === 0,
                    ];
                }

                $validated['photo_path'] = $processedPhotos[0]['path'];

                $observation->photos()->delete();
                foreach ($processedPhotos as $photoData) {
                    $observation->photos()->create([
                        'photo_path' => $photoData['path'],
                        'is_primary' => $photoData['is_primary'],
                    ]);
                }
            }

            unset($validated['photos']);

            $observation->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Observação atualizada com sucesso!',
                'data' => new AraucariaObservationResource(
                    $observation->fresh(['photos', 'user'])
                ),
            ]);

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return response()->json([
                'message' => 'Erro ao atualizar observação.',
            ], 500);
        }
    }

    /**
     * Remove uma observação.
     */
    public function destroy(
        Request $request,
        AraucariaObservation $observation
    ): JsonResponse {

        if ($observation->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Esta ação não é autorizada.',
            ], 403);
        }

        try {

            if ($observation->photo_path) {
                Storage::disk('public')->delete(
                    $observation->photo_path
                );
            }

            $observation->delete();

            return response()->json([
                'message' => 'Observação excluída com sucesso!',
            ]);

        } catch (\Throwable $e) {

            report($e);

            return response()->json([
                'message' => 'Erro ao excluir observação.',
            ], 500);
        }
    }

   /**
     * Processa, otimiza e converte a imagem para salvar no Banco de Dados (Base64).
     */
    private function processImage(
        \Illuminate\Http\UploadedFile $file
    ): string {

        // 1. Inicializa o gerenciador da Versão 3
        $manager = new \Intervention\Image\ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver()
        );

        // 2. Lê a imagem enviada
        $image = $manager->read($file);

        // 3. Redimensiona proporcionalmente para manter o banco leve
        $image->scaleDown(
            width: 1200,
            height: 1200
        );

        // 4. Codifica como JPEG com 80% de qualidade
        $encoded = $image->toJpeg(80);

        // 5. Transforma os dados binários da imagem em uma string Base64 limpa
        $base64String = base64_encode((string) $encoded);

        // 6. Retorna o Data URL formatado para o banco de dados
        return 'data:image/jpeg;base64,' . $base64String;
    }
}
