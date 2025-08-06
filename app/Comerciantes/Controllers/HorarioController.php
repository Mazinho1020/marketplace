<?php

namespace App\Comerciantes\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HorarioFuncionamento;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class HorarioController extends Controller
{
    /**
     * Verifica se usuário tem acesso à empresa
     */
    private function verificarAcesso($empresaId)
    {
        $user = auth('comerciante')->user();

        if (!$user) {
            abort(403, 'Acesso negado.');
        }

        return true;
    }    // ============= DASHBOARD PRINCIPAL =============

    /**
     * Dashboard principal dos horários
     */
    public function index($empresaId)
    {
        try {
            $this->verificarAcesso($empresaId);

            $empresa = Empresa::findOrFail($empresaId);

            // Buscar horários padrão
            $horariosPadrao = HorarioFuncionamento::porEmpresa($empresaId)
                ->padrao()
                ->ativos()
                ->orderBy('dia_semana')
                ->get();

            // Buscar próximas exceções (próximos 30 dias)
            $proximasExcecoes = HorarioFuncionamento::porEmpresa($empresaId)
                ->excecoes()
                ->where('data_especifica', '>=', now()->toDateString())
                ->where('data_especifica', '<=', now()->addDays(30)->toDateString())
                ->ativos()
                ->orderBy('data_especifica')
                ->get();

            // Status atual
            $horarioAtual = HorarioFuncionamento::horarioParaHoje($empresaId);

            $sistemas = HorarioFuncionamento::getSistemas();
            $diasSemana = HorarioFuncionamento::getDiasSemana();

            return view('comerciantes.horarios.index', compact(
                'empresaId',
                'empresa',
                'horariosPadrao',
                'proximasExcecoes',
                'horarioAtual',
                'sistemas',
                'diasSemana'
            ));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar horários: ' . $e->getMessage());
            return redirect()->route('comerciantes.empresas.index')
                ->with('erro', 'Erro ao carregar horários de funcionamento: ' . $e->getMessage());
        }
    }

    // ============= HORÁRIOS PADRÃO =============

    /**
     * Lista horários padrão
     */
    public function padrao($empresaId)
    {
        try {
            $this->verificarAcesso($empresaId);

            $empresa = Empresa::findOrFail($empresaId);

            $horarios = HorarioFuncionamento::porEmpresa($empresaId)
                ->padrao()
                ->ativos()
                ->orderBy('dia_semana')
                ->orderBy('sistema')
                ->get();

            $sistemas = HorarioFuncionamento::getSistemas();
            $diasSemana = HorarioFuncionamento::getDiasSemana();

            return view('comerciantes.horarios.padrao.index', compact(
                'empresaId',
                'empresa',
                'horarios',
                'sistemas',
                'diasSemana'
            ));
        } catch (\Exception $e) {
            Log::error('Erro ao carregar horários padrão: ' . $e->getMessage());
            return redirect()->route('comerciantes.horarios.index', $empresaId)
                ->with('erro', 'Erro ao carregar horários padrão.');
        }
    }

    /**
     * Formulário para criar horário padrão
     */
    public function criarPadrao($empresaId)
    {
        try {
            $this->verificarAcesso($empresaId);

            $empresa = Empresa::findOrFail($empresaId);
            $sistemas = HorarioFuncionamento::getSistemas();
            $diasSemana = HorarioFuncionamento::getDiasSemana();

            return view('comerciantes.horarios.padrao.create', compact(
                'empresaId',
                'empresa',
                'sistemas',
                'diasSemana'
            ));
        } catch (\Exception $e) {
            return redirect()->route('comerciantes.horarios.padrao.index', $empresaId)
                ->with('erro', 'Erro ao carregar formulário.');
        }
    }

    /**
     * Salvar horário padrão
     */
    public function salvarPadrao(Request $request, $empresaId)
    {
        try {
            $this->verificarAcesso($empresaId);

            // Validação
            $validator = Validator::make($request->all(), [
                'dia_semana' => 'required|integer|between:1,7',
                'sistema' => 'required|string|in:TODOS,PDV,ONLINE,FINANCEIRO',
                'fechado' => 'boolean',
                'hora_abertura' => 'required_if:fechado,false|nullable|date_format:H:i',
                'hora_fechamento' => 'required_if:fechado,false|nullable|date_format:H:i|after:hora_abertura',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Verificar se já existe
            $existente = HorarioFuncionamento::porEmpresa($empresaId)
                ->padrao()
                ->porDiaSemana($request->dia_semana)
                ->porSistema($request->sistema)
                ->ativos()
                ->first();

            if ($existente) {
                return redirect()->back()
                    ->with('erro', 'Já existe um horário para este dia e sistema.')
                    ->withInput();
            }

            // Criar horário
            HorarioFuncionamento::create([
                'empresa_id' => $empresaId,
                'tipo' => 'padrao',
                'dia_semana' => $request->dia_semana,
                'sistema' => $request->sistema,
                'fechado' => $request->has('fechado'),
                'hora_abertura' => $request->has('fechado') ? null : $request->hora_abertura,
                'hora_fechamento' => $request->has('fechado') ? null : $request->hora_fechamento,
                'observacoes' => $request->observacoes,
                'ativo' => true
            ]);

            return redirect()->route('comerciantes.horarios.padrao.index', $empresaId)
                ->with('sucesso', 'Horário padrão criado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar horário padrão: ' . $e->getMessage());
            return redirect()->back()
                ->with('erro', 'Erro ao salvar horário. Tente novamente.')
                ->withInput();
        }
    }

    /**
     * Formulário para editar horário padrão
     */
    public function editarPadrao($empresaId, $id)
    {
        try {
            $this->verificarAcesso($empresaId);

            $empresa = Empresa::findOrFail($empresaId);
            $horario = HorarioFuncionamento::porEmpresa($empresaId)
                ->where('id', $id)
                ->firstOrFail();

            $sistemas = HorarioFuncionamento::getSistemas();
            $diasSemana = HorarioFuncionamento::getDiasSemana();

            return view('comerciantes.horarios.padrao.edit', compact(
                'empresaId',
                'empresa',
                'horario',
                'sistemas',
                'diasSemana'
            ));
        } catch (\Exception $e) {
            return redirect()->route('comerciantes.horarios.padrao.index', $empresaId)
                ->with('erro', 'Horário não encontrado.');
        }
    }

    /**
     * Atualizar horário padrão
     */
    public function atualizarPadrao(Request $request, $empresaId, $id)
    {
        try {
            $this->verificarAcesso($empresaId);

            $horario = HorarioFuncionamento::porEmpresa($empresaId)
                ->where('id', $id)
                ->firstOrFail();

            // Validação
            $validator = Validator::make($request->all(), [
                'dia_semana' => 'required|integer|between:1,7',
                'sistema' => 'required|string|in:TODOS,PDV,ONLINE,FINANCEIRO',
                'fechado' => 'boolean',
                'hora_abertura' => 'required_if:fechado,false|nullable|date_format:H:i',
                'hora_fechamento' => 'required_if:fechado,false|nullable|date_format:H:i|after:hora_abertura',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Verificar duplicatas (exceto o atual)
            $existente = HorarioFuncionamento::porEmpresa($empresaId)
                ->padrao()
                ->porDiaSemana($request->dia_semana)
                ->porSistema($request->sistema)
                ->where('id', '!=', $id)
                ->ativos()
                ->first();

            if ($existente) {
                return redirect()->back()
                    ->with('erro', 'Já existe um horário para este dia e sistema.')
                    ->withInput();
            }

            // Atualizar
            $horario->update([
                'dia_semana' => $request->dia_semana,
                'sistema' => $request->sistema,
                'fechado' => $request->has('fechado'),
                'hora_abertura' => $request->has('fechado') ? null : $request->hora_abertura,
                'hora_fechamento' => $request->has('fechado') ? null : $request->hora_fechamento,
                'observacoes' => $request->observacoes
            ]);

            return redirect()->route('comerciantes.horarios.padrao.index', $empresaId)
                ->with('sucesso', 'Horário atualizado com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar horário: ' . $e->getMessage());
            return redirect()->back()
                ->with('erro', 'Erro ao atualizar horário. Tente novamente.')
                ->withInput();
        }
    }

    // ============= EXCEÇÕES =============

    /**
     * Lista exceções
     */
    public function excecoes($empresaId)
    {
        try {
            $this->verificarAcesso($empresaId);

            $empresa = Empresa::findOrFail($empresaId);

            $excecoes = HorarioFuncionamento::porEmpresa($empresaId)
                ->excecoes()
                ->ativos()
                ->orderBy('data_especifica', 'desc')
                ->get();

            $sistemas = HorarioFuncionamento::getSistemas();

            return view('comerciantes.horarios.excecoes.index', compact(
                'empresaId',
                'empresa',
                'excecoes',
                'sistemas'
            ));
        } catch (\Exception $e) {
            return redirect()->route('comerciantes.horarios.index', $empresaId)
                ->with('erro', 'Erro ao carregar exceções.');
        }
    }

    /**
     * Formulário para criar exceção
     */
    public function criarExcecao($empresaId)
    {
        try {
            $this->verificarAcesso($empresaId);

            $empresa = Empresa::findOrFail($empresaId);
            $sistemas = HorarioFuncionamento::getSistemas();

            return view('comerciantes.horarios.excecoes.create', compact(
                'empresaId',
                'empresa',
                'sistemas'
            ));
        } catch (\Exception $e) {
            return redirect()->route('comerciantes.horarios.excecoes.index', $empresaId)
                ->with('erro', 'Erro ao carregar formulário.');
        }
    }

    /**
     * Salvar exceção
     */
    public function salvarExcecao(Request $request, $empresaId)
    {
        try {
            $this->verificarAcesso($empresaId);

            // Validação
            $validator = Validator::make($request->all(), [
                'data_especifica' => 'required|date|after_or_equal:today',
                'sistema' => 'required|string|in:TODOS,PDV,ONLINE,FINANCEIRO',
                'fechado' => 'boolean',
                'hora_abertura' => 'required_if:fechado,false|nullable|date_format:H:i',
                'hora_fechamento' => 'required_if:fechado,false|nullable|date_format:H:i|after:hora_abertura',
                'observacoes' => 'nullable|string|max:500'
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Verificar se já existe
            $existente = HorarioFuncionamento::porEmpresa($empresaId)
                ->excecoes()
                ->where('data_especifica', $request->data_especifica)
                ->porSistema($request->sistema)
                ->ativos()
                ->first();

            if ($existente) {
                return redirect()->back()
                    ->with('erro', 'Já existe uma exceção para esta data e sistema.')
                    ->withInput();
            }

            // Criar exceção
            HorarioFuncionamento::create([
                'empresa_id' => $empresaId,
                'tipo' => 'excecao',
                'data_especifica' => $request->data_especifica,
                'sistema' => $request->sistema,
                'fechado' => $request->has('fechado'),
                'hora_abertura' => $request->has('fechado') ? null : $request->hora_abertura,
                'hora_fechamento' => $request->has('fechado') ? null : $request->hora_fechamento,
                'observacoes' => $request->observacoes,
                'ativo' => true
            ]);

            return redirect()->route('comerciantes.horarios.excecoes.index', $empresaId)
                ->with('sucesso', 'Exceção criada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao salvar exceção: ' . $e->getMessage());
            return redirect()->back()
                ->with('erro', 'Erro ao salvar exceção. Tente novamente.')
                ->withInput();
        }
    }

    // ============= AÇÕES GERAIS =============

    /**
     * Deletar horário
     */
    public function deletar($empresaId, $id)
    {
        try {
            $this->verificarAcesso($empresaId);

            $horario = HorarioFuncionamento::porEmpresa($empresaId)
                ->where('id', $id)
                ->firstOrFail();

            $tipo = $horario->tipo;
            $horario->delete();

            $rota = $tipo === 'padrao' ? 'comerciantes.horarios.padrao.index' : 'comerciantes.horarios.excecoes.index';

            return redirect()->route($rota, $empresaId)
                ->with('sucesso', 'Horário removido com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao deletar horário: ' . $e->getMessage());
            return redirect()->back()
                ->with('erro', 'Erro ao remover horário.');
        }
    }

    // ============= API =============

    /**
     * Status atual da empresa
     */
    public function apiStatus($empresaId)
    {
        try {
            $this->verificarAcesso($empresaId);

            $horarioAtual = HorarioFuncionamento::horarioParaHoje($empresaId);

            $data = [
                'empresa_id' => $empresaId,
                'data_hora' => now()->toDateTimeString(),
                'horario_atual' => $horarioAtual ? [
                    'tipo' => $horarioAtual->tipo,
                    'sistema' => $horarioAtual->sistema,
                    'fechado' => $horarioAtual->fechado,
                    'horario_formatado' => $horarioAtual->horario_formatado,
                    'esta_aberto' => $horarioAtual->estaAberto(),
                ] : null,
                'esta_funcionando' => $horarioAtual ? $horarioAtual->estaAberto() : false
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error('Erro na API de status: ' . $e->getMessage());
            return response()->json(['erro' => 'Erro interno'], 500);
        }
    }
}
