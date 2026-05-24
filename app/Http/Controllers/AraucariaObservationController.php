<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AraucariaObservation;
use App\Http\Requests\StoreAraucariaObservationRequest;
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
        // Eager loading do relacionamento 'user' para evitar o problema de consulta N+1 no banco
        $observations = AraucariaObservation::with('user')->latest()->get();

        return AraucariaObservationResource::collection($observations);
    }

    /**
     * Valida e armazena uma nova observação com upload de foto (Apenas Autenticados).
     */
    public function store(StoreAraucariaObservationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        // Manipulação do Upload da Foto
        if ($request->hasFile('photo')) {
            // Salva em: storage/app/public/observations/ gerando um hash único para o nome do arquivo
            $path = $request->file('photo')->store('observations', 'public');
            $validated['photo_path'] = $path;
        }

        // Cria o registro vinculado ao usuário autenticado na requisição
        $observation = $request->user()->araucariaObservations()->create($validated);

        return response()->json([
            'message' => 'Observação de Araucária registrada com sucesso!',
            'data' => new AraucariaObservationResource($observation)
        ], 201);
    }
}
