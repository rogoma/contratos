<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    /**
     * Para obtener el vinculo con la tabla districts
     */
    public function districts(){
        return $this->hasMany('App\Models\District', 'coddpto');
    }
}
