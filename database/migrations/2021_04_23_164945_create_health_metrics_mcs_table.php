<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthMetricsMcsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('health_metrics_mcs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('checkin_id')->nullable();
            $table->string('clinic_name');
            $table->date('leave_from');
            $table->date('leave_to');
            $table->float('total_days', 8,1);
            $table->enum('status', ['Auto Applied','Reverted']);
            $table->longText('link');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('application_id')->references('id')->on('leave_applications');
            $table->foreign('checkin_id')->references('id')->on('health_metrics_checkins');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('health_metrics_mcs');
    }
}
