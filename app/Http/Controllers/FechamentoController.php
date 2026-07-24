<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AtualizarFechamentoRequest;
use App\Http\Requests\PreviewFechamentoRequest;
use App\Http\Requests\StoreFechamentoRequest;
use App\Models\Fechamento;
use App\Models\Turno;
use App\Models\User;
use App\Services\FechamentoPreparador;
use App\Services\FechamentoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class FechamentoController extends Controller
{
    public function __construct(
        private readonly FechamentoService $fechamentoService,
        private readonly FechamentoPreparador $fechamentoPreparador,
    ) {}

    public function index(): View
    {
        Gate::authorize('viewAny', Fechamento::class);

        $postoId = currentPostoId();

        $fechamentos = Fechamento::query()
            ->with(['turno', 'fechadoPor'])
            ->forPosto($postoId)
            ->orderByDesc('data')
            ->paginate(15);

        return view('fechamentos.index', compact('fechamentos'));
    }

    public function create(): View
    {
        Gate::authorize('create', Fechamento::class);

        $postoId = currentPostoId();

        $turnos = Turno::query()
            ->forPosto($postoId)
            ->where('ativo', true)
            ->orderBy('ordem')
            ->get();

        $data = now()->toDateString();
        $turnoIdInicial = (string) ($turnos->firstWhere('nome', 'Dia')?->id ?? '');

        return view('fechamentos.create', compact('turnos', 'data', 'turnoIdInicial'));
    }

    public function preview(PreviewFechamentoRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $postoId = currentPostoId();
        /** @var User $user */
        $user = $request->user();

        $this->fechamentoPreparador->preparar(
            $validated['data'],
            (int) $validated['turno_id'],
            $postoId,
            $user
        );

        $resultado = $this->fechamentoService->calcularFechamento(
            $validated['data'],
            (int) $validated['turno_id'],
            $postoId
        );

        return response()->json($resultado);
    }

    public function atualizar(AtualizarFechamentoRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $postoId = currentPostoId();

        $this->fechamentoService->atualizarDados($validated, $postoId);

        $resultado = $this->fechamentoService->calcularFechamento(
            $validated['data'],
            (int) $validated['turno_id'],
            $postoId
        );

        return response()->json($resultado);
    }

    public function store(StoreFechamentoRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $postoId = currentPostoId();
        /** @var User $user */
        $user = $request->user();

        $this->fechamentoPreparador->preparar(
            $validated['data'],
            (int) $validated['turno_id'],
            $postoId,
            $user
        );

        $resultado = $this->fechamentoService->calcularFechamento(
            $validated['data'],
            (int) $validated['turno_id'],
            $postoId
        );

        $fechamento = $this->fechamentoService->fechar([
            'turno_id' => $validated['turno_id'],
            'data' => $validated['data'],
            'status' => $resultado['status'],
            'total_concentrador' => $resultado['total_concentrador'],
            'total_recebido' => $resultado['total_frentistas'],
            'diferenca' => $resultado['diferenca'],
            'observacoes' => $validated['observacoes'] ?? null,
        ], $postoId, $user);

        return redirect()->route('fechamentos.show', $fechamento)
            ->with('success', 'Fechamento realizado com sucesso!');
    }

    public function show(int $fechamento): View
    {
        $postoId = currentPostoId();

        $fechamento = Fechamento::query()
            ->with([
                'turno',
                'fechadoPor',
                'fechamentoFrentistas.frentista',
                'recebimentos.formaPagamento',
            ])
            ->forPosto($postoId)
            ->findOrFail($fechamento);

        Gate::authorize('view', $fechamento);

        return view('fechamentos.show', compact('fechamento'));
    }
}
