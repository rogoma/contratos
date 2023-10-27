<?php

namespace App\Http\Controllers\Planning;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Modality;
use App\Models\OrderOrderState;

class PlanningsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:plannings.orders.index,admin.orders.index')->only('index'); // Permiso para index
        $this->middleware('checkPermission:plannings.orders.show,admin.orders.show')->only('show'); // Permiso para show
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // obtenemos los pedidos con estado > 5 (PROCESADO POR DGAF y VERIFICADO POR COORD.)
        $orders = Order::where('actual_state', '>', 5)->get();
        return view('planning.plannings.index', compact('orders'));

    }

    /**
     * Visualizar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $order_id)
    {
        $order = Order::find($order_id);

        $planning_dependency = $request->user()->dependency_id;
        // Obtenemos los simese cargados por otras dependencias
        $related_simese = $order->simese()->where('dependency_id', '!=', $planning_dependency)->get();
        // Obtenemos los simese cargados por planificacion
        $related_simese_planning = $order->simese()->where('dependency_id', $planning_dependency)->get();
        // Obtenemos los archivos cargados por otras dependencias
        $other_files = $order->files()->where('dependency_id', '!=', $planning_dependency)->get();
        // Obtenemos los archivos cargados por planificacion
        $planning_files = $order->files()->where('dependency_id', $planning_dependency)->get();
        // Obtenemos las protestas cargadas en la dncp
        $objections = $order->objections;

        return view('planning.plannings.show', compact('order', 'related_simese','related_simese_planning', 'other_files', 'planning_files', 'objections'));
    }

    /**
     * Recibir un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrder(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de recibir pedido
        if (!$request->user()->hasPermission(['plannings.orders.recibe'])) {
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Si se recibe de CVE
        if ($order->actual_state == 57) {
            // Estado 16 = RECIBIDO de CVE EN PLANIFICACIÓN
            $order->actual_state = 16;
            $order->save();

            // Registramos el movimiento de estado en la tabla orders_order_state
            $order_order_state = new OrderOrderState;
            $order_order_state->order_id = $order->id;
            $order_order_state->order_state_id = 16;
            $order_order_state->creator_user_id = $request->user()->id;
            $order_order_state->save();

            return response()->json(['status' => 'success', 'message' => 'Recibido de Procesos Complementarios y Excepciones '], 200);
        } else {
            // Estado 15 = RECIBIDO PLANIFICACIÓN
            $order->actual_state = 15;
            $order->save();

            // Registramos el movimiento de estado en la tabla orders_order_state
            $order_order_state = new OrderOrderState;
            $order_order_state->order_id = $order->id;
            $order_order_state->order_state_id = 15;
            $order_order_state->creator_user_id = $request->user()->id;
            $order_order_state->save();

            return response()->json(['status' => 'success', 'message' => 'Se ha recibido exitosamente el pedido. '], 200);
        }
    }


    /**
     * Funcionalidad de modificacion del pedido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        // chequeamos que el usuario tenga permisos para editar el pedido
        if($request->user()->hasPermission(['plannings.orders.update'])){
            return view('planning.orders.update', compact('order'));
        }else{
            return back()->with('error', 'No tiene los suficientes permisos para acceder a esta sección.');
        }
    }

    /**
     * Funcionalidad de modificacion del pedido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);

        $rules = array(
            'number' => 'numeric|required|max:2147483647',
            'dncp_pac_id' => 'numeric|required|max:2147483647',
            'total_amount' => 'string|required|max:200',
            'begin_date' => 'date_format:d/m/Y|required',
            // 'cdp_number' => 'string|required|max:200|unique:orders',
            'cdp_number' => 'string|nullable|max:200',
            'cdp_date' => 'date_format:d/m/Y|nullable',
            'cdp_amount' => 'string|nullable|max:200',
            // 'dncp_resolution_number' => 'string|required|max:8',
            // 'dncp_resolution_date' => 'date_format:d/m/Y|required',
            'unpostponable' => 'boolean|nullable',

        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        // CONTROL DE NULL Y DUPLICIDAD DE CDP - Convertimos String a Number y preguntamos si ya existe el N° CDP
            if(empty($request->input('cdp_number'))){

            }else{
                $cdp_number = $request->input('cdp_number');
                $check_cdp = Order::where('cdp_number', str_replace('.', '',$cdp_number))->
                                where('id', '!=', $order_id)->count();
                if($check_cdp > 0){
                    $validator->errors()->add('cdp_number', 'Número de CDP ingresado ya se encuentra vinculado a un pedido.');
                    return back()->withErrors($validator)->withInput();
                }
            }

            // ORIGINAL
            // CONTROL DE DUPLICIDAD DE CDP - Convertimos String a Number y preguntamos si ya existe el N° CDP
                // $cdp_number = $request->input('cdp_number');
                // $check_cdp = Order::where('cdp_number', str_replace('.', '',$cdp_number))->
                //                 where('id', '!=', $order_id)->count();
                // if($check_cdp > 0){
                //     $validator->errors()->add('cdp_number', 'Número de CDP ingresado ya se encuentra vinculado a un pedido.');
                //     return back()->withErrors($validator)->withInput();
                // }


            // CONTROL DE DUPLICIDAD DE N° LLAMADO - preguntamos si ya existe el Numero de LLamado
            $check_cdp = Order::where('number', $request->input('number'))->
                                where('id', '!=', $order_id)->count();
            if($check_cdp > 0){
                $validator->errors()->add('number', 'Número de Llamado ya se encuentra vinculado a un pedido.');
                return back()->withErrors($validator)->withInput();
            }

            // CONTROL DE DUPLICIDAD DE N° PACID - preguntamos si ya existe el Numero de PACID
            $check_cdp = Order::where('dncp_pac_id', $request->input('dncp_pac_id'))->
                                where('id', '!=', $order_id)->count();
            if($check_cdp > 0){
                $validator->errors()->add('dncp_pac_id', 'ID_PAC ya se encuentra vinculado a un pedido.');
                return back()->withErrors($validator)->withInput();
            }

            // CONTROL DE MONTO DE CDP NO DEBE SER MAYOR A TOTAL_AMOUNT
            $total_amount = $request->input('total_amount');
            $total_pedido = str_replace('.', '',$total_amount);

            $cdp_amount = $request->input('cdp_amount');
            $cdp_pedido = str_replace('.', '',$cdp_amount);

            // var_dump($order['total_pedido']);
            // var_dump($order['cdp_pedido']);

            // SI EL MONTO DE CDP ESTÁ VACIO VERIFICA QUE NO TENGA N°CDP Y FECHA EL CDP
            if(empty($cdp_pedido)){
                if(!empty($request->input('cdp_number'))){
                    $validator->errors()->add('cdp_amount', 'INGRESE MONTO DEL CDP');
                    return back()->withErrors($validator)->withInput();
                }else{
                    // $validator->errors()->add('cdp_number', 'INGRESE Número del CDP');
                    // return back()->withErrors($validator)->withInput();
                }

                if(!empty($request->input('cdp_date'))){
                    $validator->errors()->add('cdp_amount', 'INGRESE MONTO DEL CDP');
                    return back()->withErrors($validator)->withInput();
                }else{
                    // $validator->errors()->add('cdp_date', 'INGRESE Fecha del CDP');
                    // return back()->withErrors($validator)->withInput();
                }
            }

            // SI EL MONTO DE CDP NO ESTÁ VACIO VERIFICA QUE TENGA N°CDP Y FECHA EL CDP
            if(!empty($cdp_pedido)){
                if(empty($request->input('cdp_number'))){
                    $validator->errors()->add('cdp_number', 'INGRESE Número del CDP');
                    return back()->withErrors($validator)->withInput();
                }

                if(empty($request->input('cdp_date'))){
                    $validator->errors()->add('cdp_date', 'INGRESE Fecha del CDP');
                    return back()->withErrors($validator)->withInput();
                }
            }


            if ($cdp_pedido > $total_pedido){
                if(empty($request->input('cdp_number'))){
                    $validator->errors()->add('cdp_number', 'INGRESE Número del CDP');
                    return back()->withErrors($validator)->withInput();
                }

                if(empty($request->input('cdp_date'))){
                    $validator->errors()->add('cdp_date', 'INGRESE Fecha del CDP');
                    return back()->withErrors($validator)->withInput();
                }

                $validator->errors()->add('cdp_amount', 'MONTO del CDP no puede ser mayor al MONTO del LLAMADO');
                return back()->withErrors($validator)->withInput();
            }


        // creamos variable para almacenar entrada de total del Form, se utilizará abajo en $order->total_amoun
        $total_amount = $request->input('total_amount');
        $cdp_number = $request->input('cdp_number');
        $cdp_amount = $request->input('cdp_amount');

        $order->number = $request->input('number');
        $order->dncp_pac_id = $request->input('dncp_pac_id');
        $order->total_amount = str_replace('.', '',$total_amount);
        $order->begin_date = empty($request->input('begin_date')) ? NULL : date('Y-m-d', strtotime(str_replace("/", "-", $request->input('begin_date'))));

        //Verificamos si están vacios los campos de CDP si están vacios se guardan como null sino están se guardan on el contenido
        if(empty($request->input('cdp_number'))){
            $order->cdp_number = null;
        }else{
            $order->cdp_number = str_replace('.', '',$cdp_number);
        }

        if(empty($request->input('cdp_date'))){
            $order->cdp_date = null;
        }else{
            $order->cdp_date = empty($request->input('cdp_date')) ? NULL : date('Y-m-d', strtotime(str_replace("/", "-", $request->input('cdp_date'))));
        }

        if(empty($request->input('cdp_amount'))){
            $order->cdp_amount = null;
        }else{
            $order->cdp_amount = str_replace('.', '',$cdp_amount);
        }

        // $order->dncp_resolution_number = $request->input('dncp_resolution_number');
        // $order->dncp_resolution_date = $request->input('dncp_resolution_date');
        $order->unpostponable = $request->input('unpostponable');
        $order->modifier_user_id = $request->user()->id;  // usuario logueado
        $order->save();

        return redirect()->route('plannings.show', $order_id)->with('success', 'Llamado modificado correctamente');;
    }

    /**
     * Derivar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveOrder(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['plannings.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 20 = PROCESADO PEDIDO EN PLANIFICACIÓN
        $order->actual_state = 20;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 20;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha derivado exitosamente el pedido.'], 200);
    }

    /**
     * Derivar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveOrderObs(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['plannings.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 62 = PROCESADO OBS EN PLANIFICACIÓN
        $order->actual_state = 62;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 62;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha derivado exitosamente el pedido.'], 200);
    }


    /**
     * Derivar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveAdjudica(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['plannings.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 63 = PROCESADO OBSERV. DE ADJUDICACIONES EN  PAC
        $order->actual_state = 63;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 63;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha derivado exitosamente a Adjudicaciones'], 200);
    }


    /**
     * Recibir un pedido para procesar las Observaciones en el PAC
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrderObs(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['plannings.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 59 = RECIBIDO EN PAC PARA PROCESAR OBSERVACIONES
        $order->actual_state = 59;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 59;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'RECIBIDO EN PAC PARA PROCESAR OBSERVACIONES'], 200);
    }

    /**
     * Recibir un pedido para procesar las Observaciones en el PAC
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrderObsAdju(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['plannings.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 61 = RECIBIDO EN PAC PARA PROCESAR OBSERVACIONES DE ADJUDICA
        $order->actual_state = 61;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 61;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'RECIBIDO EN PAC PARA PROCESAR OBSERVACIONES DE ADJUDICACIONES'], 200);
    }
}
