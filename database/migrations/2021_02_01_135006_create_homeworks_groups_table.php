<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeworksGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homeworks_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId("group_id");
            $table->foreign('group_id')->references('id')->on('groups');
            $table->foreignId("homework_id");
            $table->foreign('homework_id')->references('id')->on('homeworks');
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
        Schema::dropIfExists('homeworks_groups');
    }
}
