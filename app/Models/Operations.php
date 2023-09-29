<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operations extends Model
{
    use HasFactory;

    protected $table = 'operations';
    protected $guarded = [];
    protected $hidden = ['account'];
    protected $appends = ['account_name'];

    public function getAccountNameAttribute()
    {
        return $this->account ? $this->account->title : null;
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Accounts::class, 'id', 'account_id');
    }
}
