<?php

namespace App\Modules\Comerciante\Controllers\Pessoas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PessoaController extends Controller
{
    /**
     * Lista pessoas
     */
    public function index(Request $request)
    {
        $empresaId = $request->get('empresa_id', Auth::user()->empresa_id ?? 2);

        $filtros = $request->only([
            'nome',
            'cpf_cnpj',
            'email',
            'telefone',
            'tipo',
            'status',
            'departamento_id',
            'cargo_id',
            'order_by',
            'order_direction'
        ]);

        // Query base
        $query = DB::table('pessoas as p')
            ->leftJoin('pessoas_departamentos as d', 'p.departamento_id', '=', 'd.id')
            ->leftJoin('pessoas_cargos as c', 'p.cargo_id', '=', 'c.id')
            ->where('p.empresa_id', $empresaId)
            ->select([
                'p.*',
                'd.nome as departamento_nome',
                'c.nome as cargo_nome'
            ]);

        // Aplicar filtros
        if (!empty($filtros['nome'])) {
            $query->where(function ($q) use ($filtros) {
                $q->where('p.nome', 'like', '%' . $filtros['nome'] . '%')
                    ->orWhere('p.sobrenome', 'like', '%' . $filtros['nome'] . '%')
                    ->orWhere('p.nome_social', 'like', '%' . $filtros['nome'] . '%');
            });
        }

        if (!empty($filtros['tipo'])) {
            $query->where('p.tipo', 'like', '%' . $filtros['tipo'] . '%');
        }

        if (!empty($filtros['status'])) {
            $query->where('p.status', $filtros['status']);
        }

        if (!empty($filtros['departamento_id'])) {
            $query->where('p.departamento_id', $filtros['departamento_id']);
        }

        if (!empty($filtros['cargo_id'])) {
            $query->where('p.cargo_id', $filtros['cargo_id']);
        }

        if (!empty($filtros['email'])) {
            $query->where('p.email', 'like', '%' . $filtros['email'] . '%');
        }

        if (!empty($filtros['cpf_cnpj'])) {
            $query->where('p.cpf_cnpj', 'like', '%' . $filtros['cpf_cnpj'] . '%');
        }

        if (!empty($filtros['telefone'])) {
            $query->where('p.telefone', 'like', '%' . $filtros['telefone'] . '%');
        }

        // Ordenação
        $orderBy = $filtros['order_by'] ?? 'nome';
        $orderDirection = $filtros['order_direction'] ?? 'asc';
        $query->orderBy('p.' . $orderBy, $orderDirection);

        $pessoas = $query->paginate(20)->withQueryString();

        // Buscar dados complementares para filtros
        $departamentos = DB::table('pessoas_departamentos')
            ->where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        $cargos = DB::table('pessoas_cargos')
            ->where('empresa_id', $empresaId)
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();

        // Estatísticas por tipo
        $stats = [
            'total' => DB::table('pessoas')->where('empresa_id', $empresaId)->count(),
            'clientes' => DB::table('pessoas')->where('empresa_id', $empresaId)->where('tipo', 'like', '%cliente%')->count(),
            'funcionarios' => DB::table('pessoas')->where('empresa_id', $empresaId)->where('tipo', 'like', '%funcionario%')->count(),
            'fornecedores' => DB::table('pessoas')->where('empresa_id', $empresaId)->where('tipo', 'like', '%fornecedor%')->count(),
            'entregadores' => DB::table('pessoas')->where('empresa_id', $empresaId)->where('tipo', 'like', '%entregador%')->count(),
        ];

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $pessoas,
                'stats' => $stats
            ]);
        }

        return view('comerciante.pessoas.index', compact('pessoas', 'filtros', 'departamentos', 'cargos', 'stats', 'empresaId'));
    }

    /**
     * Mostra formulário de criação
     */
    public function create(Request $request)
    {
        $empresaId = $request->get('empresa_id', Auth::user()->empresa_id ?? 2);
        $tipo = $request->get('tipo', 'cliente');

        $configuracoes = $this->getConfiguracoes($empresaId);

        return view('comerciante.pessoas.create', compact('tipo', 'configuracoes', 'empresaId'));
    }

    /**
     * Armazena nova pessoa
     */
    public function store(Request $request)
    {
        try {
            Log::info("Tentando criar pessoa", [
                'dados_recebidos' => $request->all(),
                'empresa_id' => $request->get('empresa_id'),
                'tipos' => $request->get('tipos')
            ]);

            $validator = $this->getValidator($request->all());

            if ($validator->fails()) {
                Log::warning("Validação falhou ao criar pessoa", [
                    'erros' => $validator->errors()->toArray(),
                    'dados' => $request->all()
                ]);

                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dados inválidos',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $dados = $request->all();
            $dados['empresa_id'] = $request->get('empresa_id', Auth::user()->empresa_id ?? 2);

            // Remover campos que não devem ir para o banco
            unset($dados['_token']);

            // Processar campos vazios que devem ser null
            foreach (
                [
                    'sobrenome',
                    'nome_social',
                    'cpf_cnpj',
                    'email',
                    'telefone',
                    'data_nascimento',
                    'data_admissao',
                    'salario_atual',
                    'observacoes',
                    'departamento_id',
                    'cargo_id'
                ] as $campo
            ) {
                if (isset($dados[$campo]) && empty($dados[$campo])) {
                    $dados[$campo] = null;
                }
            }

            // Processar tipos (array para string)
            if (isset($dados['tipos']) && is_array($dados['tipos'])) {
                $dados['tipo'] = implode(',', $dados['tipos']);
                unset($dados['tipos']);
            }

            // Adicionar campos de auditoria
            $dados['created_at'] = now();
            $dados['updated_at'] = now();
            $dados['sync_status'] = 'pendente';
            $dados['sync_data'] = now();

            // Campos obrigatórios com defaults se não fornecidos
            if (!isset($dados['nacionalidade'])) {
                $dados['nacionalidade'] = 'Brasileira';
            }
            if (!isset($dados['pessoa_juridica'])) {
                $dados['pessoa_juridica'] = 0;
            }
            if (!isset($dados['limite_credito'])) {
                $dados['limite_credito'] = 0.00;
            }
            if (!isset($dados['limite_fiado'])) {
                $dados['limite_fiado'] = 0.00;
            }
            if (!isset($dados['afiliado_total_vendas'])) {
                $dados['afiliado_total_vendas'] = 0.00;
            }
            if (!isset($dados['afiliado_total_comissoes'])) {
                $dados['afiliado_total_comissoes'] = 0.00;
            }
            if (!isset($dados['afiliado_total_pago'])) {
                $dados['afiliado_total_pago'] = 0.00;
            }
            if (!isset($dados['situacao_trabalhista'])) {
                $dados['situacao_trabalhista'] = 'ativo';
            }

            Log::info("Dados processados para inserção", [
                'dados_finais' => $dados
            ]);

            $pessoaId = DB::table('pessoas')->insertGetId($dados);

            Log::info("Pessoa criada com sucesso", [
                'pessoa_id' => $pessoaId,
                'nome' => $dados['nome'],
                'empresa_id' => $dados['empresa_id']
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pessoa criada com sucesso',
                    'data' => ['id' => $pessoaId]
                ], 201);
            }

            return redirect()->route('comerciantes.clientes.pessoas.index', ['empresa_id' => $dados['empresa_id']])
                ->with('success', 'Pessoa criada com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar pessoa: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Erro ao criar pessoa: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Mostra detalhes da pessoa
     */
    public function show(Request $request, $id)
    {
        $pessoa = DB::table('pessoas as p')
            ->leftJoin('pessoas_departamentos as d', 'p.departamento_id', '=', 'd.id')
            ->leftJoin('pessoas_cargos as c', 'p.cargo_id', '=', 'c.id')
            ->where('p.id', $id)
            ->select([
                'p.*',
                'd.nome as departamento_nome',
                'd.codigo as departamento_codigo',
                'c.nome as cargo_nome',
                'c.codigo as cargo_codigo'
            ])
            ->first();

        if (!$pessoa) {
            abort(404, 'Pessoa não encontrada');
        }

        return view('comerciante.pessoas.show', compact('pessoa'));
    }

    /**
     * Mostra formulário de edição
     */
    public function edit(Request $request, $id)
    {
        try {
            Log::info("PessoaController::edit - Iniciando edição para ID: " . $id);

            $pessoa = DB::table('pessoas')->where('id', $id)->first();

            if (!$pessoa) {
                Log::error("PessoaController::edit - Pessoa não encontrada para ID: " . $id);
                abort(404, 'Pessoa não encontrada');
            }

            Log::info("PessoaController::edit - Pessoa encontrada: " . $pessoa->nome . " (empresa_id: " . $pessoa->empresa_id . ")");

            $configuracoes = $this->getConfiguracoes($pessoa->empresa_id);

            Log::info("PessoaController::edit - Configurações carregadas - Departamentos: " . count($configuracoes['departamentos']) . ", Cargos: " . count($configuracoes['cargos']));

            return view('comerciante.pessoas.edit', compact('pessoa', 'configuracoes'));
        } catch (\Exception $e) {
            Log::error("PessoaController::edit - Erro: " . $e->getMessage());
            Log::error("PessoaController::edit - Trace: " . $e->getTraceAsString());

            return response()->json([
                'error' => 'Erro no método edit',
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Atualiza pessoa
     */
    public function update(Request $request, $id)
    {
        try {
            $validator = $this->getValidator($request->all(), $id);

            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Dados inválidos',
                        'errors' => $validator->errors()
                    ], 422);
                }
                return back()->withErrors($validator)->withInput();
            }

            $dados = $request->all();

            // Processar tipos (array para string)
            if (isset($dados['tipos']) && is_array($dados['tipos'])) {
                $dados['tipo'] = implode(',', $dados['tipos']);
                unset($dados['tipos']);
            }

            // Atualizar campos de auditoria
            $dados['updated_at'] = now();
            $dados['sync_status'] = 'pendente';
            $dados['sync_data'] = now();

            DB::table('pessoas')->where('id', $id)->update($dados);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pessoa atualizada com sucesso'
                ]);
            }

            return redirect()->route('comerciantes.clientes.pessoas.show', $id)
                ->with('success', 'Pessoa atualizada com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao atualizar pessoa: ' . $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => 'Erro ao atualizar pessoa: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove pessoa
     */
    public function destroy(Request $request, $id)
    {
        try {
            $pessoa = DB::table('pessoas')->where('id', $id)->first();

            if (!$pessoa) {
                throw new \Exception('Pessoa não encontrada');
            }

            DB::table('pessoas')->where('id', $id)->delete();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pessoa excluída com sucesso'
                ]);
            }

            return redirect()->route('comerciantes.clientes.pessoas.index', ['empresa_id' => $pessoa->empresa_id])
                ->with('success', 'Pessoa excluída com sucesso!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Obtém configurações para formulários
     */
    protected function getConfiguracoes($empresaId)
    {
        return [
            'departamentos' => DB::table('pessoas_departamentos')
                ->where('empresa_id', $empresaId)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get(),
            'cargos' => DB::table('pessoas_cargos')
                ->where('empresa_id', $empresaId)
                ->where('ativo', true)
                ->orderBy('nome')
                ->get(),
        ];
    }

    /**
     * Obtém validator para validação
     */
    protected function getValidator(array $data, $excludeId = null)
    {
        $rules = [
            'nome' => 'required|string|max:255',
            'sobrenome' => 'nullable|string|max:255',
            'nome_social' => 'nullable|string|max:100',
            'cpf_cnpj' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'genero' => 'nullable|in:masculino,feminino,outros,nao_informar',
            'tipos' => 'required|array|min:1',
            'tipos.*' => 'in:cliente,funcionario,fornecedor,entregador',
            'status' => 'required|in:ativo,inativo,suspenso',
            'departamento_id' => 'nullable|exists:pessoas_departamentos,id',
            'cargo_id' => 'nullable|exists:pessoas_cargos,id',
            'data_admissao' => 'nullable|date',
            'salario_atual' => 'nullable|numeric|min:0',
            'observacoes' => 'nullable|string'
        ];

        return Validator::make($data, $rules, [
            'nome.required' => 'O nome é obrigatório',
            'tipos.required' => 'Selecione pelo menos um tipo de pessoa',
            'tipos.min' => 'Selecione pelo menos um tipo de pessoa',
            'email.email' => 'Formato de email inválido',
            'data_nascimento.date' => 'Data de nascimento inválida',
            'data_admissao.date' => 'Data de admissão inválida',
            'salario_atual.numeric' => 'Salário deve ser um valor numérico',
            'salario_atual.min' => 'Salário não pode ser negativo'
        ]);
    }
}
