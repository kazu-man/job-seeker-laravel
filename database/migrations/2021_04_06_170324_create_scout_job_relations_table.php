<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoutJobRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('scout_job_relations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('scout_id');
            $table->unsignedBigInteger('job_id');

            $table->foreign('scout_id')->references('id')->on('scouts')->onDelete('cascade');
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');

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
        Schema::dropIfExists('scout_job_relations');
    }
}
