<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    const TEACHER_ONE   = 1; // 初级
    const TEACHER_TWO = 2; // 中级
    const TEACHER_THREE  = 3; //  高级
    const IS_NOT_INDEX  = 0; //  推荐
    const IS_INDEX  = 1; //  推荐

    public static function CourseMap($teacher = 0)
    {
        $map = [
            self::TEACHER_ONE => '初级',
            self::TEACHER_TWO => '中级',
            self::TEACHER_THREE => '高级',
        ];
        if (!empty($teacher)) {
            return $map[$teacher] ?? '';
        }

        return $map;
    }
    public static function IsIndexMap($is_index = 0)
    {
        $map = [
            self::IS_NOT_INDEX => '否',
            self::IS_INDEX => '是',
        ];
        if (!empty($is_index)) {
            return $map[$is_index] ?? '';
        }

        return $map;
    }
    public static function GetKeyVall()
    {
        $teachers= Teacher::all()->toArray();
        $first_names = array_column($teachers, 'user_id','id');
        return $first_names;
    }

    public static function GetMobile()
    {
        $teachers= Teacher::all()->toArray();
        $first_names = array_column($teachers, 'mobile','id');
        return $first_names;
    }
}
