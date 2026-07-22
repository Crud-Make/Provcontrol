<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToPosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Combustivel extends Model
{
    use BelongsToPosto, HasFactory;

    protected $table = 'combustiveis';

    protected $fillable = [
        'posto_id',
        'nome',
        'codigo',
        'cor',
        'preco_atual',
        'ativo',
    ];

    protected $casts = [
        'preco_atual' => 'decimal:4',
        'ativo' => 'boolean',
    ];

    public function bicos(): HasMany
    {
        return $this->hasMany(Bico::class);
    }
}
