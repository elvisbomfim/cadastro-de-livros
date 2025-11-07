<?php

namespace App\Http\Controllers;

use Core\UseCase\Relatorio\LivrosPorCategoriaUseCase;
use Core\UseCase\Relatorio\FichaDetalhadaLivroUseCase;
use Core\UseCase\Relatorio\RelatorioPorAutorUseCase;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioController extends Controller
{
    public function __construct(
        private LivrosPorCategoriaUseCase $livrosPorCategoriaUseCase,
        private FichaDetalhadaLivroUseCase $fichaDetalhadaLivroUseCase,
        private RelatorioPorAutorUseCase $relatorioPorAutorUseCase
    ) {}

    public function livrosPorCategoria()
    {
        try {
            $dados = $this->livrosPorCategoriaUseCase->execute();
            
            $pdf = Pdf::loadView('relatorios.livros-por-categoria', [
                'dados' => $dados,
                'titulo' => 'Relatório de Livros por Categoria'
            ]);
            
            return $pdf->download('livros-por-categoria.pdf');
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF de livros por categoria: ' . $e->getMessage());
            abort(500, 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }

    public function fichaDetalhadaLivro(string $id)
    {
        try {
            $livro = $this->fichaDetalhadaLivroUseCase->execute($id);
            
            $pdf = Pdf::loadView('relatorios.ficha-detalhada-livro', [
                'livro' => (object) $livro,
                'titulo' => 'Ficha Detalhada do Livro'
            ]);
            
            return $pdf->download('ficha-livro-' . $id . '.pdf');
        } catch (LivroNaoEncontradoException $e) {
            abort(404, $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF da ficha detalhada: ' . $e->getMessage());
            abort(500, 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }

    public function relatorioPorAutor()
    {
        try {
            $dados = $this->relatorioPorAutorUseCase->execute();
            
            $pdf = Pdf::loadView('relatorios.relatorio-por-autor', [
                'dados' => $dados,
                'titulo' => 'Relatório Agrupado por Autor'
            ]);
            
            return $pdf->download('relatorio-por-autor.pdf');
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar PDF de relatório por autor: ' . $e->getMessage());
            abort(500, 'Erro ao gerar PDF: ' . $e->getMessage());
        }
    }

    public function livrosPorCategoriaJson()
    {
        $dados = $this->livrosPorCategoriaUseCase->execute();
        
        return response()->json([
            'data' => $dados
        ]);
    }

    public function fichaDetalhadaLivroJson(string $id)
    {
        try {
            $livro = $this->fichaDetalhadaLivroUseCase->execute($id);
            
            return response()->json([
                'data' => $livro
            ]);
        } catch (LivroNaoEncontradoException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 404);
        }
    }

    public function relatorioPorAutorJson()
    {
        $dados = $this->relatorioPorAutorUseCase->execute();
        
        return response()->json([
            'data' => $dados
        ]);
    }
}
