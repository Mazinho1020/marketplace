<?php

namespace App\Comerciantes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiaSemana extends Model
{
    protected $table = 'empresa_dias_semana';

    protected $fillable = [
        'nome',
        'nome_curto',
        'numero',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public $timestamps = false;

    // ============= RELACIONAMENTOS =============

    // HORÁRIOS REMOVIDOS TEMPORARIAMENTE
    // public function horarios(): HasMany
    // {
    //     return $this->hasMany(HorarioFuncionamento::class, 'dia_semana_id');
    // }

    // ============= SCOPES =============

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeOrdenado($query)
    {
        return $query->orderBy('numero');
    }

    // ============= MÉTODOS ESTÁTICOS =============

    public static function getAll()
    {
        return self::ativo()->ordenado()->get();
    }

    public static function getOptions()
    {
        return self::getAll()->pluck('nome', 'id')->toArray();
    }

    // ============= ACESSORES =============

    public function getNomeCompletoAttribute()
    {
        return $this->nome;
    }

    public function getDiaSemanaPtBrAttribute()
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

        return $dias[$this->numero] ?? 'Desconhecido';
    }
}
