<?php

use App\Role;
use Illuminate\Database\Schema\Blueprint;
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
                'name' => $role->name,
                'title' => $role->display_name
            ]);
        }
        DB::table('groups')->insert(array('name' => 'default', 'title' => '預設'));

        $defaultGroup = DB::table('groups')->where('name', 'default')->first();

        Schema::table('users', function ($table) use ($defaultGroup) {
            $table->integer('group_id')->nullable()->unsigned()->default($defaultGroup->id);
            $table->foreign('group_id')->references('id')->on('groups')->onUpdate('cascade')->onDelete('set null');
        });
    }
}
