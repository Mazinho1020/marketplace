<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use App\Comerciantes\Models\HorarioFuncionamento;
use App\Comerciantes\Models\DiaSemana;
use App\Comerciantes\Models\EmpresaUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class HorarioFuncionamentoController extends Controller
{
    /**
     * Página principal - Dashboard de horários
     */
    public function index(Request $request, $empresa)
    {
        try {
            $user = auth('comerciante')->user();

            // Verificar se o usuário tem permissão para acessar esta empresa
            if (!$user->temPermissaoEmpresa($empresa)) {
                abort(403, 'Acesso negado a esta empresa.');
            }

            $empresaId = $empresa;

            $sistema = $request->get('sistema');

            // Status atual de todos os sistemas
            $relatorioStatus = HorarioFuncionamento::getRelatorioStatus($empresaId);

            // Horários padrão
            $horariosPadrao = HorarioFuncionamento::porEmpresa($empresaId)
                ->horariosPadrao()
                ->ativo()
                ->with('diaSemana')
                ->when($sistema, function ($query) use ($sistema) {
                    return $query->porSistema($sistema);
                })
                ->orderBy('sistema')
                ->orderBy('dia_semana_id')
                ->get();

            // Exceções futuras
            $excecoesFuturas = HorarioFuncionamento::porEmpresa($empresaId)
                ->excecoes()
                ->ativo()
                ->where('data_excecao', '>=', now()->format('Y-m-d'))
                ->when($sistema, function ($query) use ($sistema) {
                    return $query->porSistema($sistema);
                })
                ->orderBy('data_excecao')
                ->limit(5)
                ->get();

            $sistemas = ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'];

            return view('comerciantes.horarios.index', compact(
                'relatorioStatus',
                'horariosPadrao',
                'excecoesFuturas',
                'sistemas',
                'sistema',
                'empresa'
            ));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar horários: ' . $e->getMessage());
            return redirect()->back()->with('erro', 'Erro ao carregar horários: ' . $e->getMessage());
        }
    }

    /**
     * Listar horários padrão
     */
    public function horariosPadrao(Request $request, $empresa)
    {
        try {
            $user = auth('comerciante')->user();

            // Verificar se o comerciante tem permissão para esta empresa
            if (!$user->temPermissaoEmpresa($empresa)) {
                abort(403, 'Acesso negado a esta empresa.');
            }

            $empresaId = $empresa;
            $sistema = $request->get('sistema');

            $horarios = HorarioFuncionamento::porEmpresa($empresaId)
                ->horariosPadrao()
                ->ativo()
                ->with('diaSemana')
                ->when($sistema, function ($query) use ($sistema) {
                    return $query->porSistema($sistema);
                })
                ->orderBy('sistema')
                ->orderBy('dia_semana_id')
                ->get();

            // Status atual do PDV
            $statusPDV = HorarioFuncionamento::getStatusHoje($empresaId, 'PDV');
            $sistemas = ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'];

            return view('comerciantes.horarios.padrao.index', compact(
                'horarios',
                'statusPDV',
                'sistemas',
                'sistema',
                'empresa'
            ));
        } catch (\Exception $e) {
            Log::error('Erro ao listar horários padrão: ' . $e->getMessage());
            return redirect()->back()->with('erro', 'Erro ao carregar horários');
        }
    }

    /**
     * Formulário para criar horário padrão
     */
    public function createPadrao($empresa)
    {
        try {
            $user = auth('comerciante')->user();

            // Verificar se o comerciante tem permissão para esta empresa
            if (!$user->temPermissaoEmpresa($empresa)) {
                abort(403, 'Acesso negado a esta empresa.');
            }

            $diasSemana = DiaSemana::getAll();
            $sistemas = ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'];

            return view('comerciantes.horarios.padrao.create', compact('diasSemana', 'sistemas', 'empresa'));
        } catch (\Exception $e) {
            Log::error('Erro ao abrir formulário de criação: ' . $e->getMessage());
            return redirect()->route('comerciantes.horarios.padrao', $empresa)->with('erro', 'Erro ao abrir formulário');
        }
    }

    /**
     * Salvar horário padrão
     */
    public function storePadrao(Request $request, $empresa)
    {
        try {
            $user = auth('comerciante')->user();

            // Verificar se o comerciante tem permissão para esta empresa
            if (!$user->temPermissaoEmpresa($empresa)) {
                abort(403, 'Acesso negado a esta empresa.');
            }

            $empresaId = $empresa;

            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'dia_semana_id' => 'required|exists:empresa_dias_semana,id',
                'sistema' => 'required|in:TODOS,PDV,FINANCEIRO,ONLINE',
                'aberto' => 'nullable|boolean',
                'hora_abertura' => 'required_if:aberto,1|nullable|date_format:H:i',
                'hora_fechamento' => 'required_if:aberto,1|nullable|date_format:H:i|after:hora_abertura',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $dados = $request->all();
            $dados['empresa_id'] = $empresaId;
            $dados['is_excecao'] = false;
            $dados['aberto'] = $request->has('aberto') ? true : false;

            // Verificar se já existe horário para esse dia/sistema
            $existente = HorarioFuncionamento::porEmpresa($empresaId)
                ->horariosPadrao()
                ->porDiaSemana($dados['dia_semana_id'])
                ->porSistema($dados['sistema'])
                ->first();

            if ($existente) {
                return redirect()->back()
                    ->with('erro', 'Já existe um horário configurado para este dia e sistema')
                    ->withInput();
            }

            HorarioFuncionamento::create($dados);

            return redirect()->route('comerciantes.horarios.padrao', $empresa)
                ->with('sucesso', 'Horário padrão cadastrado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar horário padrão: ' . $e->getMessage());
            return redirect()->back()
                ->with('erro', 'Erro ao salvar horário: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Formulário para editar horário padrão
     */
    public function editPadrao($empresa, $id)
    {
        try {
            $user = auth('comerciante')->user();

            // Verificar se o comerciante tem permissão para esta empresa
            if (!$user->temPermissaoEmpresa($empresa)) {
                abort(403, 'Acesso negado a esta empresa.');
            }

            $empresaId = $empresa;

            $horario = HorarioFuncionamento::porEmpresa($empresaId)
                ->horariosPadrao()
                ->findOrFail($id);

            $diasSemana = DiaSemana::getAll();
            $sistemas = ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'];

            return view('comerciantes.horarios.padrao.edit', compact('horario', 'diasSemana', 'sistemas', 'empresa'));
        } catch (\Exception $e) {
            Log::error('Erro ao abrir edição de horário: ' . $e->getMessage());
            return redirect()->route('comerciantes.horarios.padrao', $empresa)->with('erro', 'Horário não encontrado');
        }
    }

    /**
     * Atualizar horário padrão
     */
    public function updatePadrao(Request $request, $empresa, $id)
    {
        try {
            $user = auth('comerciante')->user();

            // Verificar se o comerciante tem permissão para esta empresa
            if (!$user->temPermissaoEmpresa($empresa)) {
                abort(403, 'Acesso negado a esta empresa.');
            }

            $empresaId = $empresa;

            $horario = HorarioFuncionamento::porEmpresa($empresaId)
                ->horariosPadrao()
                ->findOrFail($id);

            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'dia_semana_id' => 'required|exists:empresa_dias_semana,id',
                'sistema' => 'required|in:TODOS,PDV,FINANCEIRO,ONLINE',
                'aberto' => 'nullable|boolean',
                'hora_abertura' => 'required_if:aberto,1|nullable|date_format:H:i',
                'hora_fechamento' => 'required_if:aberto,1|nullable|date_format:H:i|after:hora_abertura',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $dados = $request->all();
            $dados['aberto'] = $request->has('aberto') ? true : false;

            // Verificar se já existe outro horário para esse dia/sistema
            $existente = HorarioFuncionamento::porEmpresa($empresaId)
                ->horariosPadrao()
                ->porDiaSemana($dados['dia_semana_id'])
                ->porSistema($dados['sistema'])
                ->where('id', '!=', $id)
                ->first();

            if ($existente) {
                return redirect()->back()
                    ->with('erro', 'Já existe outro horário configurado para este dia e sistema')
                    ->withInput();
            }

            $horario->update($dados);

            return redirect()->route('comerciantes.horarios.padrao', $empresa)
                ->with('sucesso', 'Horário padrão atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar horário padrão: ' . $e->getMessage());
            return redirect()->back()
                ->with('erro', 'Erro ao atualizar horário: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Listar exceções
     */
    public function excecoes(Request $request, $empresa)
    {
        try {
            $user = auth('comerciante')->user();

            // Verificar se o comerciante tem permissão para esta empresa
            if (!$user->temPermissaoEmpresa($empresa)) {
                abort(403, 'Acesso negado a esta empresa.');
            }

            $empresaId = $empresa;
            $sistema = $request->get('sistema');

            $excecoes = HorarioFuncionamento::porEmpresa($empresaId)
                ->excecoes()
                ->ativo()
                ->when($sistema, function ($query) use ($sistema) {
                    return $query->porSistema($sistema);
                })
                ->orderBy('data_excecao', 'desc')
                ->get();

            $sistemas = ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'];

            return view('comerciantes.horarios.excecoes.index', compact(
                'excecoes',
                'sistemas',
                'sistema',
                'empresa'
            ));
        } catch (\Exception $e) {
            Log::error('Erro ao listar exceções: ' . $e->getMessage());
            return redirect()->back()->with('erro', 'Erro ao carregar exceções');
        }
    }

    /**
     * Formulário para criar exceção
     */
    public function createExcecao($empresa)
    {
        try {
            // Verificar se o comerciante tem permissão para esta empresa
            if (!EmpresaUsuario::temPermissaoEmpresa(auth('comerciante')->id(), $empresa)) {
                return response()->json(['error' => 'Sem permissão para acessar esta empresa'], 403);
            }

            $sistemas = ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'];

            return view('comerciantes.horarios.excecoes.create', compact('sistemas', 'empresa'));
        } catch (\Exception $e) {
            Log::error('Erro ao abrir formulário de exceção: ' . $e->getMessage());
            return redirect()->route('comerciantes.horarios.excecoes', $empresa)->with('erro', 'Erro ao abrir formulário');
        }
    }

    /**
     * Salvar exceção
     */
    public function storeExcecao(Request $request, $empresa)
    {
        try {
            // Verificar se o comerciante tem permissão para esta empresa
            if (!EmpresaUsuario::temPermissaoEmpresa(auth('comerciante')->id(), $empresa)) {
                return response()->json(['error' => 'Sem permissão para acessar esta empresa'], 403);
            }

            $empresaId = $empresa;

            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'data_excecao' => 'required|date|after_or_equal:today',
                'sistema' => 'required|in:TODOS,PDV,FINANCEIRO,ONLINE',
                'aberto' => 'nullable|boolean',
                'hora_abertura' => 'required_if:aberto,1|nullable|date_format:H:i',
                'hora_fechamento' => 'required_if:aberto,1|nullable|date_format:H:i|after:hora_abertura',
                'descricao_excecao' => 'required|string|max:255',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $dados = $request->all();
            $dados['empresa_id'] = $empresaId;
            $dados['is_excecao'] = true;
            $dados['dia_semana_id'] = null;
            $dados['aberto'] = $request->has('aberto') ? true : false;

            // Verificar se já existe exceção para essa data/sistema
            $existente = HorarioFuncionamento::porEmpresa($empresaId)
                ->excecoes()
                ->where('data_excecao', $dados['data_excecao'])
                ->porSistema($dados['sistema'])
                ->first();

            if ($existente) {
                return redirect()->back()
                    ->with('erro', 'Já existe uma exceção configurada para esta data e sistema')
                    ->withInput();
            }

            HorarioFuncionamento::create($dados);

            return redirect()->route('comerciantes.horarios.excecoes', $empresa)
                ->with('sucesso', 'Exceção cadastrada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar exceção: ' . $e->getMessage());
            return redirect()->back()
                ->with('erro', 'Erro ao salvar exceção: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Formulário para editar exceção
     */
    public function editExcecao($empresa, $id)
    {
        try {
            // Verificar se o comerciante tem permissão para esta empresa
            if (!EmpresaUsuario::temPermissaoEmpresa(auth('comerciante')->id(), $empresa)) {
                return response()->json(['error' => 'Sem permissão para acessar esta empresa'], 403);
            }

            $empresaId = $empresa;

            $excecao = HorarioFuncionamento::porEmpresa($empresaId)
                ->excecoes()
                ->findOrFail($id);

            $sistemas = ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'];

            return view('comerciantes.horarios.excecoes.edit', compact('excecao', 'sistemas', 'empresa'));
        } catch (\Exception $e) {
            Log::error('Erro ao abrir edição de exceção: ' . $e->getMessage());
            return redirect()->route('comerciantes.horarios.excecoes', $empresa)->with('erro', 'Exceção não encontrada');
        }
    }

    /**
     * Atualizar exceção
     */
    public function updateExcecao(Request $request, $empresa, $id)
    {
        try {
            // Verificar se o comerciante tem permissão para esta empresa
            if (!EmpresaUsuario::temPermissaoEmpresa(auth('comerciante')->id(), $empresa)) {
                return response()->json(['error' => 'Sem permissão para acessar esta empresa'], 403);
            }

            $empresaId = $empresa;

            $excecao = HorarioFuncionamento::porEmpresa($empresaId)
                ->excecoes()
                ->findOrFail($id);

            // Validação dos dados
            $validator = Validator::make($request->all(), [
                'data_excecao' => 'required|date|after_or_equal:today',
                'sistema' => 'required|in:TODOS,PDV,FINANCEIRO,ONLINE',
                'aberto' => 'nullable|boolean',
                'hora_abertura' => 'required_if:aberto,1|nullable|date_format:H:i',
                'hora_fechamento' => 'required_if:aberto,1|nullable|date_format:H:i|after:hora_abertura',
                'descricao_excecao' => 'required|string|max:255',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $dados = $request->all();
            $dados['aberto'] = $request->has('aberto') ? true : false;

            // Verificar se já existe outra exceção para essa data/sistema
            $existente = HorarioFuncionamento::porEmpresa($empresaId)
                ->excecoes()
                ->where('data_excecao', $dados['data_excecao'])
                ->porSistema($dados['sistema'])
                ->where('id', '!=', $id)
                ->first();

            if ($existente) {
                return redirect()->back()
                    ->with('erro', 'Já existe outra exceção configurada para esta data e sistema')
                    ->withInput();
            }

            $excecao->update($dados);

            return redirect()->route('comerciantes.horarios.excecoes', $empresa)
                ->with('sucesso', 'Exceção atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar exceção: ' . $e->getMessage());
            return redirect()->back()
                ->with('erro', 'Erro ao atualizar exceção: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Deletar horário ou exceção
     */
    public function destroy($empresa, $id)
    {
        try {
            // Verificar se o comerciante tem permissão para esta empresa
            if (!EmpresaUsuario::temPermissaoEmpresa(auth('comerciante')->id(), $empresa)) {
                return response()->json(['error' => 'Sem permissão para acessar esta empresa'], 403);
            }

            $empresaId = $empresa;

            $horario = HorarioFuncionamento::porEmpresa($empresaId)->findOrFail($id);
            $isExcecao = $horario->is_excecao;

            $horario->delete();

            $mensagem = $isExcecao ? 'Exceção removida com sucesso!' : 'Horário removido com sucesso!';
            $rota = $isExcecao ? 'comerciantes.horarios.excecoes' : 'comerciantes.horarios.padrao';

            return redirect()->route($rota)->with('sucesso', $mensagem);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar horário: ' . $e->getMessage());
            return redirect()->back()->with('erro', 'Erro ao remover registro');
        }
    }

    /**
     * API - Status atual
     */
    public function apiStatus(Request $request, $empresa)
    {
        try {
            // Verificar se o comerciante tem permissão para esta empresa
            if (!EmpresaUsuario::temPermissaoEmpresa(auth('comerciante')->id(), $empresa)) {
                return response()->json(['error' => 'Sem permissão para acessar esta empresa'], 403);
            }

            $empresaId = $empresa;
            $sistema = $request->get('sistema', 'TODOS');

            $status = HorarioFuncionamento::getStatusHoje($empresaId, $sistema);

            return response()->json([
                'success' => true,
                'data' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * API - Próximo funcionamento
     */
    public function apiProximoAberto(Request $request, $empresa)
    {
        try {
            // Verificar se o comerciante tem permissão para esta empresa
            if (!EmpresaUsuario::temPermissaoEmpresa(auth('comerciante')->id(), $empresa)) {
                return response()->json(['error' => 'Sem permissão para acessar esta empresa'], 403);
            }

            $empresaId = $empresa;
            $sistema = $request->get('sistema', 'TODOS');

            $proximo = HorarioFuncionamento::getProximoDiaAberto($empresaId, $sistema);

            return response()->json([
                'success' => true,
                'data' => $proximo
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Relatório completo
     */
    public function relatorio(Request $request, $empresa)
    {
        try {
            // Verificar se o comerciante tem permissão para esta empresa
            if (!EmpresaUsuario::temPermissaoEmpresa(auth('comerciante')->id(), $empresa)) {
                return response()->json(['error' => 'Sem permissão para acessar esta empresa'], 403);
            }

            $empresaId = $empresa;

            $relatorio = HorarioFuncionamento::getRelatorioStatus($empresaId);

            return view('comerciantes.horarios.relatorio', compact('relatorio', 'empresa'));
        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório: ' . $e->getMessage());
            return redirect()->back()->with('erro', 'Erro ao gerar relatório');
        }
    }

    // ============= MÉTODOS SEM EMPRESA NA URL (DETECTAM AUTOMATICAMENTE) =============

    /**
     * Index sem empresa na URL - detecta automaticamente
     */
    public function indexSemEmpresa(Request $request)
    {
        try {
            $user = auth('comerciante')->user();

            // Obter empresa do usuário
            $empresaId = $this->getEmpresaId();

            // Verificar se tem permissão
            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            // Redirecionar para a rota específica da empresa
            return redirect()->route('comerciantes.empresas.horarios.index', $empresaId);
        } catch (\Exception $e) {
            Log::error('Erro ao acessar horários: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao acessar horários: ' . $e->getMessage());
        }
    }

    /**
     * Horários padrão sem empresa na URL
     */
    public function horariosPadraoSemEmpresa(Request $request)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return redirect()->route('comerciantes.empresas.horarios.padrao.index', $empresaId);
        } catch (\Exception $e) {
            Log::error('Erro ao acessar horários padrão: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao acessar horários padrão: ' . $e->getMessage());
        }
    }

    /**
     * Criar horário padrão sem empresa na URL
     */
    public function createPadraoSemEmpresa()
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return redirect()->route('comerciantes.empresas.horarios.padrao.create', $empresaId);
        } catch (\Exception $e) {
            Log::error('Erro ao criar horário padrão: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao criar horário padrão: ' . $e->getMessage());
        }
    }

    /**
     * Salvar horário padrão sem empresa na URL
     */
    public function storePadraoSemEmpresa(Request $request)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return $this->storePadrao($request, $empresaId);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar horário padrão: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao salvar horário padrão: ' . $e->getMessage());
        }
    }

    /**
     * Editar horário padrão sem empresa na URL
     */
    public function editPadraoSemEmpresa($id)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return redirect()->route('comerciantes.empresas.horarios.padrao.edit', [$empresaId, $id]);
        } catch (\Exception $e) {
            Log::error('Erro ao editar horário padrão: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao editar horário padrão: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar horário padrão sem empresa na URL
     */
    public function updatePadraoSemEmpresa(Request $request, $id)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return $this->updatePadrao($request, $empresaId, $id);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar horário padrão: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao atualizar horário padrão: ' . $e->getMessage());
        }
    }

    /**
     * Exceções sem empresa na URL
     */
    public function excecoesSemEmpresa(Request $request)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return redirect()->route('comerciantes.empresas.horarios.excecoes.index', $empresaId);
        } catch (\Exception $e) {
            Log::error('Erro ao acessar exceções: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao acessar exceções: ' . $e->getMessage());
        }
    }

    /**
     * Criar exceção sem empresa na URL
     */
    public function createExcecaoSemEmpresa()
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return redirect()->route('comerciantes.empresas.horarios.excecoes.create', $empresaId);
        } catch (\Exception $e) {
            Log::error('Erro ao criar exceção: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao criar exceção: ' . $e->getMessage());
        }
    }

    /**
     * Salvar exceção sem empresa na URL
     */
    public function storeExcecaoSemEmpresa(Request $request)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return $this->storeExcecao($request, $empresaId);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar exceção: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao salvar exceção: ' . $e->getMessage());
        }
    }

    /**
     * Editar exceção sem empresa na URL
     */
    public function editExcecaoSemEmpresa($id)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return redirect()->route('comerciantes.empresas.horarios.excecoes.edit', [$empresaId, $id]);
        } catch (\Exception $e) {
            Log::error('Erro ao editar exceção: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao editar exceção: ' . $e->getMessage());
        }
    }

    /**
     * Atualizar exceção sem empresa na URL
     */
    public function updateExcecaoSemEmpresa(Request $request, $id)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return $this->updateExcecao($request, $empresaId, $id);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar exceção: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao atualizar exceção: ' . $e->getMessage());
        }
    }

    /**
     * Deletar sem empresa na URL
     */
    public function destroySemEmpresa($id)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return $this->destroy($empresaId, $id);
        } catch (\Exception $e) {
            Log::error('Erro ao deletar: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao deletar: ' . $e->getMessage());
        }
    }

    /**
     * API Status sem empresa na URL
     */
    public function apiStatusSemEmpresa(Request $request)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return response()->json(['error' => 'Sem permissão'], 403);
            }

            return $this->apiStatus($request, $empresaId);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * API Próximo aberto sem empresa na URL
     */
    public function apiProximoAbertoSemEmpresa(Request $request)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return response()->json(['error' => 'Sem permissão'], 403);
            }

            return $this->apiProximoAberto($request, $empresaId);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Relatório sem empresa na URL
     */
    public function relatorioSemEmpresa(Request $request)
    {
        try {
            $user = auth('comerciante')->user();
            $empresaId = $this->getEmpresaId();

            if (!$user->temPermissaoEmpresa($empresaId)) {
                return redirect()->route('comerciantes.empresas.index')
                    ->with('erro', 'Você não tem permissão para acessar esta empresa.');
            }

            return $this->relatorio($request, $empresaId);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar relatório: ' . $e->getMessage());
            return redirect()->route('comerciantes.dashboard')
                ->with('erro', 'Erro ao gerar relatório: ' . $e->getMessage());
        }
    }

    // ============= MÉTODOS AUXILIARES =============

    /**
     * Obter ID da empresa do usuário logado
     */
    private function getEmpresaId()
    {
        $user = Auth::guard('comerciante')->user();

        if (!$user) {
            return 1; // Empresa padrão para desenvolvimento
        }

        // Primeira tentativa: empresa selecionada na sessão
        if (session('empresa_atual_id')) {
            return session('empresa_atual_id');
        }

        // Segunda tentativa: empresa do usuário
        if ($user->empresa_id) {
            return $user->empresa_id;
        }

        // Terceira tentativa: primeira empresa vinculada ao usuário
        if ($user->todas_empresas && $user->todas_empresas->count() > 0) {
            return $user->todas_empresas->first()->id;
        }

        // Fallback: empresa padrão
        return 1;
    }
}
