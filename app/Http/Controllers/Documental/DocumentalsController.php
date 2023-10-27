<?php

namespace App\Http\Controllers\Documental;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderOrderState;

class DocumentalsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:documentals.orders.index,admin.orders.index')->only('index'); // Permiso para index 
        $this->middleware('checkPermission:documentals.orders.show,admin.orders.show')->only('show'); // Permiso para show         
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $orders = Order::all(); 
        // // obtenemos los Llamados que fueron derivados a partir de estado derivado a Comité Evaluador >= estado igual a 35 (DERIVADO A COMITE EVALUADOR)
        // $orders = Order::where('actual_state', '>=', 35)                                            
        //                 ->get();        
        return view('documental.documentals.index', compact('orders'));
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

        // Obtenemos los archivos cargados por licitaciones con tipo de archivos 7 cuadro comparativo
        $cuadro_compar = $order->files()->where('dependency_id', '!=', $user_dependency)->where('file_type', '=', 7)//cuadro comparativo
                                                                                         ->get(); 

        return view('documental.documentals.show', compact('order', 'related_simese', 'related_simese_user', 'other_files', 'user_files','cuadro_compar'));        
    }

    /*** Formulario de modificacion de pedido *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response */
    public function edit(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        // chequeamos que el usuario tenga permisos para editar el pedido
        if($request->user()->hasPermission(['documentals.orders.update'])){
            return view('documental.orders.update', compact('order'));
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

        return redirect()->route('documentals.show', $order_id)->with('success', 'Llamado modificado correctamente');;
    }

    /**
     * Recibir PBC y antecedentes para Evaluación 
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrder(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if (!$request->user()->hasPermission(['documentals.orders.derive'])) {
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        // Cuando proviene de estado 35-Derivado a Documental, cambia a estado 135-Recibido en Comité para Evaluación de Ofertas
        $order = Order::find($order_id);                
        $order->actual_state = 135;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 135;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Recibido Antecedentes para Evaluación de Ofertas'], 200);        
    }
    
    /**
     * Recibir Evaluación y antecedentes con reparo de UTA
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrderReparo(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if (!$request->user()->hasPermission(['documentals.orders.derive'])) {
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        // Cuando proviene de estado 100-Verificado Eval en UTA ocn reparo, cambia a estado 136-Recibido Evalución con reparo de UTA        
        $order = Order::find($order_id);
        $order->actual_state = 136;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 136;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Recibido Evaluación con reparo de UTA, se verificará reparos'], 200);
    }

    /**
     * Derivar DICTAMEN 
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveInforme(Request $request, $order_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['documentals.orders.derive'])){            
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);               
        // Cambia a estado 140-PROCESADO INFORME POR COMITE EVALUADOR
        $order->actual_state = 140;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 140;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Derivado Informe de Evaluación'], 200);
    }
}
