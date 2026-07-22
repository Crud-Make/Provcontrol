<?php

declare(strict_types=1);

namespace App\Enums;

enum FechamentoStatus: string
{
    case Aberto = 'aberto';
    case Fechado = 'fechado';
    case Divergente = 'divergente';
}
