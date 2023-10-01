<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lk extends Model
{
    use HasFactory;
    const KEY_LEN = 6;
    protected $guarded = [];

    // public function toArray()
    // {
    //     return [
    //         'id' => $this->id,
    //         'name' => $this->name
    //     ];
    // }

    // protected $with = [
    //     'api_keys',
    // ];

    // protected $appends = [
    //     'api_ad'
    // ];

    protected $hidden = ['api_standard', 'api_statistic', 'api_ad', 'created_at', 'updated_at'];

    protected $appends = ['date', 'api_standard_key', 'api_statistic_key', 'api_ad_key'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function scopeAuthUser()
    {
        return $this->where('user_id', auth()->user()->id);
    }

    public function getDateAttribute()
    {
        return Carbon::parse($this->created_at)->format('d.m.y');
    }

    public function getApiStandardKeyAttribute()
    {
        return $this->api_standard ? self::getShortKey($this->api_standard->key) : null;
    }

    public function getApiStatisticKeyAttribute()
    {
        return $this->api_statistic ? self::getShortKey($this->api_statistic->key) : null;
    }

    public function getApiAdKeyAttribute()
    {
        return $this->api_ad ? self::getShortKey($this->api_ad->key) : null;
    }

    public function api_standard(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ApiKey::class, 'lk_id', 'id')->where('type', 'standard');
    }

    public function api_statistic(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ApiKey::class, 'lk_id', 'id')->where('type', 'statistic');
    }

    public function api_ad(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ApiKey::class, 'lk_id', 'id')->where('type', 'ad');
    }
    
    public function api_keys(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ApiKey::class, 'lk_id', 'id');
    }

    public function accounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Accounts::class, 'lk_id', 'id');
    }

    protected function getShortKey($key): string
    {
        return substr($key, 0, self::KEY_LEN) . '...' . substr($key, strlen($key) - self::KEY_LEN, self::KEY_LEN);
    }
}
