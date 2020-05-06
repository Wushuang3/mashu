<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberLogs extends Model
{
    public function setDataAttribute($data)
    {
        $this->attributes['data'] = json_encode($data);
    }

    public function getDataAttribute($data)
    {
        return json_decode($data, true) ?: [];
    }
}
