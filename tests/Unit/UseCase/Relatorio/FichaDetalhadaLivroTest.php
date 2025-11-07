<?php

namespace Tests\Unit\UseCase\Relatorio;

use Core\UseCase\Relatorio\FichaDetalhadaLivroUseCase;
use Core\Domain\Repository\RelatorioRepositoryInterface;
use Core\Domain\Exception\LivroNaoEncontradoException;
use Mockery;

describe('Use Case FichaDetalhadaLivro', function () {
    
    test('deve retornar ficha detalhada de um livro existente', function () {
        $repository = Mockery::mock(RelatorioRepositoryInterface::class);
        $useCase = new FichaDetalhadaLivroUseCase($repository);
        
        $fichaEsperada = [
            'id' => '550e8400-e29b-41d4-a716-446655440000',
            'titulo' => 'Dom Casmurro',
            'editora' => 'Editora Globo',
            'edicao' => 1,
            'ano_publicacao' => 1899,
            'preco' => 35.50,
            'autores' => 'Machado de Assis',
            'assuntos' => 'Romance, Literatura',
            'total_autores' => 1,
            'total_assuntos' => 2
        ];
        
        $repository->shouldReceive('fichaDetalhadaLivro')
            ->once()
            ->with('550e8400-e29b-41d4-a716-446655440000')
            ->andReturn($fichaEsperada);
        
        $ficha = $useCase->execute('550e8400-e29b-41d4-a716-446655440000');
        
        expect($ficha)->toBeArray()
            ->and($ficha['id'])->toBe('550e8400-e29b-41d4-a716-446655440000')
            ->and($ficha['titulo'])->toBe('Dom Casmurro')
            ->and($ficha['editora'])->toBe('Editora Globo')
            ->and($ficha['total_autores'])->toBe(1)
            ->and($ficha['total_assuntos'])->toBe(2);
    });

    test('deve lançar exceção quando livro não encontrado', function () {
        $repository = Mockery::mock(RelatorioRepositoryInterface::class);
        $useCase = new FichaDetalhadaLivroUseCase($repository);
        
        $repository->shouldReceive('fichaDetalhadaLivro')
            ->once()
            ->with('id-inexistente')
            ->andReturn(null);
        
        $useCase->execute('id-inexistente');
    })->throws(LivroNaoEncontradoException::class);

    test('deve retornar múltiplas fichas diferentes com IDs diferentes', function () {
        $repository = Mockery::mock(RelatorioRepositoryInterface::class);
        $useCase = new FichaDetalhadaLivroUseCase($repository);
        
        $ficha1 = [
            'id' => '11111111-1111-1111-1111-111111111111',
            'titulo' => 'Dom Casmurro',
            'editora' => 'Editora Globo',
            'edicao' => 1,
            'ano_publicacao' => 1899,
            'preco' => 35.50,
            'autores' => 'Machado de Assis',
            'assuntos' => 'Romance',
            'total_autores' => 1,
            'total_assuntos' => 1
        ];
        
        $ficha2 = [
            'id' => '22222222-2222-2222-2222-222222222222',
            'titulo' => 'A Revolta de Atlas',
            'editora' => 'Editora Sextante',
            'edicao' => 1,
            'ano_publicacao' => 1957,
            'preco' => 49.90,
            'autores' => 'Ayn Rand',
            'assuntos' => 'Ficção Científica',
            'total_autores' => 1,
            'total_assuntos' => 1
        ];
        
        $repository->shouldReceive('fichaDetalhadaLivro')
            ->twice()
            ->andReturn($ficha1, $ficha2);
        
        $resultado1 = $useCase->execute('11111111-1111-1111-1111-111111111111');
        $resultado2 = $useCase->execute('22222222-2222-2222-2222-222222222222');
        
        expect($resultado1['id'])->toBe('11111111-1111-1111-1111-111111111111')
            ->and($resultado1['titulo'])->toBe('Dom Casmurro')
            ->and($resultado2['id'])->toBe('22222222-2222-2222-2222-222222222222')
            ->and($resultado2['titulo'])->toBe('A Revolta de Atlas');
    });

})->group('UseCase', 'Relatorio');

