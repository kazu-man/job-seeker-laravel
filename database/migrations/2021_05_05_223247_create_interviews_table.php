<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInterviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('apply_record_id');
            $table->dateTime('interview_date_time');
            $table->unsignedBigInteger('interview_duration');
            $table->string('interview_token');

            $table->timestamps();
            $table->foreign('apply_record_id')->references('id')->on('apply_records')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('interviews');
    }
}
