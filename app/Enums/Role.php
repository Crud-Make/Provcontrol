<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case Admin = 'admin';
    case Proprietario = 'proprietario';
    case Gerente = 'gerente';
    case Operador = 'operador';
    case Frentista = 'frentista';
}
