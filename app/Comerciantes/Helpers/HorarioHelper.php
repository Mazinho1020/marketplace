<?php

namespace App\Comerciantes\Helpers;

use Carbon\Carbon;

class HorarioHelper
{
    /**
     * Formata hora para exibição (HH:MM)
     */
    public static function formatarHora($hora)
    {
        if (empty($hora)) return '-';

        try {
            return Carbon::parse($hora)->format('H:i');
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Formata data para exibição (dd/mm/yyyy)
     */
    public static function formatarData($data)
    {
        if (empty($data)) return '-';

        try {
            return Carbon::parse($data)->format('d/m/Y');
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Formata data e hora para exibição
     */
    public static function formatarDataHora($dataHora)
    {
        if (empty($dataHora)) return '-';

        try {
            return Carbon::parse($dataHora)->format('d/m/Y H:i');
        } catch (\Exception $e) {
            return '-';
        }
    }

    /**
     * Retorna classe CSS baseada no status de funcionamento
     */
    public static function getClasseStatus($aberto)
    {
        return $aberto ? 'text-success' : 'text-danger';
    }

    /**
     * Retorna ícone baseado no status de funcionamento
     */
    public static function getIconeStatus($aberto)
    {
        return $aberto ? 'fas fa-unlock text-success' : 'fas fa-lock text-danger';
    }

    /**
     * Converte número do dia da semana para nome em português
     */
    public static function getDiaSemana($numero)
    {
        $dias = [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            7 => 'Domingo'
        ];

        return $dias[$numero] ?? 'Desconhecido';
    }

    /**
     * Gera opções para select de sistemas
     */
    public static function getOpcoesSistema($selecionado = null)
    {
        $sistemas = [
            'TODOS' => 'Todos os Sistemas',
            'PDV' => 'PDV (Ponto de Venda)',
            'FINANCEIRO' => 'Sistema Financeiro',
            'ONLINE' => 'Loja Online'
        ];

        $options = '';
        foreach ($sistemas as $valor => $nome) {
            $selected = ($selecionado === $valor) ? 'selected' : '';
            $options .= "<option value=\"{$valor}\" {$selected}>{$nome}</option>";
        }

        return $options;
    }

    /**
     * Calcula diferença entre duas horas em formato HH:MM
     */
    public static function calcularDiferencaHoras($inicio, $fim)
    {
        if (empty($inicio) || empty($fim)) return null;

        try {
            $inicioCarbon = Carbon::parse($inicio);
            $fimCarbon = Carbon::parse($fim);

            $diferenca = $inicioCarbon->diff($fimCarbon);

            return $diferenca->format('%H:%I');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Verifica se horário atual está dentro do período
     */
    public static function estaDentroPeriodo($hora, $inicio, $fim)
    {
        if (empty($hora) || empty($inicio) || empty($fim)) return false;

        try {
            $horaCarbon = Carbon::parse($hora);
            $inicioCarbon = Carbon::parse($inicio);
            $fimCarbon = Carbon::parse($fim);

            return $horaCarbon->between($inicioCarbon, $fimCarbon);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gera resumo do status para múltiplos sistemas
     */
    public static function gerarResumoStatus($relatorio)
    {
        $resumo = [];

        foreach ($relatorio as $sistema => $dados) {
            $status = $dados['status_hoje'];
            $resumo[$sistema] = [
                'sistema' => $sistema,
                'aberto' => $status['aberto'],
                'mensagem' => $status['mensagem'],
                'classe_css' => self::getClasseStatus($status['aberto']),
                'icone' => self::getIconeStatus($status['aberto'])
            ];
        }

        return $resumo;
    }

    /**
     * Valida se data é válida e futura
     */
    public static function validarDataFutura($data)
    {
        if (empty($data)) return false;

        try {
            $dataCarbon = Carbon::parse($data);
            return $dataCarbon->isFuture() || $dataCarbon->isToday();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Gera array de datas para próximos N dias
     */
    public static function gerarProximasDatas($dias = 30)
    {
        $datas = [];
        $hoje = Carbon::now();

        for ($i = 0; $i < $dias; $i++) {
            $data = $hoje->copy()->addDays($i);

            $datas[] = [
                'valor' => $data->format('Y-m-d'),
                'texto' => $data->format('d/m/Y') . ' (' . self::getDiaSemana($data->dayOfWeekIso) . ')',
                'carbon' => $data
            ];
        }

        return $datas;
    }

    /**
     * Retorna cor do badge baseada no sistema
     */
    public static function getCorSistema($sistema)
    {
        $cores = [
            'TODOS' => 'primary',
            'PDV' => 'success',
            'FINANCEIRO' => 'info',
            'ONLINE' => 'warning'
        ];

        return $cores[$sistema] ?? 'secondary';
    }

    /**
     * Retorna ícone do sistema
     */
    public static function getIconeSistema($sistema)
    {
        $icones = [
            'TODOS' => 'fas fa-building',
            'PDV' => 'fas fa-cash-register',
            'FINANCEIRO' => 'fas fa-chart-line',
            'ONLINE' => 'fas fa-globe'
        ];

        return $icones[$sistema] ?? 'fas fa-question';
    }

    /**
     * Formata mensagem de status de forma amigável
     */
    public static function formatarMensagemStatus($status)
    {
        if (!$status['aberto']) {
            return [
                'texto' => 'Fechado',
                'classe' => 'danger',
                'icone' => 'fa-lock'
            ];
        }

        return [
            'texto' => 'Aberto',
            'classe' => 'success',
            'icone' => 'fa-unlock'
        ];
    }

    /**
     * Gera mensagem de próximo funcionamento
     */
    public static function formatarProximoFuncionamento($proximo)
    {
        if (!$proximo) {
            return 'Horário não configurado';
        }

        $data = Carbon::parse($proximo['data']);
        $hoje = Carbon::today();

        if ($data->isToday()) {
            return 'Hoje às ' . self::formatarHora($proximo['hora_abertura']);
        }

        if ($data->isTomorrow()) {
            return 'Amanhã às ' . self::formatarHora($proximo['hora_abertura']);
        }

        $diffDias = $hoje->diffInDays($data);

        if ($diffDias <= 7) {
            return $data->format('l') . ' às ' . self::formatarHora($proximo['hora_abertura']);
        }

        return $data->format('d/m/Y') . ' às ' . self::formatarHora($proximo['hora_abertura']);
    }

    /**
     * Verifica se é horário de funcionamento
     */
    public static function isHorarioFuncionamento($empresaId, $sistema = 'TODOS')
    {
        try {
            // Esta função seria implementada usando o model HorarioFuncionamento
            // Por enquanto retorna true para não quebrar a aplicação
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
