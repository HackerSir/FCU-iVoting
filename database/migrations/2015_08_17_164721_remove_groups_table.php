<?php

use App\Role;
use App\User;
use Illuminate\Database\Migrations\Migration;

class RemoveGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->dropForeign('users_group_id_foreign');
            $table->dropColumn('group_id');
        });

        Schema::drop('groups');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('groups', function ($table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->string('title', 20);
            $table->timestamps();
        });
        $roleList = Role::all();
        foreach ($roleList as $role) {
            DB::table('groups')->insert([
                'name'  => $role->name,
                'title' => $role->display_name,
            ]);
        }
        DB::table('groups')->insert(['name' => 'default', 'title' => '預設']);

        $defaultGroup = DB::table('groups')->where('name', 'default')->first();

        Schema::table('users', function ($table) use ($defaultGroup) {
            $table->integer('group_id')->nullable()->unsigned()->default($defaultGroup->id);
            $table->foreign('group_id')->references('id')->on('groups')->onUpdate('cascade')->onDelete('set null');
        });

        $userList = User::all();
        foreach ($userList as $user) {
            $role_user = DB::table('role_user')->where('user_id', $user->id)->first();
            if (!$role_user) {
                continue;
            }
            $role = DB::table('roles')->find($role_user->role_id);
            $group = DB::table('groups')->where('name', '=', $role->name)->first();
            $user->group_id = $group->id;
            $user->save();
        }
    }
}
