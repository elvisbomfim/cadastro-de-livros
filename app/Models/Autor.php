<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Autor extends Model
{
    use HasUuids, HasFactory;

    protected $table = 'autor';

    protected $fillable = [
        'id',
        'nome',
    ];

    public function livros(): BelongsToMany
    {
        return $this->belongsToMany(Livro::class, 'livro_autor', 'autor_id', 'livro_id');
    }
}
