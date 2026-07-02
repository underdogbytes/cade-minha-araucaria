<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAraucariaObservationRequest;
use App\Http\Requests\UpdateAraucariaObservationRequest;
use App\Http\Resources\AraucariaObservationResource;
use App\Models\AraucariaObservation;
use App\Models\AraucariaObservationReport;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AraucariaObservationController extends Controller
{
    /**
     * Exibe os detalhes de uma observação específica.
     */
    public function show(AraucariaObservation $observation)
    {
        return view('observations.show', compact('observation'));
    }

    /**
     * Armazena uma nova observação.
     */
    public function store(
        StoreAraucariaObservationRequest $request
    ): JsonResponse {

        $validated = $request->validated();

        DB::beginTransaction();

        try {

            if ($request->hasFile('photo_path')) {
                $validated['photo_path'] = $this->processImage(
                    $request->file('photo_path')
                );
            }

            $observation = $request
                ->user()
                ->araucariaObservations()
                ->create($validated);

            DB::commit();

            return response()->json([
                'message' => 'Observação de Araucária registrada com sucesso!',
                'data' => new AraucariaObservationResource($observation),
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

            if ($request->hasFile('photo_path')) {

                if ($observation->photo_path) {
                    Storage::disk('public')->delete(
                        $observation->photo_path
                    );
                }

                $validated['photo_path'] = $this->processImage(
                    $request->file('photo_path')
                );
            }

            $observation->update($validated);

            DB::commit();

            return response()->json([
                'message' => 'Observação atualizada com sucesso!',
                'data' => new AraucariaObservationResource(
                    $observation->fresh()
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

    /**
     * Reporta uma observação.
    */
    public function report(Request $request, AraucariaObservation $observation)
    {
        $request->validate([
            'reason' => 'required|string|in:inappropriate_image,ownership,other',
            'details' => 'nullable|string|max:144',
        ]);

        try {
            $observation->reports()->create([
                'user_id' => $request->user()->id,
                'reason' => $request->input('reason'),
                'details' => $request->input('details'),
                'status' => 'pending',
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Observação denunciada com sucesso!',
                ], 201);
            }

            return redirect()->back()->with('status', 'Observação denunciada com sucesso!');

        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'message' => 'Erro ao reportar observação.',
            ], 500);
        }
    }

    public function moderationIndex()
    {
        $groupedReports = AraucariaObservationReport::with(['observation.user', 'user'])
            ->latest()
            ->get()
            ->groupBy('araucaria_observation_id');

        return view('observations.moderation.index', compact('groupedReports'));
    }

    public function moderationDelete(AraucariaObservationReport $report)
    {
        $report->observation->delete();

        return redirect()->back()->with('status', 'Observação removida da moderação e denúncias excluídas.');
    }

    public function moderationAssign(Request $request, AraucariaObservationReport $report)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $report->observation->update([
            'user_id' => $request->input('user_id'),
        ]);

        $report->update([
            'status' => 'assigned',
        ]);

        return redirect()->back()->with('status', 'Observação atribuída com sucesso.');
    }
}