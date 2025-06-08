<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'group'];

    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function getMonths()
    {
        return collect(range(1, 12))->mapWithKeys(function ($month) {
            return [
                $month => Carbon::createFromDate(null, $month, 1)
                    ->locale(app()->getLocale())
                    ->translatedFormat('F')
            ];
        });
    }


}
