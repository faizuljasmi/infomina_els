<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CalanderRemark extends Model
{
    protected $fillable = [
        'remark_date_from', 'remark_date_from', 'remark_text', 'remark_by'
    ];
}
