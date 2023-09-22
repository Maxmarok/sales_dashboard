<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WbReportDetailByPeriod extends Model
{
    use HasFactory;
    protected $table = 'wb_report_detail_by_periods';
    //protected $table = 'ReportDetailByPeriod';
    protected $guarded = [];

    public function setDateFromAttribute()
    {
        $this->attributes['dateFrom'] = $this->attributes['dateFrom']->format('c');
    }
}
