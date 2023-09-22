<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function getLk($id): \Illuminate\Database\Eloquent\Collection
    {
        return $this->hasOne(Lk::class, 'user_id', 'id')->find($id);
    }

    public function lk(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lk::class, 'user_id', 'id')->orderBy('created_at', 'DESC');
    }

    public function apiKeys()
    {
        return ApiKey::whereIn('lk_id', $this->lk->pluck('id')->all())->get();
    }

    /**
     * Возвращает API ключ по указанным параметрам
     * @param $marketplace
     * @param $type
     * @return mixed
     */
    public function getApiKey($marketplace, $type, $lkId = null): mixed
    {
        if($lkId == null){
            $lkId = $this->lk->pluck('id')->first();
        }

        $query = $this->apiKeys()->where('marketplace', $marketplace)->where('type', $type)->where('lk_id', $lkId);
        return $query->pluck('key')->first();
    }
}
