<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivroAutor extends Model
{
    protected $table = 'livro_autor';

    public $incrementing = false;
    public $timestamps = false;


    protected $fillable = [
        'livro_id',
        'autor_id',
    ];

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class, 'livro_id');
    }

    public function autor(): BelongsTo
    {
        return $this->belongsTo(Autor::class, 'autor_id');
    }
}
