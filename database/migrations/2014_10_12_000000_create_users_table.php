<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->enum('gender', ['Male','Female'])->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // $table->integer('giv_annual')->nullable();
            // $table->integer('giv_calamity')->nullable();
            // $table->integer('giv_carryfwd')->nullable();
            // $table->integer('giv_compassionate')->nullable();
            // $table->integer('giv_emergency')->nullable();
            // $table->integer('giv_hospitalization')->nullable();
            // $table->integer('giv_marriage')->nullable();
            // $table->integer('giv_maternity')->nullable();
            // $table->integer('giv_paternity')->nullable();
            // $table->integer('giv_sick')->nullable();
            // $table->integer('giv_training')->nullable();
            // $table->integer('giv_unpaid')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
