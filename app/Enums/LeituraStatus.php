<?php

declare(strict_types=1);

namespace App\Enums;

enum LeituraStatus: string
{
    case Rascunho = 'rascunho';
    case Confirmado = 'confirmado';
}
