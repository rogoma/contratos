<?php

namespace App\Http\Controllers\Tender;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderOrderState;
use App\Models\CalledState;

use function PHPUnit\Framework\isNull;

class TendersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:tenders.orders.index,admin.orders.index')->only('index'); // Permiso para index 
        $this->middleware('checkPermission:tenders.orders.show,admin.orders.show')->only('show'); // Permiso para show 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // obtenemos los pedidos que sean Licitaciones modality = 1 y que estén con estado mayor o igual a 20 (PROCESADO PLANIFICACIÓN)        
        $orders = Order::wherein('modality_id', [1,2,3,7,8,17,20])
        ->where('actual_state', '>=', 20)
        ->get();        
        return view('tender.tenders.index', compact('orders'));        
    }

    /**
     * Visualizar un pedido
     *
     * @return \Illuminate\Http\Response    
     */
    public function show(Request $request, $order_id)
    {
        $order = Order::find($order_id);
        
        $tender_dependency = $request->user()->dependency_id;
        // Obtenemos los simese cargados por otras dependencias
        $related_simese = $order->simese()->where('dependency_id', '!=', $tender_dependency)->get();
        // Obtenemos los simese cargados por licitaciones
        $related_simese_tender = $order->simese()->where('dependency_id', $tender_dependency)->get();
        
        // Obtenemos los archivos cargados por otras dependencias y que no sean de reparo
        $other_files = $order->files()->where('dependency_id', '!=', $tender_dependency)
                                            ->whereIn('file_type', [0, 3, 4, 5])//0-antecedentes 3-contratos 4-addendas  5-dictamenes 
                                            ->orderBy('created_at','asc')
                                            ->get();
        
        // Obtenemos los archivos cargados por licitaciones con tipo de archivos que no sean 1 (reparos dncp)        
        $tender_files = $order->files()->where('dependency_id', $tender_dependency)->where('file_type', '=', 0)//Archivos normales                                                                                   -
                                                                                   ->get();

        // Obtenemos los archivos cargados por licitaciones con tipo de archivos 7 cuadro comparativo
        $tender_files2 = $order->files()->where('dependency_id', $tender_dependency)->where('file_type', '=', 7)//cuadro comparativo
                                                                                   ->get();                                                                                   
        // Obtenemos los archivos cargados por licitaciones con tipo de archivos que sean 1 (reparos dncp)                
        $tender_filedncp = $order->files()->where('dependency_id', $tender_dependency)->where('file_type', '=', 1)->get();        
        
        // Obtenemos los archivos cargados por excepciones con tipo de archivos que sean 6 (cosultas dncp)
        $tender_filedncp_con = $order->files()->where('dependency_id', $tender_dependency)->where('file_type', '=', 6)->get();
        
        // Obtenemos las consultas cargadas en la dncp
        $queries = $order->queries;
        // Obtenemos las protestas cargadas en la dncp
        $objections = $order->objections;


        // $called_states = CalledState::all();        

        return view('tender.tenders.show', compact('order', 'related_simese', 'queries',
                    'related_simese_tender', 'other_files', 'tender_files', 'objections',
                    'tender_filedncp','tender_files2','tender_filedncp_con' ));                    
    }

    /**
     * Recibir un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrder(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de recibir pedido
        if(!$request->user()->hasPermission(['tenders.orders.recibe'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 25 = RECIBIDO LICITACIONES
        $order->actual_state = 25;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 25;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha recibido exitosamente el pedido. '], 200);
    }

    /**
     * Recibir PBC con REPARO DE UTA
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrderUTA(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de recibir pedido
        if(!$request->user()->hasPermission(['tenders.orders.recibe'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 101 = RECIBIDO PBC CON REPARO DE UTA
        $order->actual_state = 101;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 101;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Recibido PBC con REPARO de UTA'], 200);
    }

    /**
     * Formulario de modificacion de pedido
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        // chequeamos que el usuario tenga permisos para editar el pedido
        if($request->user()->hasPermission(['tenders.orders.update'])){
            return view('tender.orders.update', compact('order'));
        }else{
            return back()->with('error', 'No tiene los suficientes permisos para acceder a esta sección.');
        }
    }

    /**
     * Formulario de modificacion de pedido
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

        return redirect()->route('tenders.show', $order_id)->with('success', 'Llamado modificado correctamente');;
    }

    /**
     * Derivar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveOrder(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['tenders.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 26 = ENVIADO A UTA PARA CONTROL PBC
        $order->actual_state = 26;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 26;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();
        
        return response()->json(['status' => 'success', 'message' => 'Se ha derivado exitosamente el pedido. '], 200);
    }

    /**
     * Derivar Pedido a PAC
     *
     * @return \Illuminate\Http\Response
     */
    public function derivePAC(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de recibir pedido
        if(!$request->user()->hasPermission(['tenders.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 58 = DERIVADO A PAC POR OBSERVACIONES
        $order->actual_state = 58;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 58;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();
        
        return response()->json(['status' => 'success', 'message' => 'Llamado derivado a PAC por Observaciones'], 200);
    }

    /**
     * Derivar Antecedentes a Comité Evaluador
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveComite(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['tenders.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 35 = DERIVADO DE COMITE EVALUADOR
        $order->actual_state =  35;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 35;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();
        
        return response()->json(['status' => 'success', 'message' => 'Antecedentes derivado a Comité Evaluador'], 200);
    }

    /**
     * Obtener notificaciones de alertas
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getNotifications(Request $request)
    {
        // obtenemos los pedidos que sean Licitaciones modality = 1
        // que estén con estado mayor o igual a 20 (PROCESADO PLANIFICACIÓN)
        // y estado menor o igual a 130 (DERIVADO DE DOC PARA PROCESAR PEDIDO)
        $orders = Order::whereIn('modality_id', [1,2,3,7,8])
        ->where('actual_state', '>=', 20)->where('actual_state', '<=', 130)
        ->get(); 

        // Por cada orden verificamos fecha tope y consultas sin responder
        $alerta_consultas = array();
        $alerta_aclaraciones = array();
        $tope_recepcion_consultas = 0;
        $dias_tope_consultas = 0;
        $dias_tope_aclaraciones = 0;
        $hoy = strtotime(date('Y-m-d'));
        foreach($orders as $order){
            // en caso de no haber cargado la fecha tope continuamos con el siguiente pedido
            if(empty($order->queries_deadline)){
                continue;
            }

            // se cargó la fecha tope, definimos fecha de recepcion de consultas
            $dia_apertura_sobres = date('N', strtotime($order->queries_deadline));
            switch ($order->modality_id) {
                // LPN, LPN-SBE, LPI
                case 1: case 2: case 3:
                    // lunes a domingo
                    $tope_recepcion_consultas = 5 + 2;
                    break;
                // LCO, LCO-SBE
                case 7: case 8:
                    // lunes, martes, miercoles
                    if($dia_apertura_sobres >= 1 && $dia_apertura_sobres <= 3){
                        $tope_recepcion_consultas = 3 + 2;
                    }
                    // jueves, viernes, sabado
                    if($dia_apertura_sobres >= 4 && $dia_apertura_sobres <= 6){
                        $tope_recepcion_consultas = 3;
                    }
                    // domingo
                    if($dia_apertura_sobres == 7){
                        $tope_recepcion_consultas = 3 + 1;
                    }
                    break;
                default:
                    $tope_recepcion_consultas = 5 + 2;
                    break;
            }
            // definimos fecha de aclaracion de consultas
            // lunes, martes
            if($dia_apertura_sobres >= 1 && $dia_apertura_sobres <= 2){
                $tope_aclaraciones = 2 + 2;
            }
            // miercoles, jueves, viernes, sabado
            if($dia_apertura_sobres >= 3 && $dia_apertura_sobres <= 6){
                $tope_aclaraciones = 2;
            }
            // domingo
            if($dia_apertura_sobres == 7){
                $tope_aclaraciones = 2 + 1;
            }

            // definimos dias de aviso recepcion de consultas
            $apertura_sobres = strtotime($order->queries_deadline);
            $limite_mayor_consultas = strtotime($order->queries_deadline . ' -'.$tope_recepcion_consultas.' days');
            $dias_aviso = $tope_recepcion_consultas + 5;
            $limite_menor_consultas = strtotime($order->queries_deadline . ' -'.$dias_aviso.' days');
            if($hoy <= $limite_mayor_consultas && $hoy >= $limite_menor_consultas){
                $segundos_llegar_tope = $limite_mayor_consultas - $hoy;
                $dias_tope_consultas = floor(abs($segundos_llegar_tope / 60 / 60 / 24 )); 

                $pac_id = number_format($order->dncp_pac_id,0,",",".");

                $hoy = date('d-m-Y');
                $fecha_fin = date("d-m-Y",strtotime($hoy."+ $dias_tope_consultas days")); 
                
                // array_push($alerta_consultas, array('pac_id' => $order->dncp_pac_id,'llamado' => $order->number, 'dias' => $dias_tope_consultas));
                array_push($alerta_consultas, array('pac_id' => $pac_id,'llamado' => $order->number, 'dias' => $dias_tope_consultas, 'fecha_fin' => $fecha_fin));
            }
            
            // definimos dias de aviso aclaracion de consultas
            $limite_mayor_aclaraciones = strtotime($order->queries_deadline . ' -'.$tope_aclaraciones.' days');
            $dias_aviso = $tope_aclaraciones + 5;
            $limite_menor_aclaraciones = strtotime($order->queries_deadline . ' -'.$dias_aviso.' days');
            if($hoy <= $limite_mayor_aclaraciones && $hoy >= $limite_menor_aclaraciones){
                $segundos_llegar_tope = $limite_mayor_aclaraciones - $hoy;
                $dias_tope_aclaraciones = floor(abs($segundos_llegar_tope / 60 / 60 / 24 )); 
            }

            // chequeamos si el pedido esta por llegar a fecha tope de aclaracion de consultas
            // y tiene consultas sin ser respondidas
            $consultas_faltantes_respuesta = $order->queries->where('answered', false)->count();
            if($consultas_faltantes_respuesta > 0 && $dias_tope_aclaraciones > 0){
                array_push($alerta_aclaraciones, array('llamado' => $order->number, 
                    'consultas_pendientes' => $consultas_faltantes_respuesta,
                    'dias' => $dias_tope_aclaraciones));
            }
        }

        return response()->json(['status' => 'success', 'alerta_consultas' => $alerta_consultas,
                                'alerta_aclaraciones' => $alerta_aclaraciones], 200);
    }

    /**
     * Actualizar fecha tope de consultas
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateQueriesDeadline(Request $request, $order_id)
    {
        $order = Order::find($order_id);

        if(is_null($order)){
            return response()->json(['status' => 'error', 'message' => 'Adertencia! No se encontró ningún pedido con los datos enviados.'], 200);
        }
        if(empty($request->input('queries_deadline'))){
            return response()->json(['status' => 'error', 'message' => 'Advertencia! Debe enviar una fecha apertura de sobres válida.'], 200);
        }

        $order->queries_deadline = date("Y-m-d", strtotime(str_replace("/", "-", $request->input('queries_deadline'))));
        $order->save();

        return response()->json(['status' => 'success', 'message' => 'Fecha apertura de sobres actualizada correctamente.'], 200);
    }
}
