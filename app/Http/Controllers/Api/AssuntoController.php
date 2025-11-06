<?php

namespace App\Http\Controllers\Api;

use Core\UseCase\Assunto\CriarAssuntoUseCase;
use Core\UseCase\Assunto\ListarAssuntoUseCase;
use Core\UseCase\Assunto\ListarAssuntosUseCase;
use Core\UseCase\Assunto\AtualizarAssuntoUseCase;
use Core\UseCase\Assunto\DeletarAssuntoUseCase;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Core\Domain\Exception\EntityValidationException;
use InvalidArgumentException;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssuntoController extends Controller
{
    public function __construct(
        private CriarAssuntoUseCase $criarAssuntoUseCase,
        private ListarAssuntoUseCase $listarAssuntoUseCase,
        private ListarAssuntosUseCase $listarAssuntosUseCase,
        private AtualizarAssuntoUseCase $atualizarAssuntoUseCase,
        private DeletarAssuntoUseCase $deletarAssuntoUseCase
    ) {}

    public function index(): JsonResponse
    {
        $assuntos = $this->listarAssuntosUseCase->execute();

        $items = array_map(function ($assunto) {
            return [
                'id' => $assunto->getId()->value(),
                'descricao' => $assunto->getDescricao()->value(),
            ];
        }, $assuntos);

        return response()->json([
            'data' => $items,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $assunto = $this->criarAssuntoUseCase->execute(
                descricao: $request->input('descricao') ?? ''
            );

            return response()->json([
                'data' => [
                    'id' => $assunto->getId()->value(),
                    'descricao' => $assunto->getDescricao()->value(),
                ],
            ], 201);
        } catch (EntityValidationException | InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $assunto = $this->listarAssuntoUseCase->execute($id);

            return response()->json([
                'data' => [
                    'id' => $assunto->getId()->value(),
                    'descricao' => $assunto->getDescricao()->value(),
                ],
            ], 200);
        } catch (LivroNaoEncontradoException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $assunto = $this->atualizarAssuntoUseCase->execute(
                id: $id,
                descricao: $request->input('descricao') ?? ''
            );

            return response()->json([
                'data' => [
                    'id' => $assunto->getId()->value(),
                    'descricao' => $assunto->getDescricao()->value(),
                ],
            ], 200);
        } catch (LivroNaoEncontradoException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        } catch (EntityValidationException | InvalidArgumentException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(string $id): JsonResponse
    {
        try {
            $this->deletarAssuntoUseCase->execute($id);

            return response()->json([], 204);
        } catch (LivroNaoEncontradoException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}

