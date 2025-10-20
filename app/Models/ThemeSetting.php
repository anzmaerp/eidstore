<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThemeSetting extends Model
{
    protected $fillable = ['key', 'value'];
    use HasFactory;


    public static function get($key)
    {
        return optional(static::where('key', $key)->first())->value;
    }

    public static function set($key, $value)
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
