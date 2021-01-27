<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateGroupsStudentsTableWithStdId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('groups_students', function (Blueprint $table) {
            $table->string('group_student_id');
            $table->boolean('allowed')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('groups_students', function (Blueprint $table) {
            $table->dropColumn('group_student_id');
            $table->dropColumn('allowed');
        });
    }
}
