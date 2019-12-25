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
            $table->string('staff_id')->unique();
            $table->string('name');
            $table->enum('gender', ['Male','Female'])->nullable();
            $table->string('email')->unique();
            $table->string('job_title')->nullable();
            $table->date('join_date')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_no')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('status',['Active','Inactive'])->default('Active');
            $table->string('password');
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
