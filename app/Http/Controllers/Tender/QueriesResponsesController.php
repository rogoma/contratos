<?php

namespace App\Http\Controllers\Tender;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Query;
use App\Models\QueryResponse;

class QueriesResponsesController extends Controller
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
    public function create(Request $request, $query_id)
    {
        $query = Query::findOrFail($query_id);
        return view('tender.queries_responses.create', compact('query'));
    }

    /**
     * Funcionalidad de agregar una respuesta a un consulta.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $query_id)
    {
        $query = Query::findOrFail($query_id);
        
        $rules = array(
            'response' => 'string|required|max:300',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $query_response = new QueryResponse;
        $query_response->response = $request->input('response');
        $query_response->query_id = $query_id;
        $query_response->creator_user_id = $request->user()->id;  // usuario logueado
        $query_response->creator_dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $query_response->save();
        
        // en caso de la consulta tener el campo respondido igual a false lo cambiamos a true
        if($query_response->queryParent->answered == false){
            $query_response->queryParent->answered = true;
            $query_response->queryParent->save();
        }

        return redirect()->route('tenders.show', $query->order_id)->with('success', 'Respuesta a consulta agregada correctamente');
    }

    /**
     * Show the form for editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $query_id, $query_response_id)
    {
        $query = Query::findOrFail($query_id);
        $query_response = QueryResponse::findOrFail($query_response_id);
        return view('tender.queries_responses.update', compact('query', 'query_response'));
    }

    /**
     * Funcionalidad para modificar una respuesta a un consulta.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $query_id, $query_response_id)
    {
        $query = Query::findOrFail($query_id);
        $query_response = QueryResponse::findOrFail($query_response_id);
        
        $rules = array(
            'response' => 'string|required|max:300',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $query_response->response = $request->input('response');
        $query_response->query_id = $query_id;
        $query_response->modifier_user_id = $request->user()->id;  // usuario logueado
        $query_response->modifier_dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $query_response->save();

        return redirect()->route('tenders.show', $query->order_id)->with('success', 'Respuesta a consulta modificada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $query_id, $query_response_id)
    {
        // Chequeamos que el usuario actual disponga de permisos de eliminacion
        if(!$request->user()->hasPermission(['tenders.objections.delete'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.', 'code' => 200], 200);
        }

        $query_response = QueryResponse::find($query_response_id);
        // Eliminamos la respuesta a la consulta
        $query_response->delete();

        // en caso de haber sido la unica respuesta de la consulta cambiamos el campo respondido a false
        if($query_response->queryParent->queryResponses->count() == 0){
            $query_response->queryParent->answered = false;
            $query_response->queryParent->save();
        }
        return response()->json(['status' => 'success', 'message' => 'Se ha eliminado la respuesta a la consulta ', 'code' => 200], 200);
    }
}
