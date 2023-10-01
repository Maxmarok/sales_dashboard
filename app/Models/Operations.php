<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operations extends Model
{
    use HasFactory;

    protected $table = 'operations';
    protected $guarded = [];
    protected $hidden = ['account'];
    protected $appends = ['account_name', 'article_name', 'currency_sign'];

    protected $casts = [
        'date'  => 'date:d.m.Y',
    ];

    public function getAccountNameAttribute()
    {
        return $this->account ? $this->account->title : null;
    }

    public function getArticleNameAttribute()
    {
        return $this->article ? $this->article->title : null;
    }

    public function getCurrencySignAttribute()
    {
        return $this->account ? $this->account->currency_sign : null;
    }

    public function account(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Accounts::class, 'id', 'account_id');
    }

    public function article(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Articles::class, 'id', 'article_id');
    }
}
