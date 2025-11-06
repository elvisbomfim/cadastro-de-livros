<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Autor;
use App\Models\Assunto;
use App\Models\Livro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // $this->command->info('Criando usuários...');
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->command->info('Criando autores...');
        Autor::factory(200)->create();

        $this->command->info('Criando assuntos...');
        Assunto::factory(30)->create();

        $this->command->info('Criando livros com relacionamentos...');
        Livro::factory(1000)->create();

        $this->command->info('Seed concluído com sucesso!');
        $this->command->info('Total de registros criados:');
        $this->command->info('- Autores: ' . Autor::count());
        $this->command->info('- Assuntos: ' . Assunto::count());
        $this->command->info('- Livros: ' . Livro::count());
    }
}
