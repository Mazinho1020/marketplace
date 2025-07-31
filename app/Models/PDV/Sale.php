// app/Models/PDV/Sale.php
<?php

namespace App\Models\PDV;

use App\Models\BaseModel;

class Sale extends BaseModel
{
    protected $table = 'pdv_sales';

    protected $fillable = [
        'business_id',
        'cliente_id',
        'total',
        'subtotal',
        'discount',
        'tax',
        'payment_method',
        'status',
        'cashier_id',
        'cash_register_id'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2'
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente\Cliente::class);
    }

    public function cashier()
    {
        return $this->belongsTo(\App\Models\User::class, 'cashier_id');
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }
}
