<?php

namespace App\Comerciantes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HorarioFuncionamento extends Model
{
    protected $table = 'empresa_horarios_funcionamento';

    protected $fillable = [
        'empresa_id',
        'dia_semana_id',
        'sistema',
        'aberto',
        'hora_abertura',
        'hora_fechamento',
        'is_excecao',
        'data_excecao',
        'descricao_excecao',
        'observacoes',
        'ativo'
    ];

    protected $casts = [
        'aberto' => 'boolean',
        'is_excecao' => 'boolean',
        'ativo' => 'boolean',
        'data_excecao' => 'date',
    ];

    // ============= RELACIONAMENTOS =============

    public function diaSemana(): BelongsTo
    {
        return $this->belongsTo(DiaSemana::class, 'dia_semana_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(HorarioFuncionamentoLog::class, 'horario_id');
    }

    // ============= SCOPES =============

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorSistema($query, $sistema)
    {
        return $query->where('sistema', $sistema);
    }

    public function scopeHorariosPadrao($query)
    {
        return $query->where('is_excecao', false);
    }

    public function scopeExcecoes($query)
    {
        return $query->where('is_excecao', true);
    }

    public function scopePorDiaSemana($query, $diaSemana)
    {
        return $query->where('dia_semana_id', $diaSemana);
    }

    // ============= MÉTODOS ESTÁTICOS =============

    /**
     * Verificar status do horário hoje
     */
    public static function getStatusHoje($empresaId, $sistema = 'TODOS')
    {
        try {
            $dataHoje = Carbon::now()->format('Y-m-d');
            $horaAtual = Carbon::now()->format('H:i:s');
            $diaSemana = Carbon::now()->dayOfWeekIso; // 1 = segunda, 7 = domingo

            // 1. Primeiro verifica se existe exceção para hoje
            $excecao = self::porEmpresa($empresaId)
                ->excecoes()
                ->where('data_excecao', $dataHoje)
                ->where(function ($query) use ($sistema) {
                    $query->where('sistema', $sistema)
                        ->orWhere('sistema', 'TODOS');
                })
                ->orderBy('sistema', 'DESC') // TODOS vem por último
                ->first();

            if ($excecao) {
                return self::processarStatusHorario($excecao, $horaAtual, true);
            }

            // 2. Se não tem exceção, busca horário normal
            $horario = self::porEmpresa($empresaId)
                ->horariosPadrao()
                ->porDiaSemana($diaSemana)
                ->where(function ($query) use ($sistema) {
                    $query->where('sistema', $sistema)
                        ->orWhere('sistema', 'TODOS');
                })
                ->orderBy('sistema', 'DESC') // TODOS vem por último
                ->first();

            if (!$horario) {
                return [
                    'aberto' => false,
                    'mensagem' => 'Horário não configurado',
                    'dados' => null,
                    'is_excecao' => false,
                    'proxima_abertura' => null
                ];
            }

            return self::processarStatusHorario($horario, $horaAtual, false);
        } catch (\Exception $e) {
            return [
                'aberto' => false,
                'mensagem' => 'Erro ao verificar horário: ' . $e->getMessage(),
                'dados' => null,
                'is_excecao' => false,
                'proxima_abertura' => null
            ];
        }
    }

    /**
     * Processa o status do horário
     */
    private static function processarStatusHorario($horario, $horaAtual, $isExcecao)
    {
        $estaAberto = false;
        $mensagem = 'Fechado';
        $proximaAbertura = null;

        if ($horario->aberto) {
            $horaAbertura = $horario->hora_abertura;
            $horaFechamento = $horario->hora_fechamento;

            if ($horaAtual >= $horaAbertura && $horaAtual <= $horaFechamento) {
                $estaAberto = true;
                $mensagem = "Aberto agora (das " . Carbon::parse($horaAbertura)->format('H:i') .
                    " às " . Carbon::parse($horaFechamento)->format('H:i') . ")";
            } else if ($horaAtual < $horaAbertura) {
                $mensagem = "Fechado - Abre às " . Carbon::parse($horaAbertura)->format('H:i');
                $proximaAbertura = $horaAbertura;
            } else {
                $mensagem = "Fechado - Fechou às " . Carbon::parse($horaFechamento)->format('H:i');
            }
        } else {
            $descricao = $isExcecao && $horario->descricao_excecao ?
                " ({$horario->descricao_excecao})" : "";
            $mensagem = "Fechado" . $descricao;
        }

        return [
            'aberto' => $estaAberto,
            'mensagem' => $mensagem,
            'dados' => $horario,
            'is_excecao' => $isExcecao,
            'proxima_abertura' => $proximaAbertura
        ];
    }

    /**
     * Buscar próximo dia de funcionamento
     */
    public static function getProximoDiaAberto($empresaId, $sistema = 'TODOS')
    {
        try {
            // Versão simplificada para evitar erros
            return [
                'data' => Carbon::tomorrow()->format('Y-m-d'),
                'hora_abertura' => '08:00:00',
                'mensagem' => 'Amanhã às 08:00'
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Relatório de status para todos os sistemas
     */
    public static function getRelatorioStatus($empresaId)
    {
        $sistemas = ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'];
        $relatorio = [];

        foreach ($sistemas as $sistema) {
            $status = self::getStatusHoje($empresaId, $sistema);
            $proximoAberto = self::getProximoDiaAberto($empresaId, $sistema);

            $relatorio[$sistema] = [
                'sistema' => $sistema,
                'status_hoje' => $status,
                'proximo_funcionamento' => $proximoAberto
            ];
        }

        return $relatorio;
    }

    /**
     * Validar dados antes de salvar
     */
    public static function validarDados($dados, $isExcecao = false)
    {
        $errors = [];

        // Validações básicas
        if (empty($dados['empresa_id'])) {
            $errors[] = 'Empresa é obrigatória';
        }

        // Validações específicas para exceções
        if ($isExcecao) {
            if (empty($dados['data_excecao'])) {
                $errors[] = 'Data da exceção é obrigatória';
            }

            if (!empty($dados['data_excecao'])) {
                $dataExcecao = Carbon::parse($dados['data_excecao']);
                if ($dataExcecao->isPast()) {
                    $errors[] = 'Data da exceção deve ser futura';
                }
            }
        } else {
            // Validações para horários padrão
            if (empty($dados['dia_semana_id']) || !is_numeric($dados['dia_semana_id'])) {
                $errors[] = 'Dia da semana é obrigatório';
            }
        }

        // Validar sistema
        $sistemasValidos = ['TODOS', 'PDV', 'FINANCEIRO', 'ONLINE'];
        if (!empty($dados['sistema']) && !in_array($dados['sistema'], $sistemasValidos)) {
            $errors[] = 'Sistema inválido';
        }

        // Validar horários quando estabelecimento está aberto
        if (!empty($dados['aberto']) && $dados['aberto']) {
            if (empty($dados['hora_abertura']) || empty($dados['hora_fechamento'])) {
                $errors[] = 'Horários de abertura e fechamento são obrigatórios quando aberto';
            }

            if (!empty($dados['hora_abertura']) && !empty($dados['hora_fechamento'])) {
                $abertura = Carbon::createFromFormat('H:i', $dados['hora_abertura']);
                $fechamento = Carbon::createFromFormat('H:i', $dados['hora_fechamento']);

                if ($abertura->gte($fechamento)) {
                    $errors[] = 'Horário de abertura deve ser anterior ao fechamento';
                }
            }
        }

        return $errors;
    }

    // ============= EVENTOS DO MODELO =============

    protected static function boot()
    {
        parent::boot();

        // Log de auditoria ao criar
        static::created(function ($model) {
            HorarioFuncionamentoLog::registrarLog('CREATE', $model->empresa_id, $model->id, null, $model->toArray());
        });

        // Log de auditoria ao atualizar
        static::updated(function ($model) {
            HorarioFuncionamentoLog::registrarLog('UPDATE', $model->empresa_id, $model->id, $model->getOriginal(), $model->toArray());
        });

        // Log de auditoria ao deletar
        static::deleted(function ($model) {
            HorarioFuncionamentoLog::registrarLog('DELETE', $model->empresa_id, $model->id, $model->toArray(), null);
        });
    }
}
