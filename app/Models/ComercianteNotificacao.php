<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ComercianteNotificacao extends Model
{
    use HasFactory;

    protected $table = 'comerciante_notificacoes';

    protected $fillable = [
        'empresa_id',
        'usuario_id',
        'tipo',
        'titulo',
        'mensagem',
        'dados',
        'url_acao',
        'icone',
        'cor',
        'prioridade',
        'referencia_tipo',
        'referencia_id',
        'lida',
        'lida_em',
        'expirar_em'
    ];

    protected $casts = [
        'dados' => 'array',
        'lida' => 'boolean',
        'lida_em' => 'datetime',
        'expirar_em' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relacionamentos
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeNaoLidas($query)
    {
        return $query->where('lida', false);
    }

    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePrioridade($query, $prioridade)
    {
        return $query->where('prioridade', $prioridade);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    public function scopeNaoExpiradas($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expirar_em')
                ->orWhere('expirar_em', '>', now());
        });
    }

    // Accessors
    public function getTempoAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function getCorBadgeAttribute()
    {
        return match ($this->prioridade) {
            'baixa' => 'success',
            'media' => 'info',
            'alta' => 'warning',
            'critica' => 'danger',
            default => 'secondary'
        };
    }

    public function getIconeDefaultAttribute()
    {
        return match ($this->tipo) {
            'estoque_baixo' => 'fas fa-exclamation-triangle',
            'estoque_zerado' => 'fas fa-times-circle',
            'produto_criado' => 'fas fa-plus-circle',
            'produto_editado' => 'fas fa-edit',
            'venda_realizada' => 'fas fa-shopping-cart',
            'pagamento_recebido' => 'fas fa-credit-card',
            'sistema' => 'fas fa-cog',
            'promocao' => 'fas fa-tags',
            default => 'fas fa-bell'
        };
    }

    // Métodos
    public function marcarComoLida()
    {
        return $this->update([
            'lida' => true,
            'lida_em' => now()
        ]);
    }

    public function marcarComoNaoLida()
    {
        return $this->update([
            'lida' => false,
            'lida_em' => null
        ]);
    }

    public function estaPendente()
    {
        return !$this->lida && (!$this->expirar_em || $this->expirar_em > now());
    }

    public function estaExpirada()
    {
        return $this->expirar_em && $this->expirar_em <= now();
    }

    // Métodos estáticos
    public static function criarNotificacao(array $dados)
    {
        $dados['empresa_id'] = $dados['empresa_id'] ?? session('empresa_id');
        $dados['usuario_id'] = $dados['usuario_id'] ?? Auth::id();

        return static::create($dados);
    }

    public static function notificacoesNaoLidas($empresaId = null)
    {
        $empresaId = $empresaId ?? session('empresa_id');

        return static::where('empresa_id', $empresaId)
            ->naoLidas()
            ->naoExpiradas()
            ->orderBy('prioridade_ordem')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function contarNaoLidas($empresaId = null)
    {
        $empresaId = $empresaId ?? session('empresa_id');

        return static::where('empresa_id', $empresaId)
            ->naoLidas()
            ->naoExpiradas()
            ->count();
    }

    public static function limparNotificacoesAntigas($dias = 30)
    {
        return static::where('created_at', '<', now()->subDays($dias))
            ->where('lida', true)
            ->delete();
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($notificacao) {
            // Definir ordem de prioridade para ordenação
            $notificacao->prioridade_ordem = match ($notificacao->prioridade) {
                'critica' => 1,
                'alta' => 2,
                'media' => 3,
                'baixa' => 4,
                default => 5
            };

            // Definir ícone padrão se não fornecido
            if (!$notificacao->icone) {
                $notificacao->icone = $notificacao->icone_default;
            }

            // Definir cor padrão baseada na prioridade
            if (!$notificacao->cor) {
                $notificacao->cor = $notificacao->cor_badge;
            }
        });
    }
}
