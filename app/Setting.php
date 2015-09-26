<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['id', 'desc', 'data'];

    public static function get($id)
    {
        $setting = self::find($id);
        if (!$setting) {
            return null;
        }
        return $setting->data;
    }

    public static function set($id, $data)
    {
        $setting = self::find($id);
        $setting->data = $data;
        $setting->save();
    }
}
