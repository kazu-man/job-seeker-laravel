<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_descriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('description')->nullable();
            $table->longText('requirement')->nullable();
            $table->longText('benefit')->nullable();
            $table->longText('experience')->nullable();
            $table->string('job_title')->nullable();
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
        Schema::dropIfExists('job_descriptions');
    }
}
