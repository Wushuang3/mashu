<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetClass extends Model
{
    const HOUR_9    = 9;
    const HOUR_10   = 10;
    const HOUR_11   = 11;
    const HOUR_13   = 13;
    const HOUR_14   = 14;
    const HOUR_15   = 15;
    const HOUR_16   = 16;
    const HOUR_17   = 17;

    public static function HourMap($hour = 0)
    {
        $map = [
            self::HOUR_9  => '09:00',
            self::HOUR_10 => '10:00',
            self::HOUR_11 => '11:00',
            self::HOUR_13 => '13:00',
            self::HOUR_14 => '14:00',
            self::HOUR_15 => '15:00',
            self::HOUR_16 => '16:00',
            self::HOUR_17 => '17:00',
        ];
        if (!empty($hour)) {
            return $map[$hour] ?? '';
        }

        return $map;
    }

    public function getHourAttribute($value)
    {
        return $value;
        //return explode(',', $value);
    }
    public function getTimeAttribute($value)
    {
        return date('Y-m-d',$value) ;
    }

    public function setHourAttribute($value)
    {
        $this->attributes['hour'] = implode(',', $value);
    }
    public function setTimeAttribute($value)
    {
        $this->attributes['time'] = strtotime(date('Y-m-d',strtotime($value)));
    }

}
