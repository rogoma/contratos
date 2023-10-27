<?php

namespace App\Http\Controllers\Coordination;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Objection;
use App\Models\ObjectionResponse;

class ObjectionsResponsesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:legal_advices.objections.create')->only(['create', 'store']); // Permiso para create 
        $this->middleware('checkPermission:legal_advices.objections.update')->only(['edit', 'update']); // Permiso para update 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, $objection_id)
    {
        $objection = Objection::findOrFail($objection_id);
        return view('legal_advice.objections_responses.create', compact('objection'));
    }

    /**
     * Funcionalidad de agregar una respuesta a un reparo.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $objection_id)
    {
        $objection = Objection::findOrFail($objection_id);
        
        $rules = array(
            'response' => 'string|required|max:300',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $objection_response = new ObjectionResponse;
        $objection_response->response = $request->input('response');
        $objection_response->objection_id = $objection_id;
        $objection_response->creator_user_id = $request->user()->id;  // usuario logueado
        $objection_response->creator_dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $objection_response->save();

        return redirect()->route('legal_advices.show', $objection->order_id)->with('success', 'Respuesta a reparo agregada correctamente');
    }

    /**
     * Show the form for editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $objection_id, $objection_response_id)
    {
        $objection = Objection::findOrFail($objection_id);
        $objection_response = ObjectionResponse::findOrFail($objection_response_id);
        return view('legal_advice.objections_responses.update', compact('objection', 'objection_response'));
    }

    /**
     * Funcionalidad para modificar una respuesta a un reparo.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $objection_id, $objection_response_id)
    {
        $objection = Objection::findOrFail($objection_id);
        $objection_response = ObjectionResponse::findOrFail($objection_response_id);
        
        $rules = array(
            'response' => 'string|required|max:300',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $objection_response->response = $request->input('response');
        $objection_response->objection_id = $objection_id;
        $objection_response->modifier_user_id = $request->user()->id;  // usuario logueado
        $objection_response->modifier_dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $objection_response->save();

        return redirect()->route('legal_advices.show', $objection->order_id)->with('success', 'Respuesta a reparo modificada correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $objection_id, $objection_response_id)
    {
        // Chequeamos que el usuario actual disponga de permisos de eliminacion
        if(!$request->user()->hasPermission(['legal_advices.objections.delete'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.', 'code' => 200], 200);
        }

        $objection_response = ObjectionResponse::find($objection_response_id);
        // Eliminamos la respuesta al reparo
        $objection_response->delete();
        return response()->json(['status' => 'success', 'message' => 'Se ha eliminado la respuesta al reparo ', 'code' => 200], 200);
    }
}
