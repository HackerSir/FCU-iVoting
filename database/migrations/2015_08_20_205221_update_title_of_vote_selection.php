<?php

use Hackersir\VoteSelection;
use Illuminate\Database\Migrations\Migration;

class UpdateTitleOfVoteSelection extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $voteSelectionList = VoteSelection::all();
        foreach ($voteSelectionList as $voteSelection) {
            if (!empty($voteSelection->title)) {
                continue;
            }
            //若無標題屬性，嘗試從data解析標題
            $json = json_decode($voteSelection->data);
            if (json_last_error() != JSON_ERROR_NONE) {
                continue;
            }
            if (!empty($json->title)) {
                //找出並移除data中的title
                $voteSelection->title = $json->title;
                unset($json->title);
                $voteSelection->data = json_encode($json, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
                $voteSelection->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $voteSelectionList = VoteSelection::all();
        foreach ($voteSelectionList as $voteSelection) {
            if (!starts_with($voteSelection->data, '[') && !starts_with($voteSelection->data, '{')) {
                continue;
            }
            $json = json_decode($voteSelection->data);
            if (json_last_error() != JSON_ERROR_NONE) {
                continue;
            }
            //將title存入data
            $json->title = $voteSelection->title;
            $voteSelection->title = '';
            $voteSelection->data = json_encode($json, JSON_UNESCAPED_UNICODE + JSON_UNESCAPED_SLASHES);
            $voteSelection->save();
        }
    }
}
