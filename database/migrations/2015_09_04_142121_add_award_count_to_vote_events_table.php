<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAwardCountToVoteEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //在 vote_events 新增 award_count 欄位，紀錄要取多少名次
        Schema::table('vote_events', function ($table) {
            $table->integer('award_count')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //在 vote_events 移除 award_count
        Schema::table('vote_events', function ($table) {
            $table->dropColumn('award_count');
        });
    }
}
