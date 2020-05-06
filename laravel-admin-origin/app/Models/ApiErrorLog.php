<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiErrorLog extends Model
{
    public function setDataAttribute($data)
    {
        $this->attributes['data'] = json_encode($data);
    }

    public function getDataAttribute($data)
    {
        return json_decode($data, true) ?: [];
    }

    public function setParamAttribute($data)
    {
        $this->attributes['data'] = json_encode($data);
    }

    public function getParamAttribute($data)
    {
        return json_decode($data, true) ?: [];
    }
}
