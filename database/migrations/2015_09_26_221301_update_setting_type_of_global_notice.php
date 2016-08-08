<?php

use Illuminate\Database\Migrations\Migration;

class UpdateSettingTypeOfGlobalNotice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $setting = \App\Setting::find('global-notice');
        $setting->type = 'markdown';
        $setting->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $setting = \App\Setting::find('global-notice');
        $setting->type = 'text';
        $setting->save();
    }
}
