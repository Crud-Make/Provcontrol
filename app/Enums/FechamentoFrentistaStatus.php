<?php

declare(strict_types=1);

namespace App\Enums;

enum FechamentoFrentistaStatus: string
{
    case Pendente = 'pendente';
    case Ok = 'ok';
    case Divergente = 'divergente';
}
