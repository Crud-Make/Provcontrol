<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToPosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bico extends Model
{
    use BelongsToPosto, HasFactory;

    protected $fillable = [
        'posto_id',
        'bomba_id',
        'combustivel_id',
        'numero',
        'ativo',
        'ultima_afericao_em',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'ultima_afericao_em' => 'date',
    ];

    public function bomba(): BelongsTo
    {
        return $this->belongsTo(Bomba::class);
    }

    public function combustivel(): BelongsTo
    {
        return $this->belongsTo(Combustivel::class);
    }

    public function leituras(): HasMany
    {
        return $this->hasMany(Leitura::class);
    }
}
