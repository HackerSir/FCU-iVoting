<?php

use App\Group;
use App\Role;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoleDataFromGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $groupList = Group::all();
        foreach ($groupList as $group) {
            $role = New Role();
            $role->name = $group->name;
            $role->display_name = $group->title;
            $role->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $roleList = Role::all();
        foreach ($roleList as $role) {
            $role->delete();
        }
    }
}
