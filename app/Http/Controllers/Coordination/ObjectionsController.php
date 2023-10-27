<?php

namespace App\Http\Controllers\Coordination;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Order;
use App\Models\Objection;

class ObjectionsController extends Controller
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
    public function create(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        return view('legal_advice.objections.create', compact('order'));
    }

    /**
     * Funcionalidad de agregar de reparo.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $order_id)
    {
        $order = Order::findOrFail($order_id);
        
        $rules = array(
            'objection' => 'string|required|max:300',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $objection = new Objection;
        $objection->objection = $request->input('objection');
        $objection->order_id = $order_id;
        $objection->order_state_id = $order->actual_state;
        $objection->creator_user_id = $request->user()->id;  // usuario logueado
        $objection->creator_dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $objection->save();

        return redirect()->route('legal_advices.show', $order_id)->with('success', 'Reparo agregado correctamente');
    }

    /**
     * Show the form for editing a resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $order_id, $objection_id)
    {
        $objection = Objection::findOrFail($objection_id);
        return view('legal_advice.objections.update', compact('objection'));
    }

    /**
     * Funcionalidad para modificar reparo.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_id, $objection_id)
    {
        $objection = Objection::findOrFail($objection_id);
        
        $rules = array(
            'objection' => 'string|required|max:300',
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $objection->objection = $request->input('objection');
        $objection->modifier_user_id = $request->user()->id;  // usuario logueado
        $objection->modifier_dependency_id = $request->user()->dependency_id;  // dependencia del usuario
        $objection->save();

        return redirect()->route('legal_advices.show', $order_id)->with('success', 'Reparo modificado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $order_id, $objection_id)
    {
        // Chequeamos que el usuario actual disponga de permisos de eliminacion
        if(!$request->user()->hasPermission(['legal_advices.objections.delete'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.', 'code' => 200], 200);
        }

        $objection = Objection::find($objection_id);
        // Eliminamos las respuestas cargadas
        foreach($objection->objectionResponses as $obj_response){
            $obj_response->delete();
        }
        // Eliminamos el reparo cargado
        $objection->delete();
        return response()->json(['status' => 'success', 'message' => 'Se ha eliminado el reparo ', 'code' => 200], 200);
    }
}
