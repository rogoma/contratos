<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Contract;
use App\Models\OrderOrderState;

class ContractsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:contracts.orders.index,admin.orders.index')->only('index'); // Permiso para index
        $this->middleware('checkPermission:contracts.orders.show,admin.orders.show')->only('show'); // Permiso para show
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Mostramos código >= 70 PROCESADO EN ADJUDICACIONES - 1RA ETAPA)
        $orders = Contract::where('actual_state', '>=', 0)
                        //  ->where('actual_state', '<=', 80)
                         ->get();
        return view('contract.contracts.index', compact('orders'));
    }

    /**
     * Visualizar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $order_id)
    {
        $order = Order::find($order_id);

        $contract_dependency = $request->user()->dependency_id;
        // Obtenemos los simese cargados por otras dependencias
        $related_simese = $order->simese()->where('dependency_id', '!=', $contract_dependency)->get();
        // Obtenemos los simese cargados por contratos
        $related_simese_contract = $order->simese()->where('dependency_id', $contract_dependency)->get();

        // Obtenemos los archivos cargados por otras dependencias y que no sean de reparo
        $other_files = $order->files()->where('dependency_id', '!=', $contract_dependency)
                                      ->whereIn('file_type', [0, 5, 7])//0-antecedentes 3-contratos 4-addendas  5-dictamenes
                                      ->orderBy('created_at','asc')
                                      ->get();

        // Obtenemos los archivos cargados por contratos con tipo de archivos 3 (CONTRATOS)
        $contract_files = $order->files()->where('dependency_id', $contract_dependency)->where('file_type', '=', 3)->get();
        // Obtenemos los archivos cargados por contratos con tipo de archivos 4 (ADDENDAS)
        $contract_filedncp = $order->files()->where('dependency_id', $contract_dependency)->where('file_type', '=', 4)->get();

        // Obtenemos las protestas cargadas en la dncp
        $objections = $order->objections;

        return view('contract.contracts.show', compact('order', 'related_simese',
                    'related_simese_contract', 'other_files', 'contract_files', 'objections','contract_filedncp'));
    }

    /**
     * Recibir un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function recibeOrder(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de recibir pedido
        if(!$request->user()->hasPermission(['contracts.orders.recibe'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 75 = RECIBIDO CONTRATOS
        $order->actual_state = 75;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 75;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha recibido exitosamente el pedido. '], 200);
    }

    /*** Formulario de modificacion de pedido *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response */
    public function edit(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        // chequeamos que el usuario tenga permisos para editar el pedido
        if($request->user()->hasPermission(['contracts.orders.update'])){
            return view('contract.orders.update', compact('order'));
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

        return redirect()->route('contracts.show', $order_id)->with('success', 'Llamado modificado correctamente');;
    }

    /**
     * Derivar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function deriveOrder(Request $request, $order_id){
        // Chequeamos que el usuario actual disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['contracts.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $order = Order::find($order_id);
        // Estado 80 = PROCESADO CONTRATOS
        $order->actual_state = 80;
        $order->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $order_order_state = new OrderOrderState;
        $order_order_state->order_id = $order->id;
        $order_order_state->order_state_id = 80;
        $order_order_state->creator_user_id = $request->user()->id;
        $order_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha derivado exitosamente el pedido. '], 200);
    }

    /**
     * Obtener notificaciones de alertas
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getNotifications(Request $request)
    {
        // obtenemos los pedidos que se han recibido en Contratos (ESTADO = 75)
        $orders = Order::where('actual_state', [75])
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
            $dia_apertura_sobres = date('N', strtotime($order->queries_deadline_adj));
            // dias habiles, sumamos sabados y domingos (4 sabados y 4 domingos)
            $tope_recepcion_consultas = 20 + 8;

            // definimos dias de aviso recepcion de consultas
            $limite_mayor_consultas = strtotime($order->queries_deadline_adj . ' +'.$tope_recepcion_consultas.' days');
            $dias_aviso = $tope_recepcion_consultas - 7;
            $limite_menor_consultas = strtotime($order->queries_deadline_adj . ' +'.$dias_aviso.' days');

            if($hoy <= $limite_mayor_consultas && $hoy >= $limite_menor_consultas){
                $segundos_llegar_tope = $limite_mayor_consultas - $hoy;
                $dias_tope_consultas = floor(abs($segundos_llegar_tope / 60 / 60 / 24 ));
                $pac_id = number_format($order->dncp_pac_id,0,",",".");
                $fecha_ini = date("d-m-Y", $limite_menor_consultas);
                $fecha_fin = date("d-m-Y", $limite_mayor_consultas);
                array_push($alerta_consultas, array('pac_id' => $pac_id,'llamado' => $order->number, 'dias' => $dias_tope_consultas, 'fecha_fin' => $fecha_fin, 'fecha_ini' => $fecha_ini));
            }
        }

        // return response()->json(['status' => 'success', 'alerta_consultas' => $alerta_consultas,'alerta_aclaraciones' => $alerta_aclaraciones], 200);
        return response()->json(['status' => 'success', 'alerta_consultas' => $alerta_consultas,'alerta_aclaraciones' => $alerta_aclaraciones], 200);
    }
}
