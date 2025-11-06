<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Livro extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'livro';

    protected $fillable = [
        'id',
        'titulo',
        'editora',
        'edicao',
        'ano_publicacao',
        'preco',
    ];

    protected $casts = [
        'edicao' => 'integer',
        'ano_publicacao' => 'integer',
        'preco' => 'decimal:2',
    ];

    public function autors(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'livro_autor', 'livro_id', 'autor_id');
    }

    public function assuntos(): BelongsToMany
    {
        return $this->belongsToMany(Assunto::class, 'livro_assunto', 'livro_id', 'assunto_id');
    }
}
