<?php

use App\Role;
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
        $groupList = DB::table('groups')->get();
        foreach ($groupList as $group) {
            //不處理預設群組
            if ($group->name == 'default') {
                continue;
            }
            $role = new Role();
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
