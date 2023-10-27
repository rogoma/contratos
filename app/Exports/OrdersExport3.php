<?php

namespace App\Exports;

//use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use App\Models\User;

// class ItemAwardsExport implements FromCollection


class OrdersExport3 implements FromView
{
    //use Exportable;

    // protected $order_id;
    
    // public function __construct()
    // {
        
    // }

    public function view(): view    
    {      
        $users = DB::table('vista_users_full')//vista que muestra los datos                
                    ->select(['ci','nombre','apellido','dependencia','cargo','rol','state'])                                        
                    ->orderBy('ci','asc')
                    ->get();        
        return view("admin.users.users", compact("users"));
    }
}