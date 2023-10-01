<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    use HasFactory;

    protected $table = 'bank_accounts';
    protected $guarded = [];
    protected $hidden = ['store'];
    protected $appends = ['store_name', 'currency_sign'];

    const CURRENCIES = ['₽' => 'RUB', '₸' => 'KZT', 'Br' => 'BYR'];

    public function getCurrencySignAttribute()
    {
        $currency = $this->attributes['currency'];
        $sign = array_search($currency, self::CURRENCIES);

        return $sign;
    }

    public function getStoreNameAttribute()
    {
        return $this->store ? $this->store->name : null;
    }

    public function store(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Lk::class, 'id', 'lk_id');
    }

    // protected function getShortKey($key): string
    // {
    //     return substr($key, 0, self::KEY_LEN) . '...' . substr($key, strlen($key) - self::KEY_LEN, self::KEY_LEN);
    // }
}
