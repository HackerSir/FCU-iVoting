<?php

use App\Group;
use Illuminate\Database\Schema\Blueprint;
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
        Group::create(array('name' => 'admin', 'title' => '管理員'));
        Group::create(array('name' => 'staff', 'title' => '工作人員'));
        Group::create(array('name' => 'default', 'title' => '預設'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Group::where('name', '=', 'admin')->delete();
        Group::where('name', '=', 'staff')->delete();
        Group::where('name', '=', 'default')->delete();
    }
}
