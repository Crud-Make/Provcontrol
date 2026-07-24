<div class="flex items-center gap-3">
    {{-- Logo Posto Providência (recriado em vetor; troque pelo asset oficial em logo.png) --}}
    <svg viewBox="0 0 252 52" class="h-9 w-auto" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Posto Providência">
        <defs>
            <linearGradient id="logo-sw" x1="0" y1="0" x2="1" y2="0">
                <stop offset="0" stop-color="#f7941d" />
                <stop offset="1" stop-color="#ffce3a" />
            </linearGradient>
        </defs>
        <path d="M6 27 C 74 12, 158 12, 232 21" fill="none" stroke="url(#logo-sw)" stroke-width="5" stroke-linecap="round" />
        <path d="M228 12 l11 8 -11 8 M238 12 l11 8 -11 8" fill="none" stroke="url(#logo-sw)" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round" />
        <text x="7" y="20" font-family="Instrument Sans, sans-serif" font-weight="800" font-size="16" letter-spacing="3.5" fill="#8298d8">POSTO</text>
        <text x="7" y="46" font-family="Instrument Sans, sans-serif" font-weight="800" font-size="21" letter-spacing="1.5" fill="#e11d26">PROVIDÊNCIA</text>
    </svg>
    <div class="pl-3 border-l border-slate-700">
        <h2 class="text-2xl font-bold text-slate-100">Fechamento Diário</h2>
        <p class="text-sm text-slate-400">Caixa diário — edite e salve; totais vêm do servidor</p>
    </div>
    <a href="{{ route('fechamentos.index') }}" class="ml-auto text-slate-400 hover:text-brand-300 text-sm">← Voltar</a>
</div>
