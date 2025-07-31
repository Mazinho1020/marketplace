// app/Models/Cliente/Cliente.php
<?php

namespace App\Models\Cliente;

use App\Models\BaseModel;

class Cliente extends BaseModel
{
    protected $table = 'clientes';

    protected $fillable = [
        'business_id',
        'name',
        'email',
        'phone',
        'cpf',
        'birth_date',
        'loyalty_points',
        'is_active'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'loyalty_points' => 'integer',
        'is_active' => 'boolean'
    ];

    public function addresses()
    {
        return $this->hasMany(ClienteAddress::class);
    }

    public function sales()
    {
        return $this->hasMany(\App\Models\PDV\Sale::class);
    }

    public function loyaltyPoints()
    {
        return $this->hasMany(LoyaltyPoint::class);
    }
}
