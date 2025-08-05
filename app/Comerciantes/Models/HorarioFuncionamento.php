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
        'hora_abertura' => 'datetime:H:i',
        'hora_fechamento' => 'datetime:H:i',
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
        $dataHoje = Carbon::now()->format('Y-m-d');
        $horaAtual = Carbon::now()->format('H:i:s');
        $diaSemana = Carbon::now()->dayOfWeekIso; // 1 = segunda, 7 = domingo

        // 1. Primeiro verifica se existe exceção para hoje
        $excecao = self::porEmpresa($empresaId)
            ->excecoes()
            ->where('data_excecao', $dataHoje)
            ->where(function($query) use ($sistema) {
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
            ->where(function($query) use ($sistema) {
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
            $horaAbertura = Carbon::createFromFormat('H:i:s', $horario->hora_abertura)->format('H:i:s');
            $horaFechamento = Carbon::createFromFormat('H:i:s', $horario->hora_fechamento)->format('H:i:s');

            if ($horaAtual >= $horaAbertura && $horaAtual <= $horaFechamento) {
                $estaAberto = true;
                $mensagem = "Aberto agora (das " . Carbon::createFromFormat('H:i:s', $horaAbertura)->format('H:i') . 
                           " às " . Carbon::createFromFormat('H:i:s', $horaFechamento)->format('H:i') . ")";
            } else if ($horaAtual < $horaAbertura) {
                $mensagem = "Fechado - Abre às " . Carbon::createFromFormat('H:i:s', $horaAbertura)->format('H:i');
                $proximaAbertura = $horaAbertura;
            } else {
                $mensagem = "Fechado - Fechou às " . Carbon::createFromFormat('H:i:s', $horaFechamento)->format('H:i');
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
        $hoje = Carbon::now();
        $dataAtual = $hoje->format('Y-m-d');
        $horaAtual = $hoje->format('H:i:s');

        // 1. Verificar se ainda pode abrir hoje
        $statusHoje = self::getStatusHoje($empresaId, $sistema);
        if ($statusHoje['proxima_abertura']) {
            return [
                'data' => $dataAtual,
                'dia_semana' => $hoje->dayOfWeekIso,
                'hora_abertura' => $statusHoje['proxima_abertura'],
                'mensagem' => "Hoje às " . Carbon::createFromFormat('H:i:s', $statusHoje['proxima_abertura'])->format('H:i')
            ];
        }

        // 2. Verificar exceções futuras (próximos 30 dias)
        $excecaoFutura = self::porEmpresa($empresaId)
            ->excecoes()
            ->where('data_excecao', '>', $dataAtual)
            ->where('aberto', true)
            ->where(function($query) use ($sistema) {
                $query->where('sistema', $sistema)
                      ->orWhere('sistema', 'TODOS');
            })
            ->orderBy('data_excecao')
            ->orderBy('sistema', 'DESC')
            ->first();

        // 3. Buscar próximo dia regular
        $proximoDiaRegular = null;
        for ($i = 1; $i <= 7; $i++) {
            $proximaData = $hoje->copy()->addDays($i);
            $diaSemana = $proximaData->dayOfWeekIso;

            $horarioRegular = self::porEmpresa($empresaId)
                ->horariosPadrao()
                ->porDiaSemana($diaSemana)
                ->where('aberto', true)
                ->where(function($query) use ($sistema) {
                    $query->where('sistema', $sistema)
                          ->orWhere('sistema', 'TODOS');
                })
                ->orderBy('sistema', 'DESC')
                ->first();

            if ($horarioRegular) {
                $proximoDiaRegular = [
                    'data' => $proximaData->format('Y-m-d'),
                    'dia_semana' => $diaSemana,
                    'hora_abertura' => $horarioRegular->hora_abertura,
                    'mensagem' => $proximaData->format('d/m/Y') . " às " . 
                                Carbon::createFromFormat('H:i:s', $horarioRegular->hora_abertura)->format('H:i')
                ];
                break;
            }
        }

        // Comparar e retornar o mais próximo
        if ($excecaoFutura && $proximoDiaRegular) {
            return Carbon::parse($excecaoFutura->data_excecao)->lte(Carbon::parse($proximoDiaRegular['data'])) 
                ? [
                    'data' => $excecaoFutura->data_excecao->format('Y-m-d'),
                    'hora_abertura' => $excecaoFutura->hora_abertura,
                    'mensagem' => $excecaoFutura->data_excecao->format('d/m/Y') . " às " . 
                                Carbon::createFromFormat('H:i:s', $excecaoFutura->hora_abertura)->format('H:i'),
                    'is_excecao' => true,
                    'descricao' => $excecaoFutura->descricao_excecao
                ]
                : $proximoDiaRegular;
        }

        return $excecaoFutura ? [
            'data' => $excecaoFutura->data_excecao->format('Y-m-d'),
            'hora_abertura' => $excecaoFutura->hora_abertura,
            'mensagem' => $excecaoFutura->data_excecao->format('d/m/Y') . " às " . 
                        Carbon::createFromFormat('H:i:s', $excecaoFutura->hora_abertura)->format('H:i'),
            'is_excecao' => true,
            'descricao' => $excecaoFutura->descricao_excecao
        ] : $proximoDiaRegular;
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
