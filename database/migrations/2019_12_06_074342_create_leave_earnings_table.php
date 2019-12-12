<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaveEarningsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leave_earnings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('leave_type_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->integer('no_of_days');
            $table->timestamps();

            $table->foreign('leave_type_id')->references('id')->on('leave_types');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('leave_earnings');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
