<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToPosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Frentista extends Model
{
    use BelongsToPosto, HasFactory, SoftDeletes;

    protected $fillable = [
        'posto_id',
        'user_id',
        'nome',
        'cpf',
        'telefone',
        'data_admissao',
        'foto_url',
        'ativo',
    ];

    protected $casts = [
        'data_admissao' => 'date',
        'ativo' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function fechamentosFrentistas(): HasMany
    {
        return $this->hasMany(FechamentoFrentista::class);
    }
}
