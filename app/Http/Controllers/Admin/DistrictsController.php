<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\District;

class DistrictsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __constcoddistt()
    {
        $this->middleware('checkPermission:admin.districts.index')->only('index'); // Permiso para index
        $this->middleware('checkPermission:admin.districts.create')->only(['create', 'store']);   // Permiso para create
        $this->middleware('checkPermission:admin.districts.update')->only(['edit', 'update']);   // Permiso para update
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $districts = District::all();
        return view('admin.districts.index', compact('districts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.districts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'nomdist' => 'string|required|max:100',
            'coddist' => 'string|required|max:15'
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $district = new District;
        $district->nomdist = $request->input('nomdist');
        $district->coddist = $request->input('coddist');
        $district->creator_user_id = $request->user()->id;  // usuario logueado
        $district->save();

        return redirect()->route('districts.index')->with('success', 'Distrito agregado correctamente');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $district = District::find($id);
        return view('admin.districts.update', compact('district'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = array(
            'nomdist' => 'string|required|max:100',
            'coddist' => 'string|required|max:15'
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // obtenemos el proveedor
        $district = District::find($id);
        $district->nomdist = $request->input('nomdist');
        $district->coddist = $request->input('coddist');
        $district->modifier_user_id = $request->user()->id;  // usuario logueado
        $district->save();

        return redirect()->route('districts.index')->with('success', 'Distrito modificado correctamente');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // Chequeamos que el usuario actual disponga de permisos de eliminacion
        if(!$request->user()->hasPermission(['admin.districts.delete'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.', 'code' => 200], 200);
        }


        $district = District::find($id);

        // Chequeamos si existen usuarios referenciando a departamentos
        if($district->department->count() > 0){
            return response()->json(['status' => 'error', 'message' => 'No se ha podido eliminar el distrito debido a que se encuentra vinculada a Departamentos ', 'code' => 200], 200);
        }

        // Eliminamos en caso de no existir usuarios vinculados al proveedor
        $district->delete();
        return response()->json(['status' => 'success', 'message' => 'Se ha eliminado el Distrito ' . $district->nomdist, 'code' => 200], 200);
    }
}
