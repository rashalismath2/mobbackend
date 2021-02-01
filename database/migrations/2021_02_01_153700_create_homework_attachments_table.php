<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeworkAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('homework_attachments', function (Blueprint $table) {
            $table->id();
            $table->text("file_path");
            $table->string("file_type");
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
        Schema::dropIfExists('homework_attachments');
    }
}
