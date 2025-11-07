<?php

namespace Tests\Unit\UseCase\Relatorio;

use Core\UseCase\Relatorio\RelatorioPorAutorUseCase;
use Core\Domain\Repository\RelatorioRepositoryInterface;
use Mockery;

describe('Use Case RelatorioPorAutor', function () {
    
    test('deve retornar lista vazia quando não há dados', function () {
        $repository = Mockery::mock(RelatorioRepositoryInterface::class);
        $useCase = new RelatorioPorAutorUseCase($repository);
        
        $repository->shouldReceive('relatorioPorAutor')
            ->once()
            ->andReturn([]);
        
        $dados = $useCase->execute();
        
        expect($dados)->toBeArray()
            ->and(count($dados))->toBe(0);
    });

    test('deve retornar relatório agrupado por autor', function () {
        $repository = Mockery::mock(RelatorioRepositoryInterface::class);
        $useCase = new RelatorioPorAutorUseCase($repository);
        
        $dadosEsperados = [
            (object) [
                'autor_id' => '11111111-1111-1111-1111-111111111111',
                'autor_nome' => 'Machado de Assis',
                'total_livros' => 5,
                'titulos_livros' => 'Dom Casmurro | Memórias Póstumas',
                'categorias' => 'Romance, Literatura',
                'valor_total' => 175.50,
                'preco_medio' => 35.10,
                'primeiro_livro_ano' => 1880,
                'ultimo_livro_ano' => 1908
            ],
            (object) [
                'autor_id' => '22222222-2222-2222-2222-222222222222',
                'autor_nome' => 'Clarice Lispector',
                'total_livros' => 3,
                'titulos_livros' => 'A Hora da Estrela | Perto do Coração Selvagem',
                'categorias' => 'Romance, Contos',
                'valor_total' => 90.00,
                'preco_medio' => 30.00,
                'primeiro_livro_ano' => 1943,
                'ultimo_livro_ano' => 1977
            ]
        ];
        
        $repository->shouldReceive('relatorioPorAutor')
            ->once()
            ->andReturn($dadosEsperados);
        
        $dados = $useCase->execute();
        
        expect($dados)->toBeArray()
            ->and(count($dados))->toBe(2)
            ->and($dados[0]->autor_nome)->toBe('Machado de Assis')
            ->and($dados[0]->total_livros)->toBe(5)
            ->and($dados[1]->autor_nome)->toBe('Clarice Lispector')
            ->and($dados[1]->total_livros)->toBe(3);
    });

    test('deve chamar o repository uma vez', function () {
        $repository = Mockery::mock(RelatorioRepositoryInterface::class);
        $useCase = new RelatorioPorAutorUseCase($repository);
        
        $repository->shouldReceive('relatorioPorAutor')
            ->once()
            ->andReturn([]);
        
        $useCase->execute();
    });

    test('deve retornar array com diferentes tamanhos', function () {
        $repository = Mockery::mock(RelatorioRepositoryInterface::class);
        $useCase = new RelatorioPorAutorUseCase($repository);
        
        $dados1 = [
            (object) ['autor_nome' => 'Autor 1', 'total_livros' => 1]
        ];
        
        $dados2 = [
            (object) ['autor_nome' => 'Autor 1', 'total_livros' => 1],
            (object) ['autor_nome' => 'Autor 2', 'total_livros' => 2],
            (object) ['autor_nome' => 'Autor 3', 'total_livros' => 3]
        ];
        
        $repository->shouldReceive('relatorioPorAutor')
            ->twice()
            ->andReturn($dados1, $dados2);
        
        $resultado1 = $useCase->execute();
        $resultado2 = $useCase->execute();
        
        expect(count($resultado1))->toBe(1)
            ->and(count($resultado2))->toBe(3);
    });

})->group('UseCase', 'Relatorio');

