<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HealthMetricsCheckin extends Model
{
    public function mc() {
        return $this->hasOne(HealthMetricsMC::class,'checkin_id','id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }
}
