<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WbSale extends Model
{
    use HasFactory;
    protected $table = 'wb_sales';
    //protected $table = 'sales';
    protected $guarded = [];
}
