<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmpresaController extends Controller
{
    /**
     * Lista todas as empresas
     */
    public function index(Request $request)
    {
        $query = Empresa::query();

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plano')) {
            $query->where('plano', $request->plano);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome_fantasia', 'like', "%{$search}%")
                    ->orWhere('razao_social', 'like', "%{$search}%")
                    ->orWhere('cnpj', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $empresas = $query->paginate(15);

        // Estatísticas
        $stats = $this->getEmpresaStats();

        return view('admin.empresas.index', compact('empresas', 'stats'));
    }

    /**
     * Exibe detalhes de uma empresa específica
     */
    public function show($id)
    {
        $empresa = Empresa::findOrFail($id);

        // Estatísticas da empresa
        $stats = $this->getEmpresaDetailStats($empresa);

        // Atividade recente (transações, etc.)
        $atividadeRecente = $this->getAtividadeRecente($empresa);

        return view('admin.empresas.show', compact('empresa', 'stats', 'atividadeRecente'));
    }

    /**
     * Formulário para criar nova empresa
     */
    public function create()
    {
        return view('admin.empresas.create');
    }

    /**
     * Salva nova empresa
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome_fantasia' => 'required|string|max:255',
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|size:14|unique:empresas,cnpj',
            'email' => 'required|email|unique:empresas,email',
            'telefone' => 'nullable|string|max:20',
            'plano' => 'required|in:basico,pro,premium,enterprise',
            'status' => 'required|in:ativo,inativo,suspenso,bloqueado',
            'valor_mensalidade' => 'nullable|numeric|min:0',
            'data_vencimento' => 'nullable|date|after:today'
        ]);

        $empresa = Empresa::create($request->all());

        return redirect()->route('admin.empresas.show', $empresa->id)
            ->with('success', 'Empresa criada com sucesso!');
    }

    /**
     * Formulário para editar empresa
     */
    public function edit($id)
    {
        $empresa = Empresa::findOrFail($id);
        return view('admin.empresas.edit', compact('empresa'));
    }

    /**
     * Atualiza empresa
     */
    public function update(Request $request, $id)
    {
        $empresa = Empresa::findOrFail($id);

        $request->validate([
            'nome_fantasia' => 'required|string|max:255',
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|size:14|unique:empresas,cnpj,' . $empresa->id,
            'email' => 'required|email|unique:empresas,email,' . $empresa->id,
            'telefone' => 'nullable|string|max:20',
            'plano' => 'required|in:basico,pro,premium,enterprise',
            'status' => 'required|in:ativo,inativo,suspenso,bloqueado',
            'valor_mensalidade' => 'nullable|numeric|min:0',
            'data_vencimento' => 'nullable|date'
        ]);

        $empresa->update($request->all());

        return redirect()->route('admin.empresas.show', $empresa->id)
            ->with('success', 'Empresa atualizada com sucesso!');
    }

    /**
     * Exclui empresa
     */
    public function destroy($id)
    {
        $empresa = Empresa::findOrFail($id);
        $empresa->delete();

        return redirect()->route('admin.empresas.index')
            ->with('success', 'Empresa excluída com sucesso!');
    }

    /**
     * Alterna status da empresa
     */
    public function toggleStatus($id)
    {
        $empresa = Empresa::findOrFail($id);

        $novoStatus = $empresa->status === 'ativo' ? 'inativo' : 'ativo';
        $empresa->update(['status' => $novoStatus]);

        return redirect()->back()
            ->with('success', "Status da empresa alterado para {$novoStatus}!");
    }

    /**
     * Exportar dados das empresas
     */
    public function export($format = 'xlsx')
    {
        // TODO: Implementar exportação
        return redirect()->back()->with('info', 'Exportação em desenvolvimento');
    }

    /**
     * Estatísticas gerais das empresas
     */
    private function getEmpresaStats()
    {
        try {
            return [
                'total' => Empresa::count(),
                'ativas' => Empresa::where('status', 'ativo')->count(),
                'inativas' => Empresa::where('status', 'inativo')->count(),
                'suspensas' => Empresa::where('status', 'suspenso')->count(),
                'vencimento_proximo' => Empresa::whereDate('data_vencimento', '<=', now()->addDays(7))->count(),
                'vencidas' => Empresa::whereDate('data_vencimento', '<', now())->count(),
                'receita_mensal' => Empresa::where('status', 'ativo')->sum('valor_mensalidade') ?: 0,
                'novas_mes' => Empresa::whereMonth('created_at', now()->month)->count(),
                'planos' => [
                    'basico' => Empresa::where('plano', 'basico')->count(),
                    'pro' => Empresa::where('plano', 'pro')->count(),
                    'premium' => Empresa::where('plano', 'premium')->count(),
                    'enterprise' => Empresa::where('plano', 'enterprise')->count(),
                ]
            ];
        } catch (\Exception $e) {
            return [
                'total' => 0,
                'ativas' => 0,
                'inativas' => 0,
                'suspensas' => 0,
                'vencimento_proximo' => 0,
                'vencidas' => 0,
                'receita_mensal' => 0,
                'novas_mes' => 0,
                'planos' => [
                    'basico' => 0,
                    'pro' => 0,
                    'premium' => 0,
                    'enterprise' => 0,
                ]
            ];
        }
    }

    /**
     * Estatísticas específicas de uma empresa
     */
    private function getEmpresaDetailStats($empresa)
    {
        try {
            return [
                'total_transacoes' => DB::table('afi_plan_transacoes')->where('empresa_id', $empresa->id)->count(),
                'valor_total_transacoes' => DB::table('afi_plan_transacoes')
                    ->where('empresa_id', $empresa->id)
                    ->where('status', 'aprovada')
                    ->sum('valor_final') ?: 0,
                'transacoes_mes' => DB::table('afi_plan_transacoes')
                    ->where('empresa_id', $empresa->id)
                    ->whereMonth('data_transacao', now()->month)
                    ->count(),
                'ultima_transacao' => DB::table('afi_plan_transacoes')
                    ->where('empresa_id', $empresa->id)
                    ->orderBy('data_transacao', 'desc')
                    ->first(),
                'dias_desde_cadastro' => $empresa->created_at ? $empresa->created_at->diffInDays(now()) : 0,
                'tempo_ativo' => $empresa->created_at ? $empresa->created_at->diffForHumans() : 'N/A'
            ];
        } catch (\Exception $e) {
            return [
                'total_transacoes' => 0,
                'valor_total_transacoes' => 0,
                'transacoes_mes' => 0,
                'ultima_transacao' => null,
                'dias_desde_cadastro' => 0,
                'tempo_ativo' => 'N/A'
            ];
        }
    }

    /**
     * Atividade recente da empresa
     */
    private function getAtividadeRecente($empresa)
    {
        try {
            return DB::table('afi_plan_transacoes')
                ->where('empresa_id', $empresa->id)
                ->orderBy('data_transacao', 'desc')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            return collect([]);
        }
    }

    /**
     * Relatório completo
     */
    private function getRelatorioCompleto()
    {
        $stats = $this->getEmpresaStats();

        // Adicionar mais dados específicos do relatório
        $stats['crescimento_mensal'] = $this->getCrescimentoMensal();
        $stats['churn_rate'] = $this->getChurnRate();
        $stats['ltv_medio'] = $this->getLtvMedio();

        return $stats;
    }

    private function getCrescimentoMensal()
    {
        try {
            $mesAtual = Empresa::whereMonth('created_at', now()->month)->count();
            $mesAnterior = Empresa::whereMonth('created_at', now()->subMonth()->month)->count();

            if ($mesAnterior == 0) return 0;

            return round((($mesAtual - $mesAnterior) / $mesAnterior) * 100, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getChurnRate()
    {
        try {
            $totalAtivas = Empresa::where('status', 'ativo')->count();
            $canceladasMes = Empresa::where('status', 'inativo')
                ->whereMonth('updated_at', now()->month)
                ->count();

            if ($totalAtivas == 0) return 0;

            return round(($canceladasMes / $totalAtivas) * 100, 2);
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getLtvMedio()
    {
        try {
            return Empresa::where('status', 'ativo')->avg('valor_mensalidade') * 12; // LTV estimado para 1 ano
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Gera relatórios de empresas
     */
    public function relatorio(Request $request)
    {
        $query = Empresa::query();

        // Aplicar filtros
        if ($request->filled('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('plano')) {
            $query->where('subscription_plan', $request->plano);
        }

        // Estatísticas
        $stats = [
            'total' => $query->count(),
            'ativas' => (clone $query)->where('status', 'ativo')->where('ativo', true)->count(),
            'vencendo' => (clone $query)->where('subscription_ends_at', '<=', now()->addDays(7))->count(),
            'bloqueadas' => (clone $query)->where('status', 'bloqueado')->orWhere('ativo', false)->count(),
        ];

        // Dados para gráficos
        $chartData = [
            'status' => [
                'labels' => ['Ativo', 'Inativo', 'Suspenso', 'Bloqueado'],
                'data' => [
                    Empresa::where('status', 'ativo')->where('ativo', true)->count(),
                    Empresa::where('status', 'inativo')->orWhere('ativo', false)->count(),
                    Empresa::where('status', 'suspenso')->count(),
                    Empresa::where('status', 'bloqueado')->count(),
                ]
            ],
            'plano' => [
                'labels' => ['Básico', 'Pro', 'Premium', 'Enterprise'],
                'data' => [
                    Empresa::where('subscription_plan', 'basico')->count(),
                    Empresa::where('subscription_plan', 'pro')->count(),
                    Empresa::where('subscription_plan', 'premium')->count(),
                    Empresa::where('subscription_plan', 'enterprise')->count(),
                ]
            ]
        ];

        // Verificar se é para exportar
        if ($request->has('export')) {
            return $this->exportRelatorio($query, $request->export);
        }

        // Buscar empresas para o relatório
        $empresas = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.empresas.relatorio', compact('empresas', 'stats', 'chartData'));
    }

    /**
     * Exporta relatório em diferentes formatos
     */
    private function exportRelatorio($query, $format)
    {
        $empresas = $query->get();

        if ($format === 'excel') {
            // Implementar export Excel (placeholder)
            return response()->json(['message' => 'Export Excel em desenvolvimento']);
        }

        if ($format === 'pdf') {
            // Implementar export PDF (placeholder)
            return response()->json(['message' => 'Export PDF em desenvolvimento']);
        }

        return redirect()->back()->with('error', 'Formato de export inválido');
    }
}
