<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\Posto;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToPosto
{
    public function posto(): BelongsTo
    {
        return $this->belongsTo(Posto::class);
    }

    public function scopeForPosto(Builder $query, int $postoId): Builder
    {
        return $query->where($this->getTable().'.posto_id', $postoId);
    }
}
