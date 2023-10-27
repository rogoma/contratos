<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemAwardHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'item_award_histories';

    /**
     * Para obtener el vinculo con la tabla items
     */
    public function item(){
        return $this->belongsTo('App\Models\Item');
    }

    /**
     * Para obtener el vinculo con la tabla budget_request_providers
     */
    public function budgetRequestProvider(){
        return $this->belongsTo('App\Models\BudgetRequestProvider');
    }
    
    /*** Para obtener el vinculo con la tabla orders */
    public function orders(){
        return $this->belongsToMany('App\Models\Order', 'budget_request_providers');
    }

    /**
     * Para obtener el vinculo con la tabla users
     */
    public function creatorUser(){
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Para obtener el vinculo con la tabla users
     */
    public function modifierUser(){
        return $this->belongsTo('App\Models\User');
    }

    /**
     * Agregamos funciones que formatean los datos 
     * para mayor utilidad en los views
     */
    public function provider(){
        if(empty($this->budget_request_provider_id)){
            return "";
        }else{
            return $this->budgetRequestProvider->provider->description;
        }
    }
    public function dncpPacIdFormat(){
        if(empty($this->dncp_pac_id)){
            return "";
        }else{
            return number_format($this->dncp_pac_id,0,",",".");
        }
    }
    public function amountFormat(){
        return number_format($this->amount,0,",",".");
    }
}
