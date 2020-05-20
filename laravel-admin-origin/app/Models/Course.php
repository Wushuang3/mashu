<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    const COURSE_CULTURE   = 1; // 文化课
    const COURSE_EXPERIENCE = 2; // 体验课
    const COURSE_OFFICIAL  = 3; //  正式课

    public static function CourseMap($course = 0)
    {
        $map = [
            self::COURSE_CULTURE => '文化课',
            self::COURSE_EXPERIENCE => '体验课',
            self::COURSE_OFFICIAL => '正式课',
        ];
        if (!empty($course)) {
            return $map[$course] ?? '';
        }

        return $map;
    }

    public function getPriceAttribute($value)
    {
        return $value / 100 ;
    }

    public function setPriceAttribute($value)
    {
        $this->attributes['price'] = $value * 100;
    }
}
