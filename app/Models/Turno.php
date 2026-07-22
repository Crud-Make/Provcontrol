<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToPosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Turno extends Model
{
    use BelongsToPosto, HasFactory;

    protected $fillable = [
        'posto_id',
        'nome',
        'hora_inicio',
        'hora_fim',
        'ordem',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function leituras(): HasMany
    {
        return $this->hasMany(Leitura::class);
    }

    public function fechamentos(): HasMany
    {
        return $this->hasMany(Fechamento::class);
    }
}
