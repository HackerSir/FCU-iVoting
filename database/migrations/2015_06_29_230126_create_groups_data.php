<?php

use Illuminate\Database\Migrations\Migration;

class CreateGroupsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('groups')->insert(['name' => 'admin', 'title' => '管理員']);
        DB::table('groups')->insert(['name' => 'staff', 'title' => '工作人員']);
        DB::table('groups')->insert(['name' => 'default', 'title' => '預設']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('groups')->where('name', '=', 'admin')->delete();
        DB::table('groups')->where('name', '=', 'staff')->delete();
        DB::table('groups')->where('name', '=', 'default')->delete();
    }
}
