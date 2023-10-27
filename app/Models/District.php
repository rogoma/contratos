<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    /**
     * Para obtener el vinculo con la tabla departments
     */
    public function department(){
        return $this->belongsTo('App\Models\Department', 'coddpto');
    }

    /**
     * Para obtener el vinculo con la tabla regions
     * Ya que regions es una tabla con primary key compuesto entonces obtenemos la
     * region correspondiente mediante una consulta con el modelo Región
     */
    public function region(){
        return Region::where('codreg', $this->codreg)
                     ->where('subcreg', $this->subcreg)
                     ->get()
                     ->first();
    }
    
}
