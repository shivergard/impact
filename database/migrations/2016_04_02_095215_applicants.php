<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Applicants extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_applicants', function(Blueprint $table)
        {
            $table->increments('id');

            //info details
            $table->string('name');
            $table->string('email');

            $table->string('git_url');
            //status


            $table->integer('status')->default(0);

            //config
            $table->integer('task_id')->default(0);

            $table->longText("creditals");

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
        //
    }
}
