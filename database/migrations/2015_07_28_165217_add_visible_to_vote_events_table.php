<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVisibleToVoteEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //新增 visible(可見性) 欄位到 vote_events
        Schema::table('vote_events', function ($table) {
            $table->boolean('visible')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //移除 visible(可見性) 欄位到 vote_events
        Schema::table('vote_events', function ($table) {
            $table->dropColumn('visible');
        });
    }
}
