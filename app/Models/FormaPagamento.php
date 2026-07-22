<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToPosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormaPagamento extends Model
{
    use BelongsToPosto, HasFactory;

    protected $table = 'formas_pagamento';

    protected $fillable = [
        'posto_id',
        'nome',
        'tipo',
        'taxa_percentual',
        'ativo',
    ];

    protected $casts = [
        'taxa_percentual' => 'decimal:2',
        'ativo' => 'boolean',
    ];

    public function recebimentos(): HasMany
    {
        return $this->hasMany(Recebimento::class);
    }
}
