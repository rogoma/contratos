<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Department;

class DepartmentsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('checkPermission:admin.departments.index')->only('index'); // Permiso para index 
        $this->middleware('checkPermission:admin.departments.create')->only(['create', 'store']);   // Permiso para create
        $this->middleware('checkPermission:admin.departments.update')->only(['edit', 'update']);   // Permiso para update
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $departments = Department::all();
        return view('admin.departments.index', compact('departments'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.departments.create');
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
            'coddpto' => 'required|numeric|max:999|unique:departments',          
            'nomdpto' => 'string|required|max:25'
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        //SE CAPTURA CODIGO DE LA VISTA
        $code = $request->input('coddpto');

        //CHEQUEAMOS QUE CODIGO NO EXISTA EN LA TABLA    
        $check = Department::where('coddpto', $code)->count();        
        if($check > 0){
            $validator->errors()->add('coddpto', 'Código ya existe, Verifique.');
            return back()->withErrors($validator)->withInput();
        }   

        $department = new Department;
        $department->coddpto = $request->input('coddpto');        
        $department->nomdpto = $request->input('nomdpto');
        $department->creator_user_id = $request->user()->id;  // usuario logueado
        $department->save();

        return redirect()->route('departments.index')->with('success', 'Departmento agregado correctamente');
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
        $department = Department::find($id);
        return view('admin.departments.update', compact('department'));
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
            //'coddpto' => 'required|numeric|max:999|unique:departments',           
            'nomdpto' => 'string|required|max:25'
        );
        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // obtenemos la región
        $department = Department::find($id);
        $department->coddpto = $request->input('coddpto');        
        $department->nomdpto = $request->input('nomdpto');
        $department->modifier_user_id = $request->user()->id;  // usuario logueado
        $department->save();        

        return redirect()->route('departments.index')->with('success', 'Departmento modificado correctamente');
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
        if(!$request->user()->hasPermission(['admin.departments.delete'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.', 'code' => 200], 200);
        }

        $department = Department::find($id);
        
        // Eliminamos en caso de no existir usuarios vinculados a la región
        $department->delete();
        return response()->json(['status' => 'success', 'message' => 'Se ha eliminado el Departmento ' . $department->nomdpto, 'code' => 200], 200);
    }
}
