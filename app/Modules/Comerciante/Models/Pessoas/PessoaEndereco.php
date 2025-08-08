<?php

namespace App\Modules\Comerciante\Models\Pessoas;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PessoaEndereco extends Model
{
    protected $table = 'pessoas_enderecos';

    protected $fillable = [
        'pessoa_id',
        'empresa_id',
        'tipo',
        'nome_identificacao',
        'principal',
        'cep',
        'logradouro',
        'numero',
        'complemento',
        'bairro',
        'cidade',
        'uf',
        'pais',
        'referencia',
        'latitude',
        'longitude',
        'observacoes',
        'ativo'
    ];

    protected $casts = [
        'principal' => 'boolean',
        'ativo' => 'boolean',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    /**
     * Relacionamentos
     */
    public function pessoa(): BelongsTo
    {
        return $this->belongsTo(Pessoa::class, 'pessoa_id');
    }

    /**
     * Scopes
     */
    public function scopeEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    public function scopePrincipal($query)
    {
        return $query->where('principal', true);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeCidade($query, $cidade)
    {
        return $query->where('cidade', 'LIKE', "%{$cidade}%");
    }

    public function scopeUf($query, $uf)
    {
        return $query->where('uf', $uf);
    }

    /**
     * Métodos de negócio
     */
    public function getEnderecoCompleto()
    {
        $endereco = $this->logradouro . ', ' . $this->numero;

        if ($this->complemento) {
            $endereco .= ' - ' . $this->complemento;
        }

        $endereco .= ' - ' . $this->bairro . ', ' . $this->cidade . '/' . $this->uf;

        if ($this->cep) {
            $endereco .= ' - CEP: ' . $this->getCepFormatado();
        }

        return $endereco;
    }

    public function getCepFormatado()
    {
        $cep = preg_replace('/\D/', '', $this->cep);

        if (strlen($cep) === 8) {
            return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $cep);
        }

        return $this->cep;
    }

    public function isPrincipal()
    {
        return $this->principal;
    }

    public function isAtivo()
    {
        return $this->ativo;
    }

    public function temCoordenadas()
    {
        return !empty($this->latitude) && !empty($this->longitude);
    }

    public function getLinkMaps()
    {
        if ($this->temCoordenadas()) {
            return "https://www.google.com/maps?q={$this->latitude},{$this->longitude}";
        }

        $endereco = urlencode($this->getEnderecoCompleto());
        return "https://www.google.com/maps/search/{$endereco}";
    }

    public function calcularDistancia($latitude, $longitude)
    {
        if (!$this->temCoordenadas()) {
            return null;
        }

        $earthRadius = 6371; // km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
