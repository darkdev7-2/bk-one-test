<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'symbol',
        'rate',
    ];

    public function userWallet()
    {
        return $this->hasOne(UserWallet::class, 'currency_id');
    }
}
