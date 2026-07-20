/<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Posto extends Model
{
    protected $fillable = [
        'nome',
        'cnpj',
        'endereco',
        'cidade',
        'uf',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function turnos(): HasMany
    {
        return $this->hasMany(Turno::class);
    }

    public function combustiveis(): HasMany
    {
        return $this->hasMany(Combustivel::class);
    }

    public function bombas(): HasMany
    {
        return $this->hasMany(Bomba::class);
    }

    public function frentistas(): HasMany
    {
        return $this->hasMany(Frentista::class);
    }

    public function fechamentos(): HasMany
    {
        return $this->hasMany(Fechamento::class);
    }
}
