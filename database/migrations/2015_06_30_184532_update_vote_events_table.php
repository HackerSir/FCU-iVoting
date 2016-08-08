<?php

use Illuminate\Database\Migrations\Migration;

class UpdateVoteEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_events', function ($table) {
            $table->integer('max_selected')->default(1)->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vote_events', function ($table) {
            $table->dropColumn('max_selected');
        });
    }
}
