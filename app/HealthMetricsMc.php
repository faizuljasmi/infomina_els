<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HealthMetricsMc extends Model
{
    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
