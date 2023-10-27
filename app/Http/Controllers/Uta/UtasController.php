<?php

namespace App\Http\Controllers\Uta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderOrderState;

class UtasController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:utas.orders.index,admin.orders.index')->only('index'); // Permiso para index 
        $this->middleware('checkPermission:utas.orders.show,admin.orders.show')->only('show'); // Permiso para show         
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {        
        $orders = Order::all();      
        return view('uta.utas.index', compact('orders'));
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
        
        return view('uta.utas.show', compact('order', 'related_simese', 'related_simese_user', 'other_files', 'user_files'));        
    }

    /**
     * Recibir un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrderUta(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de recibir pedido
        if(!$request->user()->hasPermission(['utas.orders.recibe'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);                    
        // Estado 95 = RECIBIDO PBC EN UTA PARA VERIFICAR
        $order->actual_state = 95;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 95;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha recibido exitosamente el pedido'], 200);
    }
    
    public function recibeInfoUta(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de recibir pedido
        if(!$request->user()->hasPermission(['utas.orders.recibe'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);                
        // Estado 98 = RECIBIDO INFORME EVAL. EN UTA PARA VERIFICAR
        $order->actual_state = 98;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 98;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha recibido exitosamente el pedido'], 200);
    }

    /*** Formulario de modificacion de pedido *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response */
    public function edit(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        // chequeamos que el usuario tenga permisos para editar el pedido
        if($request->user()->hasPermission(['utas.orders.update'])){
            return view('uta.orders.update', compact('order'));
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

        return redirect()->route('utas.show', $order_id)->with('success', 'Llamado modificado correctamente');;
    }

    /**
     * Verificar PBC SIN REPARO
     *
     * @return \Illuminate\Http\Response
     */
    public function verificaPBC_Sr(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['utas.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 96 = PBC VERIFICADO SIN REPAROS
        $order->actual_state = 96;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 96;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'EXITO: PBC Verificado SIN REPAROS!!'], 200);
    }

    /**
     * Verificar PBC SIN REPARO-PROCESO COVID
     *
     * @return \Illuminate\Http\Response
     */
    public function verificaPBC_SrCovid(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['utas.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 131 = PBC VERIFICADO SIN REPAROS-PROCESO COVID
        $order->actual_state = 131;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 131;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'EXITO: PBC(PROCESO COVID) Verificado SIN REPAROS!!'], 200);
    }
    
    /**
     * Deriva INFORME de EVAL a CESC
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveCESC(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['utas.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 98 = PBC VERIFICADO CON REPAROS
        $order->actual_state = 120;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 120;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'ATENCIÓN: INFORME DE EVALUACIÓN remitido a CESC'], 200);
    }

    /**
     * Reibe INFORME de EVAL de CESC
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeCESC(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['utas.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 120 = DERIVADO A CESC
        $order->actual_state = 122;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 122;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'ATENCIÓN: INFORME DE EVALUACIÓN recibido desde CESC!!'], 200);
    }

    /**
     * Verificar PBC CON REPARO
     *
     * @return \Illuminate\Http\Response
     */
    public function verificaPBC_Cr(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['utas.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 97 = PBC VERIFICADO CON REPAROS
        $order->actual_state = 97;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 97;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'ATENCIÓN: PBC Verificado CON REPAROS!!'], 200);
    }

    /**
     * Verificar PBC CON REPARO IGUAL ENVIADO A ASESORIA JURÍDICA
     *
     * @return \Illuminate\Http\Response
     */
    public function verificaPBC_Cr_AJ(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['utas.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 102 = PBC VERIFICADO CON REPAROS REMITIDO A AJ
        $order->actual_state = 102;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 102;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'ATENCIÓN: PBC Verificado CON REPAROS, ENVIADO A ASESORIA JURÍDICA!!'], 200);
    }

    /**
     * Verificar PBC SIN REPARO
     *
     * @return \Illuminate\Http\Response
     */
    public function verificaINFO_Sr(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['utas.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 99 = VERIFICADO INFORME DE EVAL. SIN REPAROS
        $order->actual_state = 99;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 99;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'EXITO: INFORME DE EVALUACIÓN Verificado SIN REPAROS!!'], 200);
    }

    /**
     * Verificar PBC CON REPARO
     *
     * @return \Illuminate\Http\Response
     */
    public function verificaINFO_Cr(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['utas.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 100 = VERIFICADO INFORME DE EVAL. CON REPAROS
        $order->actual_state = 100;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 100;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'ATENCIÓN: INFORME DE EVALUACIÓN Verificado CON REPAROS!!'], 200);
    }
}
