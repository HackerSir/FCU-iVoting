<?php

use Illuminate\Database\Migrations\Migration;

class AddOrderColumnToSelectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vote_selections', function ($table) {
            $table->integer('order');
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
            $table->dropColumn('order');
        });
    }
}
