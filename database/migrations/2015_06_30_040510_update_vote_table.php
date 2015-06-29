<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateVoteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_selections', function ($table) {
            $table->foreign('vote_event_id')->references('id')->on('vote_events')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::table('vote_ballots', function ($table) {
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('vote_selection_id')->references('id')->on('vote_selections')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vote_selections', function ($table) {
            $table->dropForeign('vote_selections_vote_event_id_foreign');
        });
        Schema::table('vote_ballots', function ($table) {
            $table->dropForeign('vote_ballots_user_id_foreign');
            $table->dropForeign('vote_ballots_vote_selection_id_foreign');
        });
    }
}
