<?php

use Hackersir\Setting;
use Illuminate\Database\Migrations\Migration;

class AddGlobalNoticeSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Setting::create([
            'id'   => 'global-notice',
            'desc' => '顯示在網頁頂端的訊息（留白不顯示）',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Setting::find('global-notice')->delete();
    }
}
