<?php

namespace App\Http\Controllers\Dgaf;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderOrderState;

class DgafsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:dgafs.orders.index,admin.orders.index')->only('index'); // Permiso para index 
        $this->middleware('checkPermission:dgafs.orders.show,admin.orders.show')->only('show'); // Permiso para show         
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        //$orders = Order::all();      
        //NO SE MUESTRAN LOS PEDIDOS ANULADOS            
        $orders = Order::where('actual_state', '>', 0)
        ->get();
        return view('dgaf.dgafs.index', compact('orders'));
    }

    /**
     * Visualizar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        $user_dependency = $request->user()->dependency_id;        
        // Obtenemos los simese cargados por otras dependencias
        $related_simese = $order->simese()->where('dependency_id', '!=', $user_dependency)->get();
        // Obtenemos los simese cargados por la dependencia del usuario
        $related_simese_user = $order->simese()->where('dependency_id', $user_dependency)->get();   
        
        // Obtenemos los archivos cargados por otras dependencias y que no sean de reparo
        $other_files = $order->files()->where('dependency_id', '!=', $user_dependency)
                                            ->whereIn('file_type', [0,3, 4, 5,7])//0-antecedentes 3-contratos 4-addendas  5-dictamenes 
                                            ->orderBy('created_at','asc')
                                            ->get();
        
        // Obtenemos los archivos cargados por usuario con tipo de archivos que no sean 1 (reparos dncp)
        $user_files = $order->files()->where('dependency_id', $user_dependency)->where('file_type', '=', 0)->get();

        return view('dgaf.dgafs.show', compact('order', 'related_simese', 'related_simese_user', 'other_files', 'user_files'));        
    }
    
    /*** Formulario de modificacion de pedido *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response */
    public function edit(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        // chequeamos que el usuario tenga permisos para editar el pedido
        if($request->user()->hasPermission(['dgafs.orders.update'])){
            return view('dgaf.orders.update', compact('order'));
        }else{
            return back()->with('error', 'No tiene los suficientes permisos para acceder a esta sección.');
        }
    }

    /*** Funcionalidad de modificacion del pedido *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);

        $rules = array(
            'number' => 'numeric|required|max:2147483647',
            'dncp_pac_id' => 'numeric|required|max:2147483647',
            'dncp_resolution_number' => 'string|required|max:8',
            'dncp_resolution_date' => 'date_format:d/m/Y|required',

        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $order->number = $request->input('number');
        $order->dncp_pac_id = $request->input('dncp_pac_id');
        $order->dncp_resolution_number = $request->input('dncp_resolution_number');
        $order->dncp_resolution_date = $request->input('dncp_resolution_date');
        $order->modifier_user_id = $request->user()->id;  // usuario logueado
        $order->save();

        return redirect()->route('dgafs.show', $order_id)->with('success', 'Llamado modificado correctamente');;
    }

    /**
     * Derivar DICTAMEN de PBC
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveDictamen(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['derive_orders.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);        
        //Cuando es usuario DGAF
        // if ($request->user()->dependency->id == 2) {
            // Si se recibe de CVE
            if ($order->actual_state == 117) {
                // Cambia a estado 127 = V°B°-VERIFICADO DICTAMEN DE CVE EN DGAF
                $order->actual_state = 127;
                $order->save();

                // Registramos el movimiento de estado en la tabla orders_order_state
                $order_order_state = new OrderOrderState;
                $order_order_state->order_id = $order->id;
                $order_order_state->order_state_id = 127;
                $order_order_state->creator_user_id = $request->user()->id;
                $order_order_state->save();

                return response()->json(['status' => 'success', 'message' => 'Verificado Dictamen de CVE en DGAF'], 200);
            } else {                
                // Cambia a estado 125-V°B°-VERIFICADO DICTAMEN DE PBC EN DGAF     
                $order->actual_state = 125;
                $order->save();

                // Registramos el movimiento de estado en la tabla orders_order_state
                $order_order_state = new OrderOrderState;
                $order_order_state->order_id = $order->id;
                $order_order_state->order_state_id = 125;
                $order_order_state->creator_user_id = $request->user()->id;
                $order_order_state->save();

                return response()->json(['status' => 'success', 'message' => 'Verificado Dictamen de PBC en DGAF'], 200);
            }                    
        // }   
    }

    /**
     * VERIFICAR DICTAMEN de EVALUACIÓN
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveDictamenEVAL(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['derive_orders.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);        
        //Cuando es usuario Coordinación DOC
        // if ($request->user()->dependency->id == 55) {
        //     // Cambia a estado 155-VERIFICADO DICTAMEN DE EVALUACION EN COORD. DOC
        //     $order->actual_state = 155;
        //     $order->save();
        //     return response()->json(['status' => 'success', 'message' => 'Verificado Dictamen de Evaluación'], 200);        
        // }
        
        //Cuando es usuario DGAF
        // if ($request->user()->dependency->id == 2) {
        // Cambia a estado 160-V°B°-VERIFICADO DICTAMEN DE EVALUACIÓN EN DGAF    
        $order->actual_state = 160;
        $order->save();
        
        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 160;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Verificado Dictamen de Evaluación'], 200);        
        // }
    }

    /**
     * RECIBE PEDIDO PARA PROCESAR
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrderDGAF(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['derive_orders.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);        
        // Cambia a estado 6 RECIBIDO EN DGAF    
        $order->actual_state = 6;
        $order->save();
        
        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 6;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Recibido en DGAF para proceso'], 200);        
        // }
    }

    /**
     * DERIVA PEDIDO PARA PROCESAR
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveOrderDGAF(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['derive_orders.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Cambia a estado 5 DERIVADO DE DGAF-INICIO PROCESO
        $order->actual_state = 5;
        $order->save();
        
        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 5;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Derivado desde DGAF'], 200);        
        // }
    }

    

    
}
