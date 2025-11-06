<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivroAssunto extends Model
{
    protected $table = 'livro_assunto';

    public $incrementing = false;
    public $timestamps = false;


    protected $fillable = [
        'livro_id',
        'assunto_id',
    ];

    public function livro(): BelongsTo
    {
        return $this->belongsTo(Livro::class, 'livro_id');
    }

    public function assunto(): BelongsTo
    {
        return $this->belongsTo(Assunto::class, 'assunto_id');
    }
}
