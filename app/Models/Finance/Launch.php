// app/Models/Finance/Launch.php
<?php

namespace App\Models\Finance;

use App\Models\BaseModel;

class Launch extends BaseModel
{
    protected $table = 'finance_launches';

    protected $fillable = [
        'business_id',
        'description',
        'amount',
        'type', // 'income' ou 'expense'
        'category_id',
        'account_id',
        'due_date',
        'paid_date',
        'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_date' => 'date',
        'paid_date' => 'date'
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Scopes úteis
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
