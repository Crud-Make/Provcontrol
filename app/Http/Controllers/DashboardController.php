<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Fechamento;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $postoId = currentPostoId();

        $ultimosFechamentos = Fechamento::query()
            ->with('turno')
            ->forPosto($postoId)
            ->orderByDesc('data')
            ->limit(5)
            ->get();

        return view('dashboard.index', [
            'posto' => currentPosto(),
            'ultimosFechamentos' => $ultimosFechamentos,
        ]);
    }
}
