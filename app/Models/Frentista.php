<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Frentista extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'posto_id',
        'user_id',
        'nome',
        'cpf',
        'telefone',
        'data_admissao',
        'foto_url',
        'ativo'
    ];

    protected $casts = [
        'data_admissao' => 'date',
        'ativo' => 'boolean',
    ];

    public function posto(): BelongsTo
    {
        return $this->belongsTo(Posto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fechamentosFrentistas(): HasMany
    {
        return $this->hasMany(FechamentoFrentista::class);
    }
}
