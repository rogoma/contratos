<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Region;

class RegionsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:admin.regions.index')->only('index'); // Permiso para index 
        $this->middleware('checkPermission:admin.regions.create')->only(['create', 'store']);   // Permiso para create
        $this->middleware('checkPermission:admin.regions.update')->only(['edit', 'update']);   // Permiso para update
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regions = Region::all();
        return view('admin.regions.index', compact('regions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.regions.create');
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
            'codreg' => 'required',
            'subcreg' => 'string|required|max:3',
            'nomreg' => 'string|required|max:25'
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $region = new Region;
        $region->codreg = $request->input('codreg');
        $region->subcreg = $request->input('subcreg');
        $region->nomreg = $request->input('nomreg');
        $region->creator_user_id = $request->user()->id;  // usuario logueado
        $region->save();

        return redirect()->route('regions.index')->with('success', 'Región agregado correctamente');
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
        $region = Region::find($id);
        return view('admin.regions.update', compact('region'));
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
            'codreg' => 'required',
            'subcreg' => 'string|required|max:3',
            'nomreg' => 'string|required|max:25'
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // obtenemos la región
        $region = Region::find($id);
        $region->codreg = $request->input('codreg');
        $region->subcreg = $request->input('subcreg');
        $region->nomreg = $request->input('nomreg');
        $region->modifier_user_id = $request->user()->id;  // usuario logueado
        $region->save();        

        return redirect()->route('regions.index')->with('success', 'Región modificado correctamente');
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
        if(!$request->user()->hasPermission(['admin.regions.delete'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.', 'code' => 200], 200);
        }

        $region = Region::find($id);
        
        // Eliminamos en caso de no existir usuarios vinculados a la región
        $region->delete();
        return response()->json(['status' => 'success', 'message' => 'Se ha eliminado la Región ' . $region->nomreg, 'code' => 200], 200);
    }
}
