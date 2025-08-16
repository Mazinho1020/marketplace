<?php

namespace App\Comerciantes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;

class HorarioFuncionamentoLog extends Model
{
    protected $table = 'empresa_horarios_logs';

    protected $fillable = [
        'empresa_id',
        'horario_id',
        'acao',
        'dados_anteriores',
        'dados_novos',
        'usuario_id',
        'usuario_nome',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'dados_anteriores' => 'array',
        'dados_novos' => 'array',
    ];

    public $timestamps = false;

    // ============= RELACIONAMENTOS =============

    public function horario(): BelongsTo
    {
        return $this->belongsTo(HorarioFuncionamento::class, 'horario_id');
    }

    // ============= SCOPES =============

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorAcao($query, $acao)
    {
        return $query->where('acao', $acao);
    }

    public function scopeRecentes($query, $dias = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    // ============= MÉTODOS ESTÁTICOS =============

    /**
     * Registra log de auditoria
     */
    public static function registrarLog($acao, $empresaId, $horarioId = null, $dadosAnteriores = null, $dadosNovos = null)
    {
        try {
            $usuario = Auth::guard('comerciante')->user();

            self::create([
                'empresa_id' => $empresaId,
                'horario_id' => $horarioId,
                'acao' => $acao,
                'dados_anteriores' => $dadosAnteriores,
                'dados_novos' => $dadosNovos,
                'usuario_id' => $usuario ? $usuario->id : null,
                'usuario_nome' => $usuario ? $usuario->nome : 'Sistema',
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        } catch (\Exception $e) {
            // Log de auditoria não deve quebrar a operação principal
            Log::error("Erro ao registrar log de auditoria: " . $e->getMessage());
        }
    }

    /**
     * Buscar logs por empresa
     */
    public static function getLogsPorEmpresa($empresaId, $limit = 50)
    {
        return self::porEmpresa($empresaId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Relatório de atividades
     */
    public static function getRelatorioAtividades($empresaId, $dias = 30)
    {
        return self::porEmpresa($empresaId)
            ->recentes($dias)
            ->selectRaw('acao, COUNT(*) as total, DATE(created_at) as data')
            ->groupBy('acao', 'data')
            ->orderBy('data', 'desc')
            ->get();
    }

    // ============= ACESSORES =============

    public function getAcaoFormatadaAttribute()
    {
        $acoes = [
            'CREATE' => 'Criação',
            'UPDATE' => 'Atualização',
            'DELETE' => 'Exclusão',
            'VIEW' => 'Visualização'
        ];

        return $acoes[$this->acao] ?? $this->acao;
    }

    public function getDataFormatadaAttribute()
    {
        return $this->created_at->format('d/m/Y H:i:s');
    }

    public function getResumoAlteracaoAttribute()
    {
        if ($this->acao === 'CREATE') {
            return 'Novo horário criado';
        }

        if ($this->acao === 'DELETE') {
            return 'Horário removido';
        }

        if ($this->acao === 'UPDATE' && $this->dados_anteriores && $this->dados_novos) {
            $alteracoes = [];

            foreach ($this->dados_novos as $campo => $valorNovo) {
                $valorAntigo = $this->dados_anteriores[$campo] ?? null;

                if ($valorAntigo != $valorNovo) {
                    $alteracoes[] = "{$campo}: {$valorAntigo} → {$valorNovo}";
                }
            }

            return count($alteracoes) > 0 ? implode(', ', array_slice($alteracoes, 0, 3)) : 'Dados atualizados';
        }

        return 'Alteração realizada';
    }
}
