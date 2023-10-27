<?php

namespace App\Http\Controllers\Award;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\OrderOrderState;

use function PHPUnit\Framework\isNull;

class AwardsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:awards.orders.index,admin.orders.index')->only('index'); // Permiso para index 
        $this->middleware('checkPermission:awards.orders.show,admin.orders.show')->only('show'); // Permiso para show 
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Obtenemos los pedidos con estado 160 (V°B° Verificado Evaluación en DGAF) 65,70,85 y 90(relacionados a Adjudicaciones)        
        $orders = Order::wherein('actual_state', [63,64,65,70,75,80,85,90,165])
                        ->get();        
        return view('award.awards.index', compact('orders'));    
    }

    /**
     * Visualizar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $order_id)
    {
        $order = Order::find($order_id);

        $award_dependency = $request->user()->dependency_id;
        // Obtenemos los simese cargados por otras dependencias
        $related_simese = $order->simese()->where('dependency_id', '!=', $award_dependency)->get();
        // Obtenemos los simese cargados por licitaciones
        $related_simese_award = $order->simese()->where('dependency_id', $award_dependency)->get();        
        
        // Obtenemos los archivos cargados por otras dependencias y que no sean de reparo
        $other_files = $order->files()->where('dependency_id', '!=', $award_dependency)
                                            ->whereIn('file_type', [0, 3, 4, 5, 7])//0-antecedentes 3-contratos 4-addendas  5-dictamenes 
                                            ->orderBy('created_at','asc')
                                            ->get();                

        // Obtenemos los archivos cargados por adjudicaciones con tipo de archivos que no sean 1 (reparos dncp)                
        $award_files = $order->files()->where('dependency_id', $award_dependency)->where('file_type', '=', 0)->get();
        
        // Obtenemos los archivos cargados por adjudicaciones con tipo de archivos que sean 1 (reparos dncp)                
        $award_filedncp = $order->files()->where('dependency_id', $award_dependency)->where('file_type', '=', 1)->get();

        // Obtenemos los archivos cargados por adjudicaciones con tipo de archivos que sean 6 (cosultas dncp)
        $award_filedncp_con = $order->files()->where('dependency_id', $award_dependency)->where('file_type', '=', 6)->get();

        // Obtenemos las protestas cargadas en la dncp
        $objections = $order->objections;

        // Obtenemos las consultas cargadas en la dncp
        $queries = $order->queries;
        // Obtenemos las protestas cargadas en la dncp
        $objections = $order->objections;

        return view('award.awards.show', compact('order', 'related_simese','queries', 
                    'related_simese_award', 'other_files', 'award_files', 'objections',
                    'award_filedncp','award_filedncp_con'));
    }

     //Para mostrar un archivo EXCEL guardado en el Proyecto items de Adjudicaciones
     public function ArchivoItemAw(){
        header("Content-type: application/xlsx");
        header("Content-Disposition: inline; filename=64-Importar_Items.xlsx");
        readfile("files/64-Importar_Items.xlsx");        
    }

    /**
     * Recibir un pedido Primera Etapa
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrder(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de recibir pedido
        if(!$request->user()->hasPermission(['awards.orders.recibe'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);

        // Si estado es igual a 160: V°B°-VERIFICADO DICTAMEN DE EVALUACIÓN EN DGAF        
        // Estado 65 = RECIBIDO ADJUDICACIONES 1RA ETAPA
        $order->actual_state = 65;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 65;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha recibido exitosamente el pedido. '], 200);
    }

    /**
     * Recibir un pedido Segunda Etapa
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrder2ET(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de recibir pedido
        if(!$request->user()->hasPermission(['awards.orders.recibe'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Si estado es igual a 80: PROCESADO CONTRATOS
        // Estado 85 = RECIBIDO ADJUDICACIONES para 2dA ETAPA
        $order->actual_state = 85;                
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 85;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha recibido exitosamente el pedido.'], 200);
    }

     /*** Formulario de modificacion de pedido *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response */
    public function edit(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        // chequeamos que el usuario tenga permisos para editar el pedido
        if($request->user()->hasPermission(['awards.orders.update'])){
            return view('award.orders.update', compact('order'));
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

        return redirect()->route('awards.show', $order_id)->with('success', 'Llamado modificado correctamente');;        
        
    }

    /**
     * Derivar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveOrder(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['awards.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);        
        
        if ($order->actual_state == 65) {
            // Cambia a estado 70-PROCESADO EN ADJUDICACIONES - 1RA ETAPA
            $order->actual_state = 70;
            $order->save();

            // Registramos el movimiento de estado en la tabla orders_order_state
            $order_order_state = new OrderOrderState;
            $order_order_state->order_id = $order->id;
            $order_order_state->order_state_id = 70;
            $order_order_state->creator_user_id = $request->user()->id;
            $order_order_state->save();

            return response()->json(['status' => 'success', 'message' => 'Procesado en Adjudicaciones-1ra. Etapa'], 200);
        }

        if ($order->actual_state == 85) {
            // Cambia a estado 90-PROCESADO EN ADJUDICACIONES-2DA ETAPA
            $order->actual_state = 90;
            $order->save();

            // Registramos el movimiento de estado en la tabla orders_order_state
            $order_order_state = new OrderOrderState;
            $order_order_state->order_id = $order->id;
            $order_order_state->order_state_id = 90;
            $order_order_state->creator_user_id = $request->user()->id;
            $order_order_state->save();
            
            return response()->json(['status' => 'success', 'message' => 'Finalizado Proceso de Adjudicación'], 200);        
        }
    }
    
    /**
     * Derivar Pedido a PAC
     *
     * @return \Illuminate\Http\Response
     */
    public function derivePAC(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de recibir pedido
        if(!$request->user()->hasPermission(['awards.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 68 = DERIVADO A PAC POR OBSERVACIONES EN ADJUDICACICONES
        $order->actual_state = 68;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 68;
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
        if(!$request->user()->hasPermission(['awards.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 69 = DERIVADO A COMITE POR OBSERVACIONES EN ADJUDICACICONES
        $order->actual_state =  69;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 69;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();
        
        return response()->json(['status' => 'success', 'message' => 'Antecedentes derivado a Comité Evaluador'], 200);
    }
        
    /**
     * Actualizar fecha tope de respuesta a comunicación de adjudicación a DNCP
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
            return response()->json(['status' => 'error', 'message' => 'Advertencia! Debe enviar una fecha de comunicación de adjudicación a DNCP válida.'], 200);
        }

        $order->queries_deadline_adj = date("Y-m-d", strtotime(str_replace("/", "-", $request->input('queries_deadline'))));
        $order->save();

        return response()->json(['status' => 'success', 'message' => 'Fecha de comunicación de adjudicación a DNCP actualizada correctamente.'], 200);
    }
   
       
    /**
     * Obtener notificaciones de alertas
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getNotifications(Request $request)
    {
        // obtenemos los pedidos que se han recibido en Adjudicaciones 1° etapa (ESTADO = 65)
        $orders = Order::where('actual_state', [65])        
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
            if(empty($order->queries_deadline_adj)){
                continue;
            }

            // definimos fecha de recepcion de consultas
            $dia_apertura_sobres = date('N', strtotime($order->queries_deadline_adj));//con esto se obtiene el número del día de la semana
            $limite_mayor_consultas = strtotime($order->queries_deadline_adj. ' +7 days');
            $fecha_adj = date("d-m-Y",strtotime($order->queries_deadline_adj));

            if($hoy <= $limite_mayor_consultas){
                $segundos_llegar_tope = $limite_mayor_consultas - $hoy;                
                $dias_tope_consultas = floor(abs($segundos_llegar_tope / 60 / 60 / 24 ));                 
                $pac_id = number_format($order->dncp_pac_id,0,",",".");
                $fecha_fin = date("d-m-Y", $limite_mayor_consultas);                             
                array_push($alerta_consultas, array('pac_id' => $pac_id,'llamado' => $order->number, 'dias' => $dias_tope_consultas, 'fecha_fin' => $fecha_fin, 'fecha_ini' => $fecha_adj));
            }
        }

        // return response()->json(['status' => 'success', 'alerta_consultas' => $alerta_consultas,'alerta_aclaraciones' => $alerta_aclaraciones], 200);
        return response()->json(['status' => 'success', 'alerta_consultas' => $alerta_consultas,'alerta_aclaraciones' => $alerta_aclaraciones], 200);
    }   

    // CODIGO ORIGINAL
    // /**
    //  * Obtener notificaciones de alertas
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function getNotifications(Request $request)
    // {
    //     // obtenemos los pedidos que se han recibido en Adjudicaciones 1° etapa (ESTADO = 65)
    //     $orders = Order::where('actual_state', [65])        
    //                     ->get(); 
        
    //     // Por cada orden verificamos fecha tope y consultas sin responder
    //     $alerta_consultas = array();
    //     $alerta_aclaraciones = array();
    //     $tope_recepcion_consultas = 0;
    //     $dias_tope_consultas = 0;
    //     $dias_tope_aclaraciones = 0;
    //     $hoy = strtotime(date('Y-m-d'));
    //     foreach($orders as $order){
    //         // en caso de no haber cargado la fecha tope continuamos con el siguiente pedido
    //         if(empty($order->queries_deadline_adj)){
    //             continue;
    //         }

    //         // definimos fecha de recepcion de consultas
    //         $dia_apertura_sobres = date('N', strtotime($order->queries_deadline_adj));
    //         // lunes, martes, miercoles
    //         if($dia_apertura_sobres >= 1 && $dia_apertura_sobres <= 3){
    //             $tope_recepcion_consultas = 3 + 2;
    //         }
    //         // jueves, viernes, sabado
    //         if($dia_apertura_sobres >= 4 && $dia_apertura_sobres <= 6){
    //             $tope_recepcion_consultas = 3;
    //         }
    //         // domingo
    //         if($dia_apertura_sobres == 7){
    //             $tope_recepcion_consultas = 3 + 1;
    //         }

    //         // definimos fecha de aclaracion de consultas
    //         // lunes
    //         if($dia_apertura_sobres == 1){
    //             $tope_aclaraciones = 1 + 2;
    //         }
    //         // martes, miercoles, jueves, viernes, sabado
    //         if($dia_apertura_sobres >= 2 && $dia_apertura_sobres <= 6){
    //             $tope_aclaraciones = 1;
    //         }
    //         // domingo
    //         if($dia_apertura_sobres == 7){
    //             $tope_aclaraciones = 1 + 1;
    //         }

    //         // definimos dias de aviso recepcion de consultas
    //         $apertura_sobres = strtotime($order->queries_deadline_adj);
    //         $limite_mayor_consultas = strtotime($order->queries_deadline_adj . ' -'.$tope_recepcion_consultas.' days');
    //         $dias_aviso = $tope_recepcion_consultas + 5;
    //         $limite_menor_consultas = strtotime($order->queries_deadline_adj . ' -'.$dias_aviso.' days');
    //         if($hoy <= $limite_mayor_consultas && $hoy >= $limite_menor_consultas){
    //             $segundos_llegar_tope = $limite_mayor_consultas - $hoy;
    //             $dias_tope_consultas = floor(abs($segundos_llegar_tope / 60 / 60 / 24 )); 
    //             // array_push($alerta_consultas, array('llamado' => $order->number, 'dias' => $dias_tope_consultas));
    //             array_push($alerta_consultas, array('pac_id' => $order->dncp_pac_id,'llamado' => $order->number, 'dias' => $dias_tope_consultas));
    //         }
            
    //         // definimos dias de aviso aclaracion de consultas
    //         $limite_mayor_aclaraciones = strtotime($order->queries_deadline_adj . ' -'.$tope_aclaraciones.' days');
    //         $dias_aviso = $tope_aclaraciones + 5;
    //         $limite_menor_aclaraciones = strtotime($order->queries_deadline_adj . ' -'.$dias_aviso.' days');
    //         if($hoy <= $limite_mayor_aclaraciones && $hoy >= $limite_menor_aclaraciones){
    //             $segundos_llegar_tope = $limite_mayor_aclaraciones - $hoy;
    //             $dias_tope_aclaraciones = floor(abs($segundos_llegar_tope / 60 / 60 / 24 )); 
    //         }

    //         // chequeamos si el pedido esta por llegar a fecha tope de aclaracion de consultas
    //         // y tiene consultas sin ser respondidas
    //         $consultas_faltantes_respuesta = $order->queries->where('answered', false)->count();
    //         if($consultas_faltantes_respuesta > 0 && $dias_tope_aclaraciones > 0){
    //             array_push($alerta_aclaraciones, array('llamado' => $order->number, 
    //                 'consultas_pendientes' => $consultas_faltantes_respuesta,
    //                 'dias' => $dias_tope_aclaraciones));
    //         }
    //     }

    //     return response()->json(['status' => 'success', 'alerta_consultas' => $alerta_consultas,
    //                             'alerta_aclaraciones' => $alerta_aclaraciones], 200);
    // }   

}
