<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class WbOrder extends Model
{
    use HasFactory;

    protected $table = 'wb_orders';
    protected $guarded = [];
}
