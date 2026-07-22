<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\BelongsToPosto;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bomba extends Model
{
    use BelongsToPosto, HasFactory;

    protected $fillable = [
        'posto_id',
        'nome',
        'localizacao',
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function bicos(): HasMany
    {
        return $this->hasMany(Bico::class);
    }
}
