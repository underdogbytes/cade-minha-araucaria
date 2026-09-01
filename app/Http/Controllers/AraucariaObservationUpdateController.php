<?php

namespace App\Http\Controllers;

use App\Models\AraucariaObservation;
use App\Models\AraucariaObservationUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class AraucariaObservationUpdateController extends Controller
{
    /**
     * Anexa uma nova foto/atualização de acompanhamento à árvore.
     */
    public function store(Request $request, AraucariaObservation $observation)
    {
        if (! $observation->is_shared && $observation->user_id !== $request->user()->id) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Esta árvore não aceita atualizações colaborativas.'], 403);
            }
            return redirect()->back()->with('error', 'Esta árvore não aceita atualizações colaborativas.');
        }

        $request->validate([
            'photo_path'  => 'required|image|max:10240',
            'notes'       => 'nullable|string|max:1000',
            'stage'       => 'nullable|in:seedling,sapling,adult,dead',
            'observed_at' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $photoDataUrl = $this->processImage($request->file('photo_path'));

            $update = $observation->updates()->create([
                'user_id'     => $request->user()->id,
                'photo_path'  => $photoDataUrl,
                'notes'       => $request->input('notes'),
                'stage'       => $request->input('stage'),
                'observed_at' => $request->input('observed_at') ?? now(),
            ]);

            // Se um novo estágio foi fornecido, atualiza também o registro principal
            if ($request->filled('stage')) {
                $observation->update(['stage' => $request->input('stage')]);
            }

            // Incrementa pinhões do colaborador por cuidar da árvore
            $user = $request->user();
            $user->increment('pinhao_balance', 1);

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Foto de acompanhamento adicionada com sucesso! Você ganhou +1 Pinhão 🌲',
                    'data'    => $update->load('user'),
                ], 201);
            }

            return redirect()->back()->with('status', 'Foto de acompanhamento adicionada com sucesso! +1 Pinhão recebido! 🌲');

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Erro ao salvar foto de acompanhamento.'], 500);
            }

            return redirect()->back()->with('error', 'Erro ao salvar foto de acompanhamento.');
        }
    }

    /**
     * Remove uma foto/atualização de acompanhamento.
     */
    public function destroy(Request $request, AraucariaObservationUpdate $update)
    {
        $user = $request->user();
        $isAuthor = $update->user_id === $user->id;
        $isTreeOwner = $update->observation->user_id === $user->id;
        $isAdmin = in_array($user->role ?? 'user', ['admin', 'staff']);

        if (! $isAuthor && ! $isTreeOwner && ! $isAdmin) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Ação não autorizada.'], 403);
            }
            return redirect()->back()->with('error', 'Ação não autorizada.');
        }

        try {
            $update->delete();

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Atualização removida com sucesso.']);
            }

            return redirect()->back()->with('status', 'Atualização removida com sucesso.');

        } catch (\Throwable $e) {
            report($e);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Erro ao remover atualização.'], 500);
            }

            return redirect()->back()->with('error', 'Erro ao remover atualização.');
        }
    }

    /**
     * Alterna o estado de compartilhamento colaborativo da árvore.
     */
    public function toggleShared(Request $request, AraucariaObservation $observation)
    {
        if ($observation->user_id !== $request->user()->id && ! in_array($request->user()->role ?? 'user', ['admin', 'staff'])) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Ação não autorizada.'], 403);
            }
            return redirect()->back()->with('error', 'Ação não autorizada.');
        }

        $observation->update([
            'is_shared' => ! $observation->is_shared,
        ]);

        $statusMsg = $observation->is_shared
            ? 'A árvore agora é compartilhada com a comunidade!'
            : 'A árvore agora é privada para novas contribuições.';

        if ($request->wantsJson()) {
            return response()->json([
                'message'   => $statusMsg,
                'is_shared' => $observation->is_shared,
            ]);
        }

        return redirect()->back()->with('status', $statusMsg);
    }

    /**
     * Reporta/denuncia uma foto de acompanhamento colaborativa.
     */
    public function report(Request $request, AraucariaObservationUpdate $update)
    {
        $request->validate([
            'reason'  => 'required|string|in:inappropriate_image,ownership,other',
            'details' => 'nullable|string|max:144',
        ]);

        try {
            $update->observation->reports()->create([
                'araucaria_observation_update_id' => $update->id,
                'user_id' => $request->user()->id,
                'reason'  => $request->input('reason'),
                'details' => $request->input('details'),
                'status'  => 'pending',
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Foto colaborativa denunciada com sucesso! A equipe de moderação irá analisar.',
                ], 201);
            }

            return redirect()->back()->with('status', 'Foto colaborativa denunciada com sucesso! A moderação analisará o conteúdo.');

        } catch (\Throwable $e) {
            report($e);

            if ($request->wantsJson()) {
                return response()->json(['message' => 'Erro ao reportar foto colaborativa.'], 500);
            }

            return redirect()->back()->with('error', 'Erro ao reportar foto colaborativa.');
        }
    }

    /**
     * Processa e comprime a imagem para Data URL Base64.
     */
    private function processImage(\Illuminate\Http\UploadedFile $file): string
    {
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->scaleDown(width: 1200, height: 1200);
        $encoded = $image->toJpeg(80);

        return 'data:image/jpeg;base64,' . base64_encode((string) $encoded);
    }
}
