<?php

namespace App\Http\Controllers\LegalAdvice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderOrderState;

class LegalAdvicesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:legal_advices.orders.index,admin.orders.index')->only('index'); // Permiso para index 
        $this->middleware('checkPermission:legal_advices.orders.show,admin.orders.show')->only('show'); // Permiso para show         
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $orders = Order::all();      
        return view('legal_advice.legal_advices.index', compact('orders'));
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
                                            ->whereIn('file_type', [0, 3, 4, 7])//0-antecedentes 3-contratos 4-addendas  5-dictamenes 
                                            ->orderBy('created_at','asc')
                                            ->get();
        
        // Obtenemos los archivos cargados por usuario con tipo de archivos que no sean 1 (reparos dncp)
        $user_files = $order->files()->where('dependency_id', $user_dependency)->where('file_type', '=', 5)->get();
        
        return view('legal_advice.legal_advices.show', compact('order', 'related_simese', 'related_simese_user', 'other_files', 'user_files'));        
    }
    
    /*** Formulario de modificacion de pedido *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response */
    public function edit(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        // chequeamos que el usuario tenga permisos para editar el pedido
        if($request->user()->hasPermission(['legal_advices.orders.update'])){
            return view('legal_advice.orders.update', compact('order'));
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

        return redirect()->route('legal_advices.show', $order_id)->with('success', 'Llamado modificado correctamente');;
    }

    /**
     * RECIBIR PBC PARA DICTAMEN
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrder(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['legal_advices.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Cambia a estado Recibido PBC en AJ para Dictamen
        $order->actual_state = 105;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 105;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Recibido PBC en AJ-PARA DICTAMEN'], 200);        
    }
    
    /**
     * Derivar DICTAMEN DE PBC
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveDictamen(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['legal_advices.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);               
        // Cambia a estado 110-Generado Dictamen AJ-DERIVADO A DOC
        $order->actual_state = 110;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 110;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Derivado Dictamen de PBC'], 200);
    }

    /**
     * Derivar DICTAMEN DE CVE
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveDictamenCVE(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['legal_advices.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);               
        // Cambia a estado 112-Generado Dictamen de CVE en AJ-DERIVADO A DOC
        $order->actual_state = 112;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 112;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Derivado Dictamen de Proceso CVE'], 200);
    }

    
    /**
     * RECIBIR INFORME DE EVALUACIÓN     
     * @return \Illuminate\Http\Response
     */
    public function recibeOrderCVE(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['legal_advices.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Cambia a estado 107-Recibido en AJ para Dictamen de Pedido CVE
        $order->actual_state = 107;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 107;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Recibido CVE en AJ para PARA DICTAMEN'], 200);        
    }
    
    /**
     * RECIBIR INFORME DE EVALUACIÓN     
     * @return \Illuminate\Http\Response
     */
    public function recibeOrderEVAL(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['legal_advices.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Cambia a estado 145-Recibido EVAL en AJ para Dictamen
        $order->actual_state = 145;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 145;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Recibido Evaluación de Ofertas PARA DICTAMEN'], 200);        
    }

    
    /**
     * Derivar DICTAMEN DE EVALUACIÓN
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveDictamenEVAL(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['legal_advices.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);               
        // Cambia a estado 150-Generado Dictamen DE EVAL-DERIVADO A DOC
        $order->actual_state = 150;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 150;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Derivado Dictamen de Evaluación de Ofertas'], 200);
    }
}
