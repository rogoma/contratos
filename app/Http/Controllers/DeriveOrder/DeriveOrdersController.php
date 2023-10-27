<?php

namespace App\Http\Controllers\DeriveOrder;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Simese;
use App\Models\OrderOrderState;

class DeriveOrdersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:derive_orders.orders.derive')->only(['create', 'store',]);
        $this->middleware('checkPermission:derive_orders.orders.update_derive')->only(['create', 'store']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('order.derive_orders.create', compact('order'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        $verification = $request->input('verification');
        $urgency_state = $request->input('urgency_state');
        $rejected_obs = $request->input('rejected_obs');
        $state = ($verification == 'ACEPTAR') ? 8 : 10;

        $order->urgency_state = $urgency_state;
        // ESTADO igual a PROCESADO DGAF - ACEPTADO O PROCESADO DGAF - RECHAZADO
        $order->actual_state = $state;
        $order->rejected_obs = $rejected_obs;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = $state;
        $order->rejected_obs = $rejected_obs;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        $request->session()->flash('success', 'Se ha procesado exitosamente el pedido.');
        return response()->json(['status' => 'success', 'code' => 200], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        // En caso de no encontrarse en estado PROCESADO POR DGAF
        if($order->actual_state != 8 && $order->actual_state != 10){
            return back()->with('error', 'No tiene los suficientes permisos para acceder a esta sección.');
        }
        $verification = ($order->actual_state == 8) ? 'ACEPTAR' : 'RECHAZAR';
        return view('order.derive_orders.update', compact('order', 'verification'));
    }

    /**
     * Updated a specific resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        $verification = $request->input('verification');
        $urgency_state = $request->input('urgency_state');
        $rejected_obs = $request->input('rejected_obs');
        
        $state = ($verification == 'ACEPTAR') ? 8 : 10;

        $order->urgency_state = $urgency_state;
        // ESTADO igual a PROCESADO DGAF - ACEPTADO O PROCESADO DGAF - RECHAZADO
        $order->actual_state = $state;
        $order->rejected_obs = $rejected_obs;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = $state;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        $request->session()->flash('success', 'Se ha procesado exitosamente el pedido.');
        return response()->json(['status' => 'success', 'code' => 200], 200);
    }

     /**
     * Derivar PBC 
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrder(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['derive_orders.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Cambia a estado Verificado PBC UTA
        $order->actual_state = 27;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 27;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Verificado PBC en UTA'], 200);        
    }
   
    // /**
    //  * Derivar DICTAMEN de PBC
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function deriveDictamen(Request $request, $order_id){
    //     // Chequeamos que el usuario disponga de permisos de derivacion de pedido
    //     if(!$request->user()->hasPermission(['derive_orders.orders.derive'])){            
    //         return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
    //     }

    //     $order = Order::find($order_id);        
    //     //Cuando es usuario DGAF
    //     // if ($request->user()->dependency->id == 2) {
    //         // Si se recibe de CVE
    //         if ($order->actual_state == 117) {
    //             // Cambia a estado 127 = V°B°-VERIFICADO DICTAMEN DE CVE EN DGAF
    //             $order->actual_state = 127;
    //             $order->save();
    //             return response()->json(['status' => 'success', 'message' => 'Verificado Dictamen de CVE en DGAF'], 200);
    //         } else {                
    //             // Cambia a estado 125-V°B°-VERIFICADO DICTAMEN DE PBC EN DGAF     
    //             $order->actual_state = 125;
    //             $order->save();
    //             return response()->json(['status' => 'success', 'message' => 'Verificado Dictamen de PBC en DGAF'], 200);
    //         }                    
    //     // }   
    // }

    // /**
    //  * VERIFICAR DICTAMEN de EVALUACIÓN
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function deriveDictamenEVAL(Request $request, $order_id){
    //     // Chequeamos que el usuario disponga de permisos de derivacion de pedido
    //     if(!$request->user()->hasPermission(['derive_orders.orders.derive'])){            
    //         return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
    //     }

    //     $order = Order::find($order_id);        
    //     //Cuando es usuario Coordinación DOC
    //     if ($request->user()->dependency->id == 55) {
    //         // Cambia a estado 155-VERIFICADO DICTAMEN DE EVALUACION EN COORD. DOC
    //         $order->actual_state = 155;
    //         $order->save();
    //         return response()->json(['status' => 'success', 'message' => 'Verificado Dictamen de Evaluación'], 200);        
    //     }
        
    //     //Cuando es usuario DGAF
    //     if ($request->user()->dependency->id == 2) {
    //         // Cambia a estado 160-V°B°-VERIFICADO DICTAMEN DE EVALUACIÓN EN DGAF    
    //         $order->actual_state = 160;
    //         $order->save();
    //         return response()->json(['status' => 'success', 'message' => 'Verificado Dictamen de Evaluación'], 200);        
    //     }
    // }

    /**
     * Derivar DERIVAR PEDIDO A EXCEPCIONES
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveExcepciones(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['derive_orders.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);

        //Cuando es usuario Coordinación
        // if ($request->user()->dependency->id == 55) {
        // Cambia a estado 13-DERIVADO DE DOC A DPTO. DE PROCESOS COMPLEM. Y EXCEPCIONES
        $order->actual_state = 13;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 13;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Derivado Pedido a DPTO. DE PROCESOS COMPLEM. Y EXCEPCIONES'], 200);        
        // }               
    }

    
    /**
     * Derivar INFORME DE EVALUACIÓN por EL COMITE
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveInforme(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['derive_orders.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Cambia a estado 40-PROCESADO INFORME COMITE EVALUADOR
        $order->actual_state = 40;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 40;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Derivado Informe de Comité de Evaluación'], 200);          
    }    
}
