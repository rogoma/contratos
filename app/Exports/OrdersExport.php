<?php

namespace App\Exports;

//use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

// class ItemAwardsExport implements FromCollection


class OrdersExport implements FromView
{
    //use Exportable;

    // protected $order_id;
    
    // public function __construct()
    // {
        
    // }

    public function view(): view    
    {        
        $orders = DB::table('vista_uta')//vista que muestra los datos                
                    ->select(['number','dncp_pac_id','order_description','modality_code','exp_obj_code','total_amount', 
                    'cdp_number','cdp_date','order_state_description','begin_date','covid','year',
                    'provider','contract_number','contract_date','cc_number','cc_date','monto_adjudica'])                    
                    ->where('monto_adjudica', null)
                    ->orderBy('provider','asc') 
                    ->get();
        //$orders = Order::all();
        return view("order.orders.items", compact("orders"));

        

    }
}