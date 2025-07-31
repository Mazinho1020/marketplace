<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'funforcli';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'cpf',
        'data_nascimento',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'status'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
    ];

    // Accessor para padronizar o nome
    public function getNameAttribute()
    {
        return $this->nome;
    }
}
