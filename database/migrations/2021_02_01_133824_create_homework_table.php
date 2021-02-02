<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeworkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homeworks', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("note");
            $table->boolean("onetime");
            $table->date("startDate");
            $table->date("endDate")->nullable();
            $table->time("startTime")->nullable();
            $table->time("endTime")->nullable();   
            $table->string("status")->default("queued");
            $table->interger("number_of_questions");
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
        Schema::dropIfExists('homeworks');
    }
}
