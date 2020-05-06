<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public function getTimeAttribute($value)
    {
        return date('Y-m-d H:i:s',$value) ;
    }
}
