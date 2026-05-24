<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AraucariaObservation;
use App\Http\Requests\StoreAraucariaObservationRequest;
use App\Http\Requests\UpdateAraucariaObservationRequest;
use App\Http\Resources\AraucariaObservationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
class AraucariaObservationController extends Controller
{
    /**
     * Retorna todas as observações cadastradas (Endpoint Público).
     */
    public function index(): AnonymousResourceCollection
    {
        $observations = AraucariaObservation::with('user')->latest()->get();
        return AraucariaObservationResource::collection($observations);
    }

    /**
     * Valida e armazena uma nova observação com upload de foto (Apenas Autenticados).
     */
    public function store(StoreAraucariaObservationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('photo_url')) {
            // storage/app/public/observations/
            $path = $request->file('photo_url')->store('observations', 'public');
            $validated['photo_url'] = $path;
        }

        $observation = $request->user()->araucariaObservations()->create($validated);

        return response()->json([
            'message' => 'Observação de Araucária registrada com sucesso!',
            'data' => new AraucariaObservationResource($observation)
        ], 201);
    }

    /**
     * Atualiza uma observação existente (Apenas o Criador).
     */
    public function update(UpdateAraucariaObservationRequest $request, AraucariaObservation $observation): JsonResponse
    {
        if ($observation->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Esta ação não é autorizada.'
            ], 403);
        }

        $validated = $request->validated();

        if ($request->hasFile('photo_url') && $request->file('photo_url')->isValid()) {
            
            if ($observation->photo_url) {
                Storage::disk('public')->delete($observation->photo_url);
            }
            
            $path = $request->file('photo_url')->store('observations', 'public');
            $validated['photo_url'] = $path;
        } else {
            unset($validated['photo_url']);
        }

        $observation->update($validated);

        return response()->json([
            'message' => 'Observação de Araucária atualizada com sucesso!',
            'data' => new AraucariaObservationResource($observation->fresh())
        ], 200);
    }

    /**
     * Exclui uma observação existente (Apenas o Criador).
     */
    public function destroy(Request $request, AraucariaObservation $observation): JsonResponse
    {
        if ($observation->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Esta ação não é autorizada.'
            ], 403);
        }

        if ($observation->photo_url) {
            Storage::disk('public')->delete($observation->photo_url);
        }

        $observation->delete();

        return response()->json([
            'message' => 'Observação de Araucária excluída com sucesso!'
        ], 200);
    }
}
