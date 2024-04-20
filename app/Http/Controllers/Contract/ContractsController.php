<?php

namespace App\Http\Controllers\Contract;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Contract;
use App\Models\Order;
use App\Models\OrderMultiYear;
use App\Models\Dependency;
use App\Models\Modality;
use App\Models\Provider;
use App\Models\SubProgram;
use App\Models\FundingSource;
use App\Models\FinancialOrganism;
use App\Models\ExpenditureObject;
use App\Models\OrderOrderState;
use App\Models\ContractState;
use App\Models\ContractType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Illuminate\Support\Facades\DB;
use App\Exports\OrdersExport;
use App\Exports\OrdersExport2;
use App\Exports\OrdersExport3;

use App\Rules\ValidarCalendarios;

use Maatwebsite\Excel\Facades\Excel;
// use Illuminate\Support\Carbon;
use Carbon\Carbon;


class ContractsController extends Controller
{
    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $index_permissions = ['admin.orders.index','contracts.contracts.index','derive_contracts.contracts.show'];
        $create_permissions = ['admin.orders.create','contracts.contracts.create'];
        $update_permissions = ['admin.orders.update', 'contracts.contracts.update'];

        $this->middleware('checkPermission:'.implode(',',$index_permissions))->only('index'); // Permiso para index
        $this->middleware('checkPermission:'.implode(',',$create_permissions))->only(['create', 'store']);   // Permiso para create
        $this->middleware('checkPermission:'.implode(',',$update_permissions))->only(['edit', 'update']);   // Permiso para update
    }

    public function calculo(Request $request)
    {
        dd($request->date);
        // Envía el resultado a la vista
        // return view('contract.contracts.create');
    }

    /**
     * Listado de todos los pedidos.
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Mostramos código >= 70 PROCESADO EN ADJUDICACIONES - 1RA ETAPA)
        $contracts = Contract::where('contract_state_id', '>=', 1)
                    ->orderBy('iddncp','asc')
                    ->get();
        // $contracts = DB::select('Select * from  contracts  where contract_state_id > 0' orderby );
        return view('contract.contracts.index', compact('contracts'));

        $dependency = $request->user()->dependency_id;
    }

    //Para exportar a Excel pedidos encurso aún sn adjudicación
    public function exportarExcel()
    {
        return Excel::download(new OrdersExport, 'pedidos.xlsx');

    }

    //Para exportar a Excel pedidos adjudicados
    public function exportarExcel2()
    {
        return Excel::download(new OrdersExport2, 'pedidos_adjudicados.xlsx');

    }

    //Para exportar a Excel usuarios
    public function exportarExcel3()
    {
        return Excel::download(new OrdersExport3, 'usuarios.xlsx');

    }

    /**
     * Formulario de agregacion de pedido.     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        // $fechaActual = Carbon::now()->toDateString(); // Obtener la fecha actual en formato YYYY-MM-DD
        $dependencies = Dependency::all();
        $modalities = Modality::all();
        $sub_programs = SubProgram::all();
        $funding_sources = FundingSource::all();
        $financial_organisms = FinancialOrganism::all();
        $expenditure_objects = ExpenditureObject::where('level', 3)->get();
        $providers = Provider::all();//se podria filtrar por estado sólo activo
        $contr_states = ContractState::all();
        $contract_types = ContractType::all();
        return view('contract.contracts.create', compact('dependencies', 'modalities','sub_programs', 'funding_sources', 'financial_organisms',
        'expenditure_objects', 'providers', 'contr_states','contract_types'));
    }

    /**
     * Formulario de agregacion de pedido cargando archivo excel.     *
     * @return \Illuminate\Http\Response
     */
    public function uploadExcel()
    {
        return view('contract.contracts.uploadExcel');
    }

    /**
     * Funcionalidad de guardado del pedido.     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'description' => 'string|required|max:300',
            'iddncp' => 'string|required|max:999999|min:7',
            'linkdncp' => 'string|required|max:300',
            'number_year' => 'string|required|max:9',
            'year_adj' => 'numeric|required|max:9999',
            'sign_date' => 'date_format:d/m/Y|required',
            'provider_id' => 'numeric|required|max:999999',
            'contract_state_id' => 'numeric|required|max:999999',
            'modality_id' => 'numeric|required|max:999999',
            'financial_organism_id' => 'numeric|required|max:999999',
            'contract_type_id' => 'numeric|required|max:999999',
            'total_amount' => 'string|required|max:9223372036854775807',
            'advance_validity_from' => 'nullable|date_format:d/m/Y',
            'advance_validity_to' => 'nullable|date_format:d/m/Y',
            'fidelity_validity_from' => 'nullable|date_format:d/m/Y',
            'fidelity_validity_to' => 'nullable|date_format:d/m/Y',
            'accidents_validity_from' => 'nullable|date_format:d/m/Y',
            'accidents_validity_to' => 'nullable|date_format:d/m/Y',
            'risks_validity_from' => 'nullable|date_format:d/m/Y',
            'risks_validity_to' => 'nullable|date_format:d/m/Y',
            'civil_resp_validity_from' => 'nullable|date_format:d/m/Y',
            'civil_resp_validity_to' => 'nullable|date_format:d/m/Y',
            'comments' => 'nullable|max:300',
            'control_1' => 'nullable|numeric',
            'control_a' => 'nullable|numeric',
            'control_2' => 'nullable|numeric',
            'control_b' => 'nullable|numeric',
            'control_3' => 'nullable|numeric',
            'control_c' => 'nullable|numeric',
            'control_4' => 'nullable|numeric',
            'control_d' => 'nullable|numeric',
            'control_5' => 'nullable|numeric',
            'control_e' => 'nullable|numeric'
        );

        $validator =  Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $contract = new Contract;

        $contract->description=$request->input('description');

        $iddncp_fin = str_replace('.', '',($request->input('iddncp')));

        if ($iddncp_fin > 999999) {
            $validator->errors()->add('iddncp', 'Número no debe sobrepasar 999.999');
            return back()->withErrors($validator)->withInput()->with('fila');
        }else{
            $contract->iddncp = $iddncp_fin;
        }

        $contract->linkdncp=$request->input('linkdncp');
        $contract->number_year=$request->input('number_year');
        $year_adj_fin = str_replace('.', '',($request->input('year_adj')));
        $contract->year_adj = $year_adj_fin;
        $contract->sign_date = date('Y-m-d', strtotime(str_replace("/", "-", $request->input('sign_date'))));
        $contract->provider_id=$request->input('provider_id');
        $contract->contract_state_id=$request->input('contract_state_id');
        $contract->modality_id=$request->input('modality_id');
        $contract->financial_organism_id=$request->input('financial_organism_id');
        $contract->contract_type_id=$request->input('contract_type_id');

        $total_amount_fin = str_replace('.', '',($request->input('total_amount')));
        if ($total_amount_fin <= 0 ) {
            $validator->errors()->add('total_amount', 'Monto no puede ser cero ni negativo');
            return back()->withErrors($validator)->withInput();
        }else{
            $contract->total_amount = $total_amount_fin;
        }

        //CONTROLAR QUE LAS FECHAS SI SON VACIAS GRABEN NULL

        //ANTICIPOS
        $advanceValidityFrom = $request->input('advance_validity_from');
        $advanceValidityTo = $request->input('advance_validity_to');
        if (is_null($advanceValidityFrom) && !empty($advanceValidityTo)) {
            $validator->errors()->add('advance_validity_from', 'Por favor, seleccione una fecha para adavance_validity_from');
            return back()->withErrors($validator)->withInput();
        }
        if (!empty($advanceValidityFrom) && is_null($advanceValidityTo)) {
            $validator->errors()->add('advance_validity_to', 'Por favor, seleccione una fecha para adavance_validity_to');
            return back()->withErrors($validator)->withInput();
        }

        if (empty($advanceValidityFrom) && is_null($advanceValidityTo)) {
            $contract->advance_validity_from=null;
            $contract->advance_validity_to=null;
            $contract->control_1=null;
            $contract->control_a=null;
        }else{
            $contract->advance_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('advance_validity_from'))));
            $contract->advance_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('advance_validity_to'))));

            //control para grabar control_1 y control_a
            $control_1 = $request->input('control_1');
            $control_a = $request->input('control_a');
            if ($control_a < 0 ) {
                $validator->errors()->add('control_a', 'Número no puede ser negativo');
                return back()->withErrors($validator)->withInput();
            }else{
                if (empty($control_1) || empty($control_a) ) {
                    $validator->errors()->add('control_a', 'ANTICIPOS - Dias Vigencia o a Vencer no pueden estar sin datos');
                    return back()->withErrors($validator)->withInput();
                }else{
                    $contract->control_1=$request->input('control_1');
                    $contract->control_a=$request->input('control_a');
                }
            }
        }

        //FIEL CUMPLIMIENTO
        $fidelityValidityFrom = $request->input('fidelity_validity_from');
        $fidelityValidityTo = $request->input('fidelity_validity_to');
        if (is_null($fidelityValidityFrom) && !empty($fidelityValidityTo)) {
            $validator->errors()->add('fidelity_validity_from', 'Por favor, seleccione una fecha para adavance_validity_from');
            return back()->withErrors($validator)->withInput();
        }
        if (!empty($fidelityValidityFrom) && is_null($fidelityValidityTo)) {
            $validator->errors()->add('fidelity_validity_to', 'Por favor, seleccione una fecha para adavance_validity_to');
            return back()->withErrors($validator)->withInput();
        }

        if (empty($fidelityValidityFrom) && is_null($fidelityValidityTo)) {
            $contract->fidelity_validity_from=null;
            $contract->fidelity_validity_to=null;
            $contract->control_2=null;
            $contract->control_b=null;
        }else{
            $contract->fidelity_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('fidelity_validity_from'))));
            $contract->fidelity_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('fidelity_validity_to'))));

            //control para grabar control_2 y control_b
            $control_2 = $request->input('control_2');
            $control_b = $request->input('control_b');
            if ($control_b < 0 ) {
                $validator->errors()->add('control_b', 'Número no puede ser negativo');
                return back()->withErrors($validator)->withInput();
            }else{
                if (empty($control_2) || empty($control_b) ) {
                    $validator->errors()->add('control_b', 'ANTICIPOS - Dias Vigencia o a Vencer no pueden estar sin datos');
                    return back()->withErrors($validator)->withInput();
                }else{
                    $contract->control_2=$request->input('control_2');
                    $contract->control_b=$request->input('control_b');
                }
            }
        }

        //ACCIDENTES
        $accidentsValidityFrom = $request->input('accidents_validity_from');
        $accidentsValidityTo = $request->input('accidents_validity_to');
        if (is_null($accidentsValidityFrom) && !empty($accidentsValidityTo)) {
            $validator->errors()->add('accidents_validity_from', 'Por favor, seleccione una fecha para adavance_validity_from');
            return back()->withErrors($validator)->withInput();
        }
        if (!empty($accidentsValidityFrom) && is_null($accidentsValidityTo)) {
            $validator->errors()->add('accidents_validity_to', 'Por favor, seleccione una fecha para adavance_validity_to');
            return back()->withErrors($validator)->withInput();
        }

        if (empty($accidentsValidityFrom) && is_null($accidentsValidityTo)) {
            $contract->accidents_validity_from=null;
            $contract->accidents_validity_to=null;
            $contract->control_3=null;
            $contract->control_c=null;
        }else{
            $contract->accidents_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('accidents_validity_from'))));
            $contract->accidents_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('accidents_validity_to'))));

            //control para grabar control_3 y control_c
            $control_3 = $request->input('control_3');
            $control_c = $request->input('control_c');
            if ($control_c < 0 ) {
                $validator->errors()->add('control_c', 'Número no puede ser negativo');
                return back()->withErrors($validator)->withInput();
            }else{
                if (empty($control_3) || empty($control_c) ) {
                    $validator->errors()->add('control_c', 'ANTICIPOS - Dias Vigencia o a Vencer no pueden estar sin datos');
                    return back()->withErrors($validator)->withInput();
                }else{
                    $contract->control_3=$request->input('control_3');
                    $contract->control_c=$request->input('control_c');
                }
            }
        }

        //RIESGO TOTAL
        $risksValidityFrom = $request->input('risks_validity_from');
        $risksValidityTo = $request->input('risks_validity_to');
        if (is_null($risksValidityFrom) && !empty($risksValidityTo)) {
            $validator->errors()->add('risks_validity_from', 'Por favor, seleccione una fecha para adavance_validity_from');
            return back()->withErrors($validator)->withInput();
        }
        if (!empty($risksValidityFrom) && is_null($risksValidityTo)) {
            $validator->errors()->add('risks_validity_to', 'Por favor, seleccione una fecha para adavance_validity_to');
            return back()->withErrors($validator)->withInput();
        }

        if (empty($risksValidityFrom) && is_null($risksValidityTo)) {
            $contract->risks_validity_from=null;
            $contract->risks_validity_to=null;
            $contract->control_4=null;
            $contract->control_d=null;
        }else{
            $contract->risks_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('risks_validity_from'))));
            $contract->risks_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('risks_validity_to'))));

            //control para grabar control_4 y control_d
            $control_4 = $request->input('control_4');
            $control_d = $request->input('control_d');
            if ($control_d < 0 ) {
                $validator->errors()->add('control_d', 'Número no puede ser negativo');
                return back()->withErrors($validator)->withInput();
            }else{
                if (empty($control_4) || empty($control_d) ) {
                    $validator->errors()->add('control_d', 'ANTICIPOS - Dias Vigencia o a Vencer no pueden estar sin datos');
                    return back()->withErrors($validator)->withInput();
                }else{
                    $contract->control_4=$request->input('control_4');
                    $contract->control_d=$request->input('control_d');
                }
            }
        }

        //RESPONSABILIDAD CIVIL
        $civil_respValidityFrom = $request->input('civil_resp_validity_from');
        $civil_respValidityTo = $request->input('civil_resp_validity_to');
        if (is_null($civil_respValidityFrom) && !empty($civil_respValidityTo)) {
            $validator->errors()->add('civil_resp_validity_from', 'Por favor, seleccione una fecha para adavance_validity_from');
            return back()->withErrors($validator)->withInput();
        }
        if (!empty($civil_respValidityFrom) && is_null($civil_respValidityTo)) {
            $validator->errors()->add('civil_resp_validity_to', 'Por favor, seleccione una fecha para adavance_validity_to');
            return back()->withErrors($validator)->withInput();
        }

        if (empty($civil_respValidityFrom) && is_null($civil_respValidityTo)) {
            $contract->civil_resp_validity_from=null;
            $contract->civil_resp_validity_to=null;
            $contract->control_5=null;
            $contract->control_e=null;
        }else{
            $contract->civil_resp_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('civil_resp_validity_from'))));
            $contract->civil_resp_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('civil_resp_validity_to'))));

            //control para grabar control_5 y control_e
            $control_5 = $request->input('control_5');
            $control_e = $request->input('control_e');
            if ($control_e < 0 ) {
                $validator->errors()->add('control_e', 'Número no puede ser negativo');
                return back()->withErrors($validator)->withInput();
            }else{
                if (empty($control_5) || empty($control_e) ) {
                    $validator->errors()->add('control_e', 'ANTICIPOS - Dias Vigencia o a Vencer no pueden estar sin datos');
                    return back()->withErrors($validator)->withInput();
                }else{
                    $contract->control_5=$request->input('control_5');
                    $contract->control_e=$request->input('control_e');
                }
            }
        }

        $contract->comments=$request->input('comments');
        $contract->creator_user_id = $request->user()->id;  // usuario logueado
        $contract->dependency_id = $request->user()->dependency_id;//dependencia del usuario
        $contract->save();
        return redirect()->route('contracts.index')->with('success', 'Llamado agregado correctamente');
    }

    /**
     * Visualización de un pedido
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $contract_id)
    {
        $contract = Contract::findOrFail($contract_id);
        $user_dependency = $request->user()->dependency_id;
        $role_user = $request->user()->role_id;
        // Obtenemos los simese cargados por otras dependencias
        // $related_simese = $contract->simese()->where('dependency_id', '!=', $user_dependency)->get();
        // Obtenemos los simese cargados por la dependencia del usuario
        // $related_simese_user = $contract->simese()->where('dependency_id', $user_dependency)->get();

        // Obtenemos los archivos cargados por otras dependencias y que no sean de reparo
        $other_files = $contract->files()->where('dependency_id', '!=', $user_dependency)
        // ->whereIn('file_type', [0,3, 4, 5, 7])//0-antecedentes 3-contratos 4-addendas  5-dictamenes
        ->orderBy('created_at','asc')
        ->get();

        // ROL ADMINSTRADOR Obtenemos los archivos cargados por otras dependencias
        if($role_user == 1){
            $other_files = $contract->files()->where('dependency_id', '!=', $user_dependency)
            // ->whereIn('file_type', [0,3, 4, 5, 7])//0-antecedentes 3-contratos 4-addendas  5-dictamenes
            ->orderBy('created_at','asc')
            ->get();
        }

        // Obtenemos los archivos cargados por usuario con tipo de archivos que no sean 1 (reparos dncp)z
        // $user_files = $contract->files()->where('dependency_id', $user_dependency)->where('file_type', '=', 0)->get();
        $user_files = $contract->files()->where('dependency_id', $user_dependency)->get();

        // chequeamos que el usuario tenga permisos para visualizar el pedido
        if($request->user()->hasPermission(['admin.contracts.show', 'process_contracts.contracts.show',
        'contracts.contracts.index','derive_contracts.contracts.index']) || $contract->dependency_id == $request->user()->dependency_id){
            return view('contract.contracts.show', compact('contract','other_files','user_files'));
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
    public function edit(Request $request, $contract_id)
    {
        $contract = Contract::findOrFail($contract_id);
        // chequeamos que el usuario tenga permisos para editar el llamado
        if($request->user()->hasPermission(['admin.contracts.update','contracts.contracts.update'])){
            $dependencies = Dependency::all();
            $modalities = Modality::all();
            $sub_programs = SubProgram::all();
            $funding_sources = FundingSource::all();
            $financial_organisms = FinancialOrganism::all();
            $expenditure_objects = ExpenditureObject::where('level', 3)->get();
            $providers = Provider::all();//se podria filtrar por estado sólo activo
            $contr_states = ContractState::all();
            $contract_types = ContractType::all();
            return view('contract.contracts.update', compact('contract','dependencies', 'modalities','sub_programs', 'funding_sources', 'financial_organisms',
                'expenditure_objects', 'providers', 'contr_states','contract_types'));
        }else{
            return back()->with('error', 'No tiene los suficientes permisos para acceder a esta sección.');
        }
    }

    /**
     * Funcionalidad de modificacion del pedido.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $contract_id)
    {
        $contract = Contract::findOrFail($contract_id);

        $rules = array(
            'description' => 'string|required|max:300',
            'iddncp' => 'string|required|max:999999|min:7',
            'linkdncp' => 'string|required|max:300',
            'number_year' => 'string|required|max:9',
            'year_adj' => 'numeric|required|max:9999',
            'sign_date' => 'date_format:d/m/Y|required',
            'provider_id' => 'numeric|required|max:999999',
            'contract_state_id' => 'numeric|required|max:999999',
            'modality_id' => 'numeric|required|max:999999',
            'financial_organism_id' => 'numeric|required|max:999999',
            'contract_type_id' => 'numeric|required|max:999999',
            'total_amount' => 'string|required|max:9223372036854775807',
            'advance_validity_from' => 'nullable|date_format:d/m/Y',
            'advance_validity_to' => 'nullable|date_format:d/m/Y',
            'fidelity_validity_from' => 'nullable|date_format:d/m/Y',
            'fidelity_validity_to' => 'nullable|date_format:d/m/Y',
            'accidents_validity_from' => 'nullable|date_format:d/m/Y',
            'accidents_validity_to' => 'nullable|date_format:d/m/Y',
            'risks_validity_from' => 'nullable|date_format:d/m/Y',
            'risks_validity_to' => 'nullable|date_format:d/m/Y',
            'civil_resp_validity_from' => 'nullable|date_format:d/m/Y',
            'civil_resp_validity_to' => 'nullable|date_format:d/m/Y',
            'comments' => 'nullable|max:300',
            'control_1' => 'nullable|numeric',
            'control_a' => 'nullable|numeric',
            'control_2' => 'nullable|numeric',
            'control_b' => 'nullable|numeric',
            'control_3' => 'nullable|numeric',
            'control_c' => 'nullable|numeric',
            'control_4' => 'nullable|numeric',
            'control_d' => 'nullable|numeric',
            'control_5' => 'nullable|numeric',
            'control_e' => 'nullable|numeric'
        );

        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $contract->description=$request->input('description');
        // $contract->iddncp=$request->input('iddncp');


        $iddncp_fin = str_replace('.', '',($request->input('iddncp')));

        if ($iddncp_fin > 999999) {
            $validator->errors()->add('iddncp', 'Número no debe sobrepasar 999.999');
            return back()->withErrors($validator)->withInput()->with('fila');
        }else{
            $contract->iddncp = $iddncp_fin;
        }
        // $contract->iddncp = $request->input('iddncp');

        $contract->linkdncp=$request->input('linkdncp');
        $contract->number_year=$request->input('number_year');

        $year_adj_fin = str_replace('.', '',($request->input('year_adj')));
        $contract->year_adj = $year_adj_fin;

        $contract->sign_date = date('Y-m-d', strtotime(str_replace("/", "-", $request->input('sign_date'))));
        $contract->provider_id=$request->input('provider_id');
        $contract->contract_state_id=$request->input('contract_state_id');
        $contract->modality_id=$request->input('modality_id');
        $contract->financial_organism_id=$request->input('financial_organism_id');
        $contract->contract_type_id=$request->input('contract_type_id');

        $total_amount_fin = str_replace('.', '',($request->input('total_amount')));
        if ($total_amount_fin <= 0 ) {
            $validator->errors()->add('total_amount', 'Monto no puede ser cero ni negativo');
            return back()->withErrors($validator)->withInput();
        }else{
            $contract->total_amount = $total_amount_fin;
        }

        //CONTROLAR QUE LAS FECHAS SI SON VACIAS GRABEN NULL

        //ANTICIPOS
        $advanceValidityFrom = $request->input('advance_validity_from');
        $advanceValidityTo = $request->input('advance_validity_to');
        if (is_null($advanceValidityFrom) && !empty($advanceValidityTo)) {
            $validator->errors()->add('advance_validity_from', 'Por favor, seleccione una fecha para adavance_validity_from');
            return back()->withErrors($validator)->withInput();
        }
        if (!empty($advanceValidityFrom) && is_null($advanceValidityTo)) {
            $validator->errors()->add('advance_validity_to', 'Por favor, seleccione una fecha para adavance_validity_to');
            return back()->withErrors($validator)->withInput();
        }

        if (empty($advanceValidityFrom) && is_null($advanceValidityTo)) {
            $contract->advance_validity_from=null;
            $contract->advance_validity_to=null;
            $contract->control_1=null;
            $contract->control_a=null;
        }else{
            $contract->advance_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('advance_validity_from'))));
            $contract->advance_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('advance_validity_to'))));

            //control para grabar control_1 y control_a
            $control_1 = $request->input('control_1');
            $control_a = $request->input('control_a');
            if ($control_a < 0 ) {
                $validator->errors()->add('control_a', 'Número no puede ser negativo');
                return back()->withErrors($validator)->withInput();
            }else{
                if (empty($control_1) || empty($control_a) ) {
                    $validator->errors()->add('control_a', 'ANTICIPOS - Dias Vigencia o a Vencer no pueden estar sin datos');
                    return back()->withErrors($validator)->withInput();
                }else{
                    $contract->control_1=$request->input('control_1');
                    $contract->control_a=$request->input('control_a');
                }
            }
        }

        //FIEL CUMPLIMIENTO
        $fidelityValidityFrom = $request->input('fidelity_validity_from');
        $fidelityValidityTo = $request->input('fidelity_validity_to');
        if (is_null($fidelityValidityFrom) && !empty($fidelityValidityTo)) {
            $validator->errors()->add('fidelity_validity_from', 'Por favor, seleccione una fecha para adavance_validity_from');
            return back()->withErrors($validator)->withInput();
        }
        if (!empty($fidelityValidityFrom) && is_null($fidelityValidityTo)) {
            $validator->errors()->add('fidelity_validity_to', 'Por favor, seleccione una fecha para adavance_validity_to');
            return back()->withErrors($validator)->withInput();
        }

        if (empty($fidelityValidityFrom) && is_null($fidelityValidityTo)) {
            $contract->fidelity_validity_from=null;
            $contract->fidelity_validity_to=null;
            $contract->control_2=null;
            $contract->control_b=null;
        }else{
            $contract->fidelity_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('fidelity_validity_from'))));
            $contract->fidelity_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('fidelity_validity_to'))));

            //control para grabar control_2 y control_b
            $control_2 = $request->input('control_2');
            $control_b = $request->input('control_b');
            if ($control_b < 0 ) {
                $validator->errors()->add('control_b', 'Número no puede ser negativo');
                return back()->withErrors($validator)->withInput();
            }else{
                if (empty($control_2) || empty($control_b) ) {
                    $validator->errors()->add('control_b', 'ANTICIPOS - Dias Vigencia o a Vencer no pueden estar sin datos');
                    return back()->withErrors($validator)->withInput();
                }else{
                    $contract->control_2=$request->input('control_2');
                    $contract->control_b=$request->input('control_b');
                }
            }
        }

        //ACCIDENTES
        $accidentsValidityFrom = $request->input('accidents_validity_from');
        $accidentsValidityTo = $request->input('accidents_validity_to');
        if (is_null($accidentsValidityFrom) && !empty($accidentsValidityTo)) {
            $validator->errors()->add('accidents_validity_from', 'Por favor, seleccione una fecha para adavance_validity_from');
            return back()->withErrors($validator)->withInput();
        }
        if (!empty($accidentsValidityFrom) && is_null($accidentsValidityTo)) {
            $validator->errors()->add('accidents_validity_to', 'Por favor, seleccione una fecha para adavance_validity_to');
            return back()->withErrors($validator)->withInput();
        }

        if (empty($accidentsValidityFrom) && is_null($accidentsValidityTo)) {
            $contract->accidents_validity_from=null;
            $contract->accidents_validity_to=null;
            $contract->control_3=null;
            $contract->control_c=null;
        }else{
            $contract->accidents_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('accidents_validity_from'))));
            $contract->accidents_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('accidents_validity_to'))));

            //control para grabar control_3 y control_c
            $control_3 = $request->input('control_3');
            $control_c = $request->input('control_c');
            if ($control_c < 0 ) {
                $validator->errors()->add('control_c', 'Número no puede ser negativo');
                return back()->withErrors($validator)->withInput();
            }else{
                if (empty($control_3) || empty($control_c) ) {
                    $validator->errors()->add('control_c', 'ANTICIPOS - Dias Vigencia o a Vencer no pueden estar sin datos');
                    return back()->withErrors($validator)->withInput();
                }else{
                    $contract->control_3=$request->input('control_3');
                    $contract->control_c=$request->input('control_c');
                }
            }
        }

        //RIESGO TOTAL
        $risksValidityFrom = $request->input('risks_validity_from');
        $risksValidityTo = $request->input('risks_validity_to');
        if (is_null($risksValidityFrom) && !empty($risksValidityTo)) {
            $validator->errors()->add('risks_validity_from', 'Por favor, seleccione una fecha para adavance_validity_from');
            return back()->withErrors($validator)->withInput();
        }
        if (!empty($risksValidityFrom) && is_null($risksValidityTo)) {
            $validator->errors()->add('risks_validity_to', 'Por favor, seleccione una fecha para adavance_validity_to');
            return back()->withErrors($validator)->withInput();
        }

        if (empty($risksValidityFrom) && is_null($risksValidityTo)) {
            $contract->risks_validity_from=null;
            $contract->risks_validity_to=null;
            $contract->control_4=null;
            $contract->control_d=null;
        }else{
            $contract->risks_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('risks_validity_from'))));
            $contract->risks_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('risks_validity_to'))));

            //control para grabar control_4 y control_d
            $control_4 = $request->input('control_4');
            $control_d = $request->input('control_d');
            if ($control_d < 0 ) {
                $validator->errors()->add('control_d', 'Número no puede ser negativo');
                return back()->withErrors($validator)->withInput();
            }else{
                if (empty($control_4) || empty($control_d) ) {
                    $validator->errors()->add('control_d', 'ANTICIPOS - Dias Vigencia o a Vencer no pueden estar sin datos');
                    return back()->withErrors($validator)->withInput();
                }else{
                    $contract->control_4=$request->input('control_4');
                    $contract->control_d=$request->input('control_d');
                }
            }
        }

        //RESPONSABILIDAD CIVIL
        $civil_respValidityFrom = $request->input('civil_resp_validity_from');
        $civil_respValidityTo = $request->input('civil_resp_validity_to');
        if (is_null($civil_respValidityFrom) && !empty($civil_respValidityTo)) {
            $validator->errors()->add('civil_resp_validity_from', 'Por favor, seleccione una fecha para adavance_validity_from');
            return back()->withErrors($validator)->withInput();
        }
        if (!empty($civil_respValidityFrom) && is_null($civil_respValidityTo)) {
            $validator->errors()->add('civil_resp_validity_to', 'Por favor, seleccione una fecha para adavance_validity_to');
            return back()->withErrors($validator)->withInput();
        }

        if (empty($civil_respValidityFrom) && is_null($civil_respValidityTo)) {
            $contract->civil_resp_validity_from=null;
            $contract->civil_resp_validity_to=null;
            $contract->control_5=null;
            $contract->control_e=null;
        }else{
            $contract->civil_resp_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('civil_resp_validity_from'))));
            $contract->civil_resp_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('civil_resp_validity_to'))));

            //control para grabar control_5 y control_e
            $control_5 = $request->input('control_5');
            $control_e = $request->input('control_e');
            if ($control_e < 0 ) {
                $validator->errors()->add('control_e', 'Número no puede ser negativo');
                return back()->withErrors($validator)->withInput();
            }else{
                if (empty($control_5) || empty($control_e) ) {
                    $validator->errors()->add('control_e', 'ANTICIPOS - Dias Vigencia o a Vencer no pueden estar sin datos');
                    return back()->withErrors($validator)->withInput();
                }else{
                    $contract->control_5=$request->input('control_5');
                    $contract->control_e=$request->input('control_e');
                }
            }
        }
        $contract->comments=$request->input('comments');
        $contract->creator_user_id = $request->user()->id;  // usuario logueado
        $contract->dependency_id = $request->user()->dependency_id;//dependencia del usuario
        $contract->save();
        return redirect()->route('contracts.show', $contract->id)->with('success', 'Pedido modificado correctamente');

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
        $orders = Contract::where('contract_state_id', '>=', 20)->where('actual_state', '<=', 130)
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


    public function excel(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="prueba.xlsx"');
        $writer->save('php://output');
    }

    /**
     * Derivar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function derive(Request $request, $contract_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['orders.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        //SE DERIVA A DGAF PARA REVISIÓN DE PEDIDO
        $contract = Order::find($contract_id);
        // Estado 4 = DERIVADO A DGAF PARA REVISIÓN DE PEDIDO
        $contract->actual_state = 4;
        $contract->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $contract_order_state = new OrderOrderState;
        $contract_order_state->order_id = $contract->id;
        $contract_order_state->order_state_id = 4;
        $contract_order_state->creator_user_id = $request->user()->id;
        $contract_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha derivado exitosamente el pedido. '], 200);
    }


    /**
     * Anular pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function anuleOrder(Request $request, $contract_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['orders.orders.anule'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $contract = Order::find($contract_id);
        // Verifica estado y si en Estado 2 = PROCESADO PEDIDO se puede cambiar
        $contract->actual_state = 0;
        $contract->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $contract_order_state = new OrderOrderState;
        $contract_order_state->order_id = $contract->id;
        $contract_order_state->order_state_id = 0;
        $contract_order_state->creator_user_id = $request->user()->id;
        $contract_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha anulado el pedido exitosamente. '], 200);
    }


    /**
     * Anular Derivar un pedido
     *
     * @return \Illuminate\Http\Response
     */
    public function anuleDerive(Request $request, $contract_id){
        // Chequeamos que el usuario disponga de permisos de derivacion de pedido
        if(!$request->user()->hasPermission(['orders.orders.derive'])){
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.'], 200);
        }

        $contract = Order::find($contract_id);
        // Verifica estado y si en Estado 2 = PROCESADO PEDIDO se puede cambiar
        $contract->actual_state = 1;
        $contract->save();

        // Registramos el movimiento de estado en la tabla orders_order_state
        $contract_order_state = new OrderOrderState;
        $contract_order_state->order_id = $contract->id;
        $contract_order_state->order_state_id = 1;
        $contract_order_state->creator_user_id = $request->user()->id;
        $contract_order_state->save();

        return response()->json(['status' => 'success', 'message' => 'Se ha anulado derivación exitosamente. '], 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $contract = Order::find($id);

        // chequeamos que el usuario tenga permisos para eliminar el pedido
        if( ($request->user()->hasPermission(['orders.orders.delete']) && $contract->dependency_id == $request->user()->dependency_id) ||
            $request->user()->hasPermission(['admin.orders.delete']) ){
            // Si el pedido se encuentra en ESTADO SOLICITUD se elimina directamente
            if($contract->actual_state == 1){
                foreach ($contract->items as $item) {
                    foreach ($item->itemAwardHistories as $row) {
                        // eliminamos precios referenciales relacionados a los items
                        $row->delete();
                    }
                }
                // eliminamos items relacionados
                foreach ($contract->items as $row) { $row->delete(); }
                // eliminamos solicitud de presupuestos relacionados
                foreach ($contract->budgetRequestProviders as $row) { $row->delete(); }
                // eliminamos plurianualidad relacionada
                foreach ($contract->orderMultiYears as $row) { $row->delete(); }
            }else{
                // Chequeamos si existen items referenciando al pedido
                if($contract->items->count() > 0){
                    return response()->json(['status' => 'error', 'message' => 'No se ha podido eliminar el pedido debido a que se encuentra vinculado con items ', 'code' => 200], 200);
                }
                // Chequeamos si existen budgetRequestProviders referenciando al pedido
                if($contract->budgetRequestProviders->count() > 0){
                    return response()->json(['status' => 'error', 'message' => 'No se ha podido eliminar el pedido debido a que se encuentra vinculado con presupuestos de empresas cargados ', 'code' => 200], 200);
                }
                // Chequeamos si existen orderMultiYears referenciando al pedido
                if($contract->orderMultiYears->count() > 0){
                    return response()->json(['status' => 'error', 'message' => 'No se ha podido eliminar el pedido debido a que se encuentra vinculado con datos guardados de plurianualidad ', 'code' => 200], 200);
                }
            }

            // Eliminamos en caso de no existir registros referenciando al pedido
            $contract->delete();
            $request->session()->flash('success', 'Se ha eliminado exitosamente el pedido.');
            return response()->json(['status' => 'success', 'code' => 200], 200);
        }else{
            return response()->json(['status' => 'error', 'message' => 'No posee los suficientes permisos para realizar esta acción.', 'code' => 200], 200);
        }
    }

    //Para mostrar un archivo EXCEL guardado en el Proyecto
    public function ArchivoPedido(){
        header("Content-type: application/xlsx");
        header("Content-Disposition: inline; filename=0-Modelo Pedido.xlsx");
        readfile("files/0-Modelo Pedido.xlsx");
    }

    //Para mostrar un archivo EXCEL guardado en el Proyecto tipo Contrato Abierto
    public function ArchivoItem(){
        header("Content-type: application/xlsx");
        header("Content-Disposition: inline; filename=1-Modelo Item ABIERTO.xlsx");
        readfile("files/1-Modelo Item ABIERTO.xlsx");
    }

    //Para mostrar un archivo EXCEL guardado en el Proyecto tipo Contrato Cerrado
    public function ArchivoItem2(){
        header("Content-type: application/xlsx");
        header("Content-Disposition: inline; filename=2-Modelo Item CERRADO.xlsx");
        readfile("files/2-Modelo Item CERRADO.xlsx");
    }

    //Para mostrar un archivo EXCEL guardado en el Proyecto tipo Contrato Cerrado
    public function ArchivoItem3(){
        header("Content-type: application/xlsx");
        header("Content-Disposition: inline; filename=3-Modelo Item 3-ABIERTO MMin-MMax.xlsx");
        readfile("files/3-Modelo Item ABIERTO MMin-MMax.xlsx");
    }

    //Para mostrar un archivo EXCEL guardado en el Proyecto items de Adjudicaciones
    public function ArchivoItemAw(){
        header("Content-type: application/xlsx");
        header("Content-Disposition: inline; filename=64-Importar_Items.xlsx");
        readfile("files/64-Importar_Items.xlsx");
    }
}
