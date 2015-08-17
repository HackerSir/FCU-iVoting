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
        foreach ($userList as $user) {
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
