<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lk extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
