<?php

namespace App;

use App\Helper\MarkdownHelper;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['id', 'type', 'desc', 'data'];
    public static $types = [
        'text' => '單行文字',
        'multiline' => '多行文字',
        'markdown' => 'Markdown多行文字'
    ];

    public static function get($id)
    {
        $setting = self::find($id);
        if (!$setting) {
            return null;
        }
        return $setting->getData();
    }

    public static function getRaw($id)
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

    public function getType()
    {
        if (!in_array($this->type, array_keys(static::$types))) {
            return head(array_keys(static::$types));
        }
        return $this->type;
    }

    public function getTypeDesc()
    {
        return static::$types[$this->getType()];
    }

    public function getData()
    {
        if ($this->getType() == 'text') {
            return htmlspecialchars($this->data);
        }
        if ($this->getType() == 'multiline') {
            return nl2br(htmlspecialchars($this->data));
        }
        if ($this->getType() == 'markdown') {
            return MarkdownHelper::translate($this->data);
        }
    }
}
