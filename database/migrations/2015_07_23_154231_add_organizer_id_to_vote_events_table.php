<?php

use Illuminate\Database\Migrations\Migration;

class AddOrganizerIdToVoteEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_events', function ($table) {
            $table->integer('organizer_id')->nullable()->unsigned();
            $table->foreign('organizer_id')->references('id')->on('organizers')->onUpdate('cascade')->onDelete('set null');
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
            $table->dropForeign('vote_events_organizer_id_foreign');
            $table->dropColumn('organizer_id');
        });
    }
}
