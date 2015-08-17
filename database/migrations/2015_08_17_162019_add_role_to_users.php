<?php

use App\Role;
use App\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRoleToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $userList = User::all();
        $defaultGroup = DB::table('groups')->where('name', 'default')->first();
        foreach ($userList as $user) {
            //不處理預設群組
            if ($user->group_id == $defaultGroup->id) {
                continue;
            }
            $role = Role::where('name', $user->group->name)->first();
            $user->attachRole($role);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $userList = User::all();
        foreach ($userList as $user) {
            $user->detachRoles($user->roles);
        }
    }
}
