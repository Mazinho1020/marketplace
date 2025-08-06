<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HorarioFuncionamento extends Model
{
    use HasFactory;

    protected $table = 'horarios_funcionamento';

    protected $fillable = [
        'empresa_id',
        'tipo',
        'dia_semana',
        'data_especifica',
        'hora_abertura',
        'hora_fechamento',
        'fechado',
        'sistema',
        'observacoes',
        'ativo'
    ];

    protected $casts = [
        'data_especifica' => 'date',
        'fechado' => 'boolean',
        'ativo' => 'boolean',
    ];

    // ============= RELACIONAMENTOS =============

    /**
     * Relacionamento com Empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    // ============= SCOPES =============

    /**
     * Scope para horários de uma empresa específica
     */
    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    /**
     * Scope para horários padrão
     */
    public function scopePadrao($query)
    {
        return $query->where('tipo', 'padrao');
    }

    /**
     * Scope para exceções
     */
    public function scopeExcecoes($query)
    {
        return $query->where('tipo', 'excecao');
    }

    /**
     * Scope por sistema
     */
    public function scopePorSistema($query, $sistema)
    {
        return $query->where('sistema', $sistema);
    }

    /**
     * Scope por dia da semana
     */
    public function scopePorDiaSemana($query, $diaSemana)
    {
        return $query->where('dia_semana', $diaSemana);
    }

    /**
     * Scope para horários ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    // ============= MÉTODOS AUXILIARES =============

    /**
     * Retorna o nome do dia da semana
     */
    public function getNomeDiaSemanaAttribute()
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

        return $dias[$this->dia_semana] ?? 'N/A';
    }

    /**
     * Retorna o horário formatado
     */
    public function getHorarioFormatadoAttribute()
    {
        if ($this->fechado) {
            return 'Fechado';
        }

        if (!$this->hora_abertura || !$this->hora_fechamento) {
            return 'Não definido';
        }

        return $this->hora_abertura . ' às ' . $this->hora_fechamento;
    }

    /**
     * Verifica se está aberto no momento
     */
    public function estaAberto($agora = null)
    {
        if ($this->fechado) {
            return false;
        }

        if (!$this->hora_abertura || !$this->hora_fechamento) {
            return false;
        }

        $agora = $agora ?: now();
        $horaAtual = $agora->format('H:i:s');

        return $horaAtual >= $this->hora_abertura && $horaAtual <= $this->hora_fechamento;
    }

    // ============= MÉTODOS ESTÁTICOS =============

    /**
     * Retorna os sistemas disponíveis
     */
    public static function getSistemas()
    {
        return ['TODOS', 'PDV', 'ONLINE', 'FINANCEIRO'];
    }

    /**
     * Retorna os dias da semana
     */
    public static function getDiasSemana()
    {
        return [
            1 => 'Segunda-feira',
            2 => 'Terça-feira',
            3 => 'Quarta-feira',
            4 => 'Quinta-feira',
            5 => 'Sexta-feira',
            6 => 'Sábado',
            7 => 'Domingo'
        ];
    }

    /**
     * Busca horário para hoje
     */
    public static function horarioParaHoje($empresaId, $sistema = 'TODOS')
    {
        $hoje = now();
        $diaSemana = $hoje->dayOfWeek == 0 ? 7 : $hoje->dayOfWeek; // Ajustar domingo

        // Primeiro verifica se há exceção para hoje
        $excecao = self::porEmpresa($empresaId)
            ->excecoes()
            ->where('data_especifica', $hoje->toDateString())
            ->porSistema($sistema)
            ->ativo()
            ->first();

        if ($excecao) {
            return $excecao;
        }

        // Se não há exceção, busca horário padrão
        return self::porEmpresa($empresaId)
            ->padrao()
            ->porDiaSemana($diaSemana)
            ->porSistema($sistema)
            ->ativo()
            ->first();
    }
}
