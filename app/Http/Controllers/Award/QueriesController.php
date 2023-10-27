<?php

namespace App\Http\Controllers\Award;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Query;

class QueriesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:tenders.objections.create')->only(['create', 'store']); // Permiso para create 
        $this->middleware('checkPermission:tenders.objections.update')->only(['edit', 'update']); // Permiso para update 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('tender.queries.create', compact('order'));
    }

    /**
     * Funcionalidad de agregar de Consulta.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        
        $rules = array(
            'query' => 'string|required|max:300',
            'query_date' => 'date_format:d/m/Y|required',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $query = new Query;
        $query->query = $request->input('query');
        $query->query_date = date('Y-m-d', strtotime(str_replace("/", "-", $request->input('query_date'))));
        $query->order_id = $order_id;
        $query->order_state_id = $order->actual_state;
        $query->creator_user_id = $request->user()->id;  // usuario logueado
        $query->creator_dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $query->save();

        return redirect()->route('tenders.show', $order_id)->with('success', 'Consulta agregada correctamente');
    }

    /**
     * Show the form for editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $order_id, $query_id)
    {
        $query = Query::findOrFail($query_id);
        return view('tender.queries.update', compact('query'));
    }

    /**
     * Funcionalidad para modificar Consulta.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id, $query_id)
    {
        $query = Query::findOrFail($query_id);
        
        $rules = array(
            'query' => 'string|required|max:300',
            'query_date' => 'date_format:d/m/Y|required',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $query->query = $request->input('query');
        $query->query_date = date('Y-m-d', strtotime(str_replace("/", "-", $request->input('query_date'))));
        $query->modifier_user_id = $request->user()->id;  // usuario logueado
        $query->modifier_dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $query->save();

        return redirect()->route('tenders.show', $order_id)->with('success', 'Consulta modificada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $order_id, $query_id)
    {
        // Chequeamos que el usuario actual disponga de permisos de eliminacion
        if(!$request->user()->hasPermission(['tenders.objections.delete'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.', 'code' => 200], 200);
        }

        $query = Query::find($query_id);
        // Eliminamos las respuestas cargadas
        foreach($query->queryResponses as $obj_response){
            $obj_response->delete();
        }
        // Eliminamos la consulta cargado
        $query->delete();
        return response()->json(['status' => 'success', 'message' => 'Se ha eliminado la consulta ', 'code' => 200], 200);
    }
}
