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
            'description' => 'string|required|max:200',
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

        $contract = new Contract;

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
        // $contract->year_adj=$request->input('year_adj ' );
        $year_adj_fin = str_replace('.', '',($request->input('year_adj')));
        $contract->year_adj = $year_adj_fin;
        $contract->sign_date = date('Y-m-d', strtotime(str_replace("/", "-", $request->input('sign_date'))));
        $contract->provider_id=$request->input('provider_id');
        $contract->contract_state_id=$request->input('contract_state_id');
        $contract->modality_id=$request->input('modality_id');
        $contract->financial_organism_id=$request->input('financial_organism_id');
        $contract->contract_type_id=$request->input('contract_type_id');
        $total_amount_fin = str_replace('.', '',($request->input('total_amount')));
        $contract->total_amount = $total_amount_fin;

        //CONTROLAR QUE LAS FECHAS SI SON VACIAS NO GRABEN FECHAS ERRONEAS
        // if (is_null($funding_source)) {
        //     $validator->errors()->add('funding_source', 'No existe fuente financiera igual a la ingresada. Por favor ingrese una de las siguientes: 10,20,30.');
        //     return back()->withErrors($validator)->withInput()->with('fila', $row);
        // }
        $contract->advance_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('advance_validity_from'))));
        $contract->advance_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('advance_validity_to'))));;
        $contract->fidelity_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('fidelity_validity_from'))));
        $contract->fidelity_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('fidelity_validity_to'))));
        $contract->accidents_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('accidents_validity_from'))));
        $contract->accidents_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('accidents_validity_to'))));
        $contract->risks_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('risks_validity_from'))));
        $contract->risks_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('risks_validity_to'))));
        $contract->civil_resp_validity_from=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('civil_resp_validity_from'))));
        $contract->civil_resp_validity_to=date('Y-m-d', strtotime(str_replace("/", "-", $request->input('civil_resp_validity_to'))));
        
        $contract->comments=$request->input('control_1');
        $contract->comments=$request->input('control_a');
        $contract->comments=$request->input('control_2');
        $contract->comments=$request->input('control_b');
        $contract->comments=$request->input('control_3');
        $contract->comments=$request->input('control_c');
        $contract->comments=$request->input('control_4');
        $contract->comments=$request->input('control_d');
        $contract->comments=$request->input('control_5');
        $contract->comments=$request->input('control_e');
        $contract->comments=$request->input('comments');
        $contract->creator_user_id = $request->user()->id;  // usuario logueado
        $contract->dependency_id = $request->user()->dependency_id;//dependencia del usuario


        $contract->save();
        return redirect()->route('contracts.index')->with('success', 'Llamado agregado correctamente');

         // Si plurianualidad es 1 chequeamos que la suma no sobrepase el monto total
        // if($request->input('plurianualidad') == 1){
        //     $years = $request->input('multi_year_year');
        //     $amounts = $request->input('multi_year_amount');
        //     $total_amount = 0;
        //     for ($i=0; $i < count($years); $i++) {
        //         if(is_numeric($amounts[$i])){
        //             $total_amount += $amounts[$i];
        //         }
        //     }
        //     if($total_amount != $request->input('total_amount')){
        //         $validator->errors()->add('multi_year', 'La suma de los montos plurianuales no coincide con el monto total.');
        //         return back()->withErrors($validator)->withInput();
        //     }
        // }

        // Si OG1 = 0 emite alerta
        // if($request->input('expenditure_object_id') == 0){
        //     $validator->errors()->add('expenditure_object_id', 'El OG no puede ser 0');
        //     return back()->withErrors($validator)->withInput();
        // }

        // Si monto OG1 = 0 emite alerta
        // if($request->input('amount1') == 0){
        //     $validator->errors()->add('amount1', 'El monto de OG1 no puede ser 0');
        //     return back()->withErrors($validator)->withInput();
        // }


        //creamos variables de montos de OG y sumamos para ver el total de la sumatoria
        // $tot_am_exp1 = str_replace('.', '',($request->input('amount1')));
        // $tot_am_exp2 = str_replace('.', '',($request->input('amount2')));
        // $tot_am_exp3 = str_replace('.', '',($request->input('amount3')));
        // $tot_am_exp4 = str_replace('.', '',($request->input('amount4')));
        // $tot_am_exp5 = str_replace('.', '',($request->input('amount5')));
        // $tot_am_exp6 = str_replace('.', '',($request->input('amount6')));
        // $totales_am_exp = ($tot_am_exp1 + $tot_am_exp2 + $tot_am_exp3 + $tot_am_exp4 + $tot_am_exp5 + $tot_am_exp6);

        // // // //creamos variable para capturar el monto total del pedio sacandole el separador de miles
        // $total_amount_fin = str_replace('.', '',($request->input('total_amount')));

        // // // //validamos si el monto total de los OG no supera el monto total del pedido
        // if($totales_am_exp <> $total_amount_fin){
        //     $validator->errors()->add('total_amount', 'El monto TOTAL de los OG NO COINCIDE con el TOTAL del PEDIDO, verifique los montos de los OG');
        //     return back()->withErrors($validator)->withInput();
        // }
    }

    /**
     * Formulario de agregacion de pedido cargando archivo excel.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeExcel(Request $request)
    {
        if($request->hasFile('excel')){
            // chequeamos la extension del archivo subido
            if($request->file('excel')->getClientOriginalExtension() != 'xls' && $request->file('excel')->getClientOriginalExtension() != 'xlsx'){
                $validator = Validator::make($request->input(), []); // Creamos un objeto validator
                $validator->errors()->add('excel', 'El archivo introducido debe ser un excel de tipo: xls o xlsx'); // Agregamos el error
                return back()->withErrors($validator)->withInput();
            }

            // creamos un array de indices de la cabecera (1ra columna)

            // CODIGO ORIGINAL
            $header = array('responsible', 'year', 'modality', 'covid','program_type', 'program',
            'sub_program', 'funding_source','financial_organism','description',

            'expenditure_object','amount1',
            'expenditure_object2','amount2',
            'expenditure_object3','amount3',
            'expenditure_object4','amount4',
            'expenditure_object5','amount5',
            'expenditure_object6','amount6',
            'total_amount',

            'ad_referendum',
            'plurianualidad','system_awarded_by','fonacide','catalogs_technical_annexes',
            'alternative_offers', 'open_contract', 'period_time','manufacturer_authorization',
            'technical_specifications', 'samples','delivery_plan','contract_administrator',
            'contract_validity','price_sheet',

            'dncp_pac_id',
            'begin_date',
            'financial_advance_percentage_amount',
            'evaluation_committee_proposal',
            'payment_conditions',
            'contract_guarantee',
            'product_guarantee',
            'additional_technical_documents',
            'additional_qualified_documents',
            'property_title',
            'magnetic_medium',
            'referring_person_data',
            'form4_city', 'form4_date', 'dncp_resolution_number', 'dncp_resolution_date');

            // $header = array('responsible', 'modality', 'program_type', 'program', 'sub_program', 'funding_source',
            // 'financial_organism','expenditure_object','amount1',
            // 'expenditure_object2','amount2','total_amount','description',
            // 'ad_referendum', 'plurianualidad','system_awarded_by',
            // 'fonacide', 'catalogs_technical_annexes', 'alternative_offers', 'open_contract', 'period_time',
            // 'manufacturer_authorization','technical_specifications', 'samples',
            // 'delivery_plan', 'contract_administrator','contract_validity', 'price_sheet');

            // accedemos al archivo excel cargado
            $reader = IOFactory::createReader(ucfirst($request->file('excel')->getClientOriginalExtension())); // pasamos la extension xls o xlsx
            $reader->setReadDataOnly(true);
            $reader->setReadEmptyCells(false);
            $spreadsheet = $reader->load($request->excel->path());  // cargamos el archivo
            // variable que guarda la plantilla activa
            $worksheet = $spreadsheet->getActiveSheet();

            $rows = $worksheet->getHighestRow();    // cantidad de filas
            $columns = count($header);  // cantidad de columnas que debe tener el archivo
            $last_column = Coordinate::stringFromColumnIndex($columns);

            // Recorremos cada fila del archivo excel
            for ($row = 2; $row <= $rows; ++$row) {
                $data = $spreadsheet->getActiveSheet()->rangeToArray(
                    'A'.$row.':'.$last_column.$row, //Ej: A2:L2 The worksheet range that we want to retrieve
                    NULL,        // Value that should be returned for empty cells
                    TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                    TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                    TRUE         // Should the array be indexed by cell row and cell column
                );

                // Manejando BUG de la librería phpspreadsheet para archivos con formato xlsx
                if(empty(trim(implode("", $data[$row])))){
                    continue;
                }

                // creamos un array con indices igual al array de columnas y valores igual a los obtenidos en el archivo excel
                $contract = array_combine($header, $data[$row]);

                // creamos las reglas de validacion
                $rules = array(
                    'responsible' => 'string|required|max:100',
                    'year' => 'numeric|required|max:9999|min:1',
                    'modality' => 'string|required|max:10',
                    'covid' => 'boolean|required|min:0|max:1',
                    'program_type' => 'numeric|required|max:999',
                    'program' => 'numeric|required|max:999',
                    'sub_program' => 'numeric|required|max:999',
                    'funding_source' => 'numeric|required|max:32767',
                    'financial_organism' => 'numeric|required|max:32767',
                    'description' => 'string|required|max:200',
                    'expenditure_object' => 'numeric|required|max:32767',
                    'amount1' => 'numeric|required|max:9223372036854775807',
                    // 'expenditure_object2' => 'numeric|nullable|max:32767',
                    'expenditure_object2' => 'numeric|max:32767',
                    'amount2' => 'numeric|nullable|max:9223372036854775807',
                    // 'expenditure_object3' => 'numeric|nullable|max:32767',
                    'expenditure_object3' => 'numeric|max:32767',
                    'amount3' => 'numeric|nullable|max:9223372036854775807',
                    // 'expenditure_object4' => 'numeric|nullable|max:32767',
                    'expenditure_object4' => 'numeric|max:32767',
                    'amount4' => 'numeric|nullable|max:9223372036854775807',
                    // 'expenditure_object5' => 'numeric|nullable|max:32767',
                    'expenditure_object5' => 'numeric|max:32767',
                    'amount5' => 'numeric|nullable|max:9223372036854775807',
                    // 'expenditure_object6' => 'numeric|nullable|max:32767',
                    'expenditure_object6' => 'numeric|max:32767',
                    'amount6' => 'numeric|nullable|max:9223372036854775807',

                    //total amount se valida más abajo
                    'total_amount' => 'numeric|required|max:9223372036854775807',

                    'ad_referendum' => 'boolean|required|min:0|max:1',
                    'plurianualidad' => 'boolean|required|min:0|max:1',
                    'system_awarded_by' => 'string|required|in:LOTE,ÍTEM,TOTAL,COMBINADO',
                    // 'expenditure_object' => 'numeric|required|max:32767',
                    'fonacide' => 'boolean|required|min:0|max:1',
                    'catalogs_technical_annexes' => 'boolean|required|min:0|max:1',
                    'alternative_offers' => 'boolean|required|min:0|max:1',
                    'open_contract' => 'numeric|required|min:1|max:3',
                    'period_time' => 'string|required|max:50',
                    'manufacturer_authorization' => 'boolean|required|min:0|max:1',
                    'technical_specifications' => 'string|required|max:100',
                    'samples' => 'boolean|required|min:0|max:1',
                    'delivery_plan' => 'string|nullable|max:150',
                    'contract_administrator' => 'string|required|max:150',
                    'contract_validity' => 'string|required|max:200',
                    'price_sheet' => 'string|nullable|max:150',

                    'dncp_pac_id' => 'numeric|nullable|max:2147483647',
                    'begin_date' => 'date_format:d/m/Y|nullable',
                    'financial_advance_percentage_amount' => 'boolean|nullable',
                    'evaluation_committee_proposal' => 'string|nullable|max:200',
                    'payment_conditions' => 'string|nullable',
                    'contract_guarantee' => 'string|nullable',
                    'product_guarantee' => 'string|nullable|max:200',
                    'additional_technical_documents' => 'string|nullable|max:200',
                    'additional_qualified_documents' => 'string|nullable|max:200',
                    'property_title' => 'string|nullable|max:200',
                    'magnetic_medium' => 'string|nullable|max:50',
                    'referring_person_data' => 'string|nullable|max:100',

                    'form4_city' => 'string|max:100|nullable',
                    'form4_date' => 'date_format:d/m/Y|nullable',
                    'dncp_resolution_number' => 'string|max:8|nullable',
                    'dncp_resolution_date' => 'date_format:d/m/Y|nullable',
                );

                // var_dump($monto_contract,$monto_adjudicar);
                //var_dump($contract['total_amount']);exit();
                // var_dump($contract['total_amount']);exit();

                // validamos los datos
                $validator = Validator::make($contract, $rules); // Creamos un objeto validator
                if ($validator->fails()) {
                    return back()->withErrors($validator)->withInput()->with('fila', $row);
                }

                $modality = Modality::where('code', $contract['modality'])->get()->first();
                if (is_null($modality)) {
                    $validator->errors()->add('modality', 'No existe modalidad igual a la ingresada. Por favor ingrese una de las modalidades registradas en el sistema.');
                    return back()->withErrors($validator)->withInput()->with('fila', $row);
                }
                $sub_program = SubProgram::where('activity_code', $contract['sub_program'])->get()->first();
                if (is_null($sub_program)) {
                    $validator->errors()->add('sub_program', 'No existe código de sub_programa igual al ingresado. Por favor ingrese uno de los sub_programas registrados en el sistema.');
                    return back()->withErrors($validator)->withInput()->with('fila', $row);
                }
                $input_program = intval($contract['program']);
                if($sub_program->program->code != $input_program){
                    $validator->errors()->add('program', 'No existe código de programa igual al ingresado. Por favor ingrese uno de los programas registrados en el sistema.');
                    return back()->withErrors($validator)->withInput()->with('fila', $row);
                }
                $input_program_type = intval($contract['program_type']);
                if($sub_program->program->programType->code != $input_program_type){
                    $validator->errors()->add('program_type', 'No existe código de tipo de programa igual al ingresado. Por favor ingrese uno de los tipos de programa registrados en el sistema.');
                    return back()->withErrors($validator)->withInput()->with('fila', $row);
                }
                $funding_source = FundingSource::where('code', $contract['funding_source'])->get()->first();
                if (is_null($funding_source)) {
                    $validator->errors()->add('funding_source', 'No existe fuente financiera igual a la ingresada. Por favor ingrese una de las siguientes: 10,20,30.');
                    return back()->withErrors($validator)->withInput()->with('fila', $row);
                }
                $financial_organism = FinancialOrganism::where('code', $contract['financial_organism'])->get()->first();
                if (is_null($financial_organism)) {
                    $validator->errors()->add('financial_organism', 'No existe un organismo financiero igual al ingresado. Por favor ingrese el código de los organismos financieros registrados.');
                    return back()->withErrors($validator)->withInput()->with('fila', $row);
                }
                $expenditure_object = ExpenditureObject::where('code', $contract['expenditure_object'])->where('level', 3)->get()->first();
                if (is_null($expenditure_object)) {
                    $validator->errors()->add('expenditure_object', 'No existe objeto de gasto igual al ingresado. Por favor ingrese uno de los objetos de gasto registrados en el sistema.');
                    return back()->withErrors($validator)->withInput()->with('fila', $row);
                }

                //CONTROLAMOS QUE MONTO TOTAL SEA MAYOR A CERO
                $tot_amount = intval($contract['total_amount']);
                // print_r($tot_amount);exit;

                if($tot_amount <= 0){
                    $validator->errors()->add('estado', 'Monto total debe ser mayor a 0, Verifique su planilla');
                    return back()->withErrors($validator)->withInput();
                }
                //******************************************************


                $OG1 = $contract['expenditure_object'];
                if ($OG1 == 0) {
                    $validator->errors()->add('expenditure_object', 'Debe ser asignado un Objeto de gasto en OG#1. Por favor ingrese uno de los objetos de gasto registrados en el sistema.');
                    return back()->withErrors($validator)->withInput()->with('fila', $row);
                }

                //VALIDACIONES DE OBJETOS DE GASTOS

                //Controlamos OG1 - Si OG1 es 0 no permite carga debe tener si o si un OG
                $OG1 = $contract['expenditure_object'];
                if ($OG1 == 0) {
                    $validator->errors()->add('expenditure_object', 'Debe ser asignado un Objeto de gasto en OG#1. Por favor ingrese uno de los objetos de gasto registrados en el sistema.');
                    return back()->withErrors($validator)->withInput()->with('fila', $row);
                }

                //Si OG1 tiene OG y monto es 0 no permite carga debe tener si o si un monto
                $amount_OG1 = $contract['amount1'];
                if ($OG1 > 0) {
                    if (is_null($amount_OG1)) {
                        $validator->errors()->add('expenditure_object', 'Monto del objeto de gasto OG1 no pude estar vacío. Por favor ingrese un valor');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG1 ==0) {
                        $validator->errors()->add('expenditure_object', 'Monto del objeto de gasto OG1 debe ser mayor a 0. Por favor ingrese un valor');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                }

                //Controlamos OG2
                $OG2 = $contract['expenditure_object2'];
                $expenditure_object2 = ExpenditureObject::where('code', $contract['expenditure_object2'])->where('level', 3)->get()->first();
                //Si OG2 está vacio no valida nada
                if (is_null($OG2)) {

                }else{
                    if (is_null($expenditure_object2)) {
                        $validator->errors()->add('expenditure_object2', 'No existe objeto de gasto OG2 igual al ingresado. Por favor ingrese uno de los objetos de gasto registrados en el sistema.');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                //Si OG2 tiene OG y monto es 0 no permite carga debe tener si o si un monto
                $amount_OG2 = $contract['amount2'];
                if ($OG2 > 0) {
                    if (is_null($amount_OG2) || ($amount_OG2 == 0 )) {
                        $validator->errors()->add('expenditure_object', 'Monto del objeto de gasto OG2 no pude estar VACIO ni ser CERO. Por favor ingrese un valor');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG2 < 0) {
                        $validator->errors()->add('expenditure_object', 'MONTO OG2 debe ser mayor a 0');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                if ($OG2 == 0) {
                    if ($amount_OG2 > 0) {
                        $validator->errors()->add('expenditure_object', 'Objeto de gasto OG2 no pude estar VACIO si existe MONTO asignado. Por favor ingrese un OG');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG2 < 0) {
                        $validator->errors()->add('expenditure_object', 'MONTO de OG2 debe ser mayor a 0');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }


                //Controlamos OG3
                $OG3 = $contract['expenditure_object3'];
                $expenditure_object3 = ExpenditureObject::where('code', $contract['expenditure_object3'])->where('level', 3)->get()->first();
                //Si OG3 está vacio no valida nada
                if (is_null($OG3)) {

                }else{
                    if (is_null($expenditure_object3)) {
                        $validator->errors()->add('expenditure_object3', 'No existe objeto de gasto OG3 igual al ingresado. Por favor ingrese uno de los objetos de gasto registrados en el sistema.');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                //Si OG3 tiene OG y monto es 0 no permite carga debe tener si o si un monto
                $amount_OG3 = $contract['amount3'];
                if ($OG3 > 0) {
                    if (is_null($amount_OG3) || ($amount_OG3 == 0 )) {
                        $validator->errors()->add('expenditure_object', 'Monto del objeto de gasto OG3 no pude estar VACIO ni ser CERO. Por favor ingrese un valor');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG3 < 0) {
                        $validator->errors()->add('expenditure_object', 'MONTO OG3 debe ser mayor a 0');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                if ($OG3 == 0) {
                    if ($amount_OG3 > 0) {
                        $validator->errors()->add('expenditure_object', 'Objeto de gasto OG3 no pude estar VACIO si existe MONTO asignado. Por favor ingrese un OG');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG3 < 0) {
                        $validator->errors()->add('expenditure_object', 'MONTO de OG3 debe ser mayor a 0');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                //Controlamos OG4
                $OG4 = $contract['expenditure_object4'];
                $expenditure_object4 = ExpenditureObject::where('code', $contract['expenditure_object4'])->where('level', 3)->get()->first();
                //Si OG4 está vacio no valida nada
                if (is_null($OG4)) {

                }else{
                    if (is_null($expenditure_object4)) {
                        $validator->errors()->add('expenditure_object4', 'No existe objeto de gasto OG4 igual al ingresado. Por favor ingrese uno de los objetos de gasto registrados en el sistema.');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                //Si OG4 tiene OG y monto es 0 no permite carga debe tener si o si un monto
                $amount_OG4 = $contract['amount4'];
                if ($OG4 > 0) {
                    if (is_null($amount_OG4) || ($amount_OG4 == 0 )) {
                        $validator->errors()->add('expenditure_object', 'Monto del objeto de gasto OG4 no pude estar VACIO ni ser CERO. Por favor ingrese un valor');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG4 < 0) {
                        $validator->errors()->add('expenditure_object', 'MONTO OG4 debe ser mayor a 0');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                if ($OG4 == 0) {
                    if ($amount_OG4 > 0) {
                        $validator->errors()->add('expenditure_object', 'Objeto de gasto OG4 no pude estar VACIO si existe MONTO asignado. Por favor ingrese un OG');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG4 < 0) {
                        $validator->errors()->add('expenditure_object', 'MONTO de OG4 debe ser mayor a 0');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                //Controlamos OG5
                $OG5 = $contract['expenditure_object5'];
                $expenditure_object5 = ExpenditureObject::where('code', $contract['expenditure_object5'])->where('level', 3)->get()->first();
                //Si OG5 está vacio no valida nada
                if (is_null($OG5)) {

                }else{
                    if (is_null($expenditure_object5)) {
                        $validator->errors()->add('expenditure_object5', 'No existe objeto de gasto OG5 igual al ingresado. Por favor ingrese uno de los objetos de gasto registrados en el sistema.');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                //Si OG5 tiene OG y monto es 0 no permite carga debe tener si o si un monto
                $amount_OG5 = $contract['amount5'];
                if ($OG5 > 0) {
                    if (is_null($amount_OG5) || ($amount_OG5 == 0 )) {
                        $validator->errors()->add('expenditure_object', 'Monto del objeto de gasto OG5 no pude estar VACIO ni ser CERO. Por favor ingrese un valor');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG5 < 0) {
                        $validator->errors()->add('expenditure_object', 'MONTO OG5 debe ser mayor a 0');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                if ($OG5 == 0) {
                    if ($amount_OG5 > 0) {
                        $validator->errors()->add('expenditure_object', 'Objeto de gasto OG5 no pude estar VACIO si existe MONTO asignado. Por favor ingrese un OG');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG5 < 0) {
                        $validator->errors()->add('expenditure_object', 'MONTO OG5 debe ser mayor a 0');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                //Controlamos OG6
                $OG6 = $contract['expenditure_object6'];
                $expenditure_object6 = ExpenditureObject::where('code', $contract['expenditure_object6'])->where('level', 3)->get()->first();
                //Si OG6 está vacio no valida nada
                if (is_null($OG6)) {

                }else{
                    if (is_null($expenditure_object6)) {
                        $validator->errors()->add('expenditure_object6', 'No existe objeto de gasto OG6 igual al ingresado. Por favor ingrese uno de los objetos de gasto registrados en el sistema.');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                //Si OG6 tiene OG y monto es 0 no permite carga debe tener si o si un monto
                $amount_OG6 = $contract['amount6'];
                if ($OG6 > 0) {
                    if (is_null($amount_OG6) || ($amount_OG6 == 0 )) {
                        $validator->errors()->add('expenditure_object', 'Monto del objeto de gasto OG6 no pude estar VACIO ni ser CERO. Por favor ingrese un valor');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG6 < 0) {
                        $validator->errors()->add('expenditure_object', 'MONTO OG6 debe ser mayor a 0');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }

                if ($OG6 == 0) {
                    if ($amount_OG6 > 0) {
                        $validator->errors()->add('expenditure_object', 'Objeto de gasto OG6 no pude estar VACIO si existe MONTO asignado. Por favor ingrese un OG');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }

                    if ($amount_OG6 < 0) {
                        $validator->errors()->add('expenditure_object', 'MONTO OG6 debe ser mayor a 0');
                        return back()->withErrors($validator)->withInput()->with('fila', $row);
                    }
                }



                $contract['modality_id'] = $modality->id;
                $contract['sub_program_id'] = $sub_program->id;
                $contract['funding_source_id'] = $funding_source->id;
                $contract['financial_organism_id'] = $financial_organism->id;
                $contract['expenditure_object_id'] = $expenditure_object->id;

                //Si OG3 está vacio prepara null para la carga en el array
                if (is_null($OG2)) {
                    $contract['expenditure_object2_id'] = null;
                }else{
                    $contract['expenditure_object2_id'] = $expenditure_object2->id;
                }

                //Si OG3 está vacio prepara null para la carga en el array
                if (is_null($OG3)) {
                    $contract['expenditure_object3_id'] = null;
                }else{
                    $contract['expenditure_object3_id'] = $expenditure_object3->id;
                }

                //Si OG4 está vacio prepara null para la carga en el array
                if (is_null($OG4)) {
                    $contract['expenditure_object4_id'] = null;
                }else{
                    $contract['expenditure_object4_id'] = $expenditure_object4->id;
                }

                //Si OG5 está vacio prepara null para la carga en el array
                if (is_null($OG5)) {
                    $contract['expenditure_object5_id'] = null;
                }else{
                    $contract['expenditure_object5_id'] = $expenditure_object5->id;
                }

                //Si OG6 está vacio prepara null para la carga en el array
                if (is_null($OG6)) {
                    $contract['expenditure_object6_id'] = null;
                }else{
                    $contract['expenditure_object6_id'] = $expenditure_object6->id;
                }

                // agregamos la fila al array de pedidos
                $contracts[] = $contract;
            }


            // En caso de haber pasado todas las validaciones guardamos los datos
            foreach ($contracts as $contract) {
                $new_order = new Order;
                $new_order->dependency_id = $request->user()->dependency_id;
                $new_order->responsible = $contract['responsible'];
                $new_order->year = $contract['year'];
                $new_order->modality_id = $contract['modality_id'];
                $new_order->covid = $contract['covid'];
                $new_order->dncp_pac_id = $contract['dncp_pac_id'];
                $new_order->begin_date = empty($contract['begin_date']) ? NULL : date('Y-m-d', strtotime(str_replace("/", "-", $contract['begin_date'])));
                $new_order->sub_program_id = $contract['sub_program_id'];
                $new_order->funding_source_id = $contract['funding_source_id'];
                $new_order->financial_organism_id = $contract['financial_organism_id'];

                $new_order->expenditure_object_id = $contract['expenditure_object_id'];
                $new_order->amount1 = $contract['amount1'];
                $new_order->expenditure_object2_id = $contract['expenditure_object2_id'];
                $new_order->amount2 = $contract['amount2'];

                $new_order->expenditure_object3_id = $contract['expenditure_object3_id'];
                $new_order->amount3 = $contract['amount3'];

                $new_order->expenditure_object4_id = $contract['expenditure_object4_id'];
                $new_order->amount4 = $contract['amount4'];
                $new_order->expenditure_object5_id = $contract['expenditure_object5_id'];
                $new_order->amount5 = $contract['amount5'];
                $new_order->expenditure_object6_id = $contract['expenditure_object6_id'];
                $new_order->amount6 = $contract['amount6'];
                $new_order->total_amount = $contract['total_amount'];

                $new_order->description = $contract['description'];
                $new_order->ad_referendum = $contract['ad_referendum'];
                $new_order->plurianualidad = $contract['plurianualidad'];
                $new_order->system_awarded_by = $contract['system_awarded_by'];
                // $new_order->expenditure_object_id = $contract['expenditure_object_id'];
                $new_order->fonacide = $contract['fonacide'];
                $new_order->catalogs_technical_annexes = $contract['catalogs_technical_annexes'];
                $new_order->alternative_offers = $contract['alternative_offers'];
                $new_order->open_contract = $contract['open_contract'];
                $new_order->period_time = $contract['period_time'];
                $new_order->manufacturer_authorization = $contract['manufacturer_authorization'];
                $new_order->financial_advance_percentage_amount = $contract['financial_advance_percentage_amount'];
                $new_order->technical_specifications = $contract['technical_specifications'];
                $new_order->samples = $contract['samples'];
                $new_order->delivery_plan = $contract['delivery_plan'];
                $new_order->evaluation_committee_proposal = $contract['evaluation_committee_proposal'];
                $new_order->payment_conditions = $contract['payment_conditions'];
                $new_order->contract_guarantee = $contract['contract_guarantee'];
                $new_order->product_guarantee = $contract['product_guarantee'];
                $new_order->contract_administrator = $contract['contract_administrator'];
                $new_order->contract_validity = $contract['contract_validity'];
                $new_order->additional_technical_documents = $contract['additional_technical_documents'];
                $new_order->additional_qualified_documents = $contract['additional_qualified_documents'];
                $new_order->price_sheet = $contract['price_sheet'];
                $new_order->property_title = $contract['property_title'];
                $new_order->magnetic_medium = $contract['magnetic_medium'];
                $new_order->referring_person_data = $contract['referring_person_data'];
                $new_order->form4_city = $contract['form4_city'];
                // $new_order->form4_date = date('Y-m-d', strtotime(str_replace("/", "-", $contract['form4_date'])));
                $new_order->form4_date = empty($contract['form4_date']) ? NULL : date('Y-m-d', strtotime(str_replace("/", "-", $contract['form4_date'])));
                $new_order->dncp_resolution_number = $contract['dncp_resolution_number'];
                // $new_order->dncp_resolution_date = date('Y-m-d', strtotime(str_replace("/", "-", $contract['dncp_resolution_date'])));
                $new_order->dncp_resolution_date = empty($contract['dncp_resolution_date']) ? NULL : date('Y-m-d', strtotime(str_replace("/", "-", $contract['dncp_resolution_date'])));

                $new_order->creator_user_id = $request->user()->id;  // usuario logueado
                $new_order->actual_state = 1; // ESTADO igual a SOLICITUD
                $new_order->save();

                //DESCOMENTAR SIN FALTA OJO
                // Registramos el movimiento de estado en la tabla orders_order_state
                $contract_order_state = new OrderOrderState;
                $contract_order_state->order_id = $new_order->id;
                $contract_order_state->order_state_id = 1;
                $contract_order_state->creator_user_id = $request->user()->id;
                $contract_order_state->save();
            }

            return redirect()->route('orders.index')->with('success', 'Pedido importado correctamente');

        }else{
            $validator = Validator::make($request->input(), []);
            $validator->errors()->add('excel', 'El campo es requerido, debe ingresar un archivo excel.');
            return back()->withErrors($validator)->withInput();
        }
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
        $contract = Order::findOrFail($contract_id);

        $rules = array(
            'responsible' => 'string|required|max:100',
            'year' => 'numeric|required|max:9999',
            'modality' => 'numeric|required|max:32767',
            // 'dncp_pac_id' => 'numeric|max:2147483647',
            'begin_date' => 'date_format:d/m/Y|nullable',
            'sub_program' => 'numeric|required|max:32767',
            'funding_source' => 'numeric|required|max:32767',
            'financial_organism' => 'numeric|required|max:32767',

            // 'total_amount' => 'numeric|required|max:9223372036854775807',

            'description' => 'string|required|max:200',
            'ad_referendum' => 'boolean|required',
            'plurianualidad' => 'boolean|required',
            'multi_year_year' => 'array|nullable',
            'multi_year_amount' => 'array|nullable',
            'system_awarded_by' => 'string|required|max:25',
            'fonacide' => 'boolean|required',
            'catalogs_technical_annexes' => 'boolean|required',
            'alternative_offers' => 'boolean|required',
            'open_contract' => 'numeric|required',
            'period_time' => 'string|required|max:50',
            'manufacturer_authorization' => 'boolean|required',
            'financial_advance_percentage_amount' => 'boolean|required',
            'technical_specifications' => 'string|required|max:100',
            'samples' => 'boolean|required',
            'delivery_plan' => 'string|nullable|max:150',
            'evaluation_committee_proposal' => 'string|nullable|max:200',
            'payment_conditions' => 'string|nullable',
            'contract_guarantee' => 'string|nullable',
            'product_guarantee' => 'string|nullable|max:200',
            'contract_administrator' => 'string|required|max:150',
            'contract_validity' => 'string|required|max:200',
            'additional_technical_documents' => 'string|nullable|max:200',
            'additional_qualified_documents' => 'string|nullable|max:200',
            'price_sheet' => 'string|nullable|max:150',
            'property_title' => 'string|nullable|max:200',
            'magnetic_medium' => 'string|nullable|max:50',
            'referring_person_data' => 'string|nullable|max:100',

            'form4_city' => 'string|max:100|nullable',
            'form4_date' => 'date_format:d/m/Y|nullable',
            'dncp_resolution_number' => 'string|max:8|nullable',
            'dncp_resolution_date' => 'date_format:d/m/Y|nullable',

            'total_amount' => 'string|required|max:9223372036854775807',

            'expenditure_object_id' => 'numeric|required|max:32767',
            'amount1' => 'string|required|max:9223372036854775807',

            'expenditure_object2_id' => 'numeric|nullable|max:32767',
            'amount2' => 'string|nullable|max:9223372036854775807',

            'expenditure_object3' => 'numeric|nullable|max:32767',
            'amount3' => 'string|nullable|max:9223372036854775807',

            'expenditure_object4' => 'numeric|nullable|max:32767',
            'amount4' => 'string|nullable|max:9223372036854775807',

            'expenditure_object5' => 'numeric|nullable|max:32767',
            'amount5' => 'string|nullable|max:9223372036854775807',

            'expenditure_object6' => 'numeric|nullable|max:32767',
            'amount6' => 'string|nullable|max:9223372036854775807',

            'total_amount' => 'string|required|max:9223372036854775807',

            'covid' => 'boolean|required',
        );

        $validator =  Validator::make($request->input(), $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Si plurianualidad es 1 chequeamos que la suma no sobrepase el monto total
        if($request->input('plurianualidad') == 1){
            $years = $request->input('multi_year_year');
            $amounts = $request->input('multi_year_amount');
            $total_amount_pluri = 0;
            for ($i=0; $i < count($years); $i++) {
                if(is_numeric($amounts[$i])){
                    $total_amount_pluri += $amounts[$i];
                }
            }

        //creamos variable para capturar el monto total del pedido sacandole el separador de miles
        $total_amount = str_replace('.', '',($request->input('total_amount')));

            if($total_amount_pluri != $total_amount){
                $validator->errors()->add('multi_year', 'La suma de los montos plurianuales no coincide con el monto total.');
                return back()->withErrors($validator)->withInput();
            }
        }

        // Si OG1 = 0 emite alerta
        if($request->input('expenditure_object_id') == 0){
            $validator->errors()->add('expenditure_object_id', 'El OG no puede ser 0');
            return back()->withErrors($validator)->withInput();
        }

        // Si monto OG1 = 0 emite alerta
        if($request->input('amount1') == 0){
            $validator->errors()->add('amount1', 'El monto de OG1 no puede ser 0');
            return back()->withErrors($validator)->withInput();
        }


        // Controlamos OG2 y montos de OG2
        if($request->input('expenditure_object2_id') >0 ){
            if($request->input('amount2') == ""){
                $validator->errors()->add('amount2', 'El monto de OG2 no puede ser vacio');
                return back()->withErrors($validator)->withInput();
            }

            if($request->input('amount2') == 0){
                $validator->errors()->add('amount2', 'El monto de OG2 no puede ser 0');
                return back()->withErrors($validator)->withInput();
            }
            $contract->amount2 = str_replace('.', '',($request->input('amount2')));

        }

        if($request->input('expenditure_object2_id') == 0 ){
            if($request->input('amount2') == ""){
                $validator->errors()->add('amount2', 'El monto de OG2 debe ser 0');
                return back()->withErrors($validator)->withInput();
            }

            if($request->input('amount2') > 0){
                $validator->errors()->add('amount2', 'El monto de OG2 debe ser 0');
                return back()->withErrors($validator)->withInput();
            }
            $contract->amount2 = str_replace('.', '',($request->input('amount2')));
        }

        // Controlamos OG3 y montos de OG3
        if($request->input('expenditure_object3_id') >0 ){

            if($request->input('amount3') == ""){
                $validator->errors()->add('amount3', 'El monto de OG3 no puede ser vacio');
                return back()->withErrors($validator)->withInput();
            }

            if($request->input('amount3') == 0){
                $validator->errors()->add('amount3', 'El monto de OG3 no puede ser 0');
                return back()->withErrors($validator)->withInput();
            }

            $contract->amount3 = str_replace('.', '',($request->input('amount3')));
        }

        if($request->input('expenditure_object3_id') == 0 ){
            if($request->input('amount3') == ""){
                $validator->errors()->add('amount3', 'El monto de OG3 debe ser 0');
                return back()->withErrors($validator)->withInput();
            }

            if($request->input('amount3') > 0){
                $validator->errors()->add('amount3', 'El monto de OG3 debe ser 0');
                return back()->withErrors($validator)->withInput();
            }
            $contract->amount3 = str_replace('.', '',($request->input('amount3')));
        }

        // if(empty($request->input('monto_adjudica'))){
        //     $budget->monto_adjudica = 0;
        // }else{
        //     $monto_adjudica = $request->input('monto_adjudica');
        //     $budget->monto_adjudica = str_replace('.', '',$monto_adjudica);
        // }

        // Controlamos OG4 y montos de OG4
        if($request->input('expenditure_object4_id') >0 ){
            if($request->input('amount4') == ""){
                $validator->errors()->add('amount4', 'El monto de OG4 no puede ser vacio');
                return back()->withErrors($validator)->withInput();
            }

            if($request->input('amount4') == 0){
                $validator->errors()->add('amount4', 'El monto de OG4 no puede ser 0');
                return back()->withErrors($validator)->withInput();
            }
            $contract->amount4 = str_replace('.', '',($request->input('amount4')));
        }

        if($request->input('expenditure_object4_id') == 0 ){
            if($request->input('amount4') == ""){
                $validator->errors()->add('amount4', 'El monto de OG4 debe ser 0');
                return back()->withErrors($validator)->withInput();
            }

            if($request->input('amount4') > 0){
                $validator->errors()->add('amount4', 'El monto de OG4 debe ser 0');
                return back()->withErrors($validator)->withInput();

            }
            $contract->amount4 = str_replace('.', '',($request->input('amount4')));
        }

        // Controlamos OG5 y montos de OG5
        if($request->input('expenditure_object5_id') >0 ){
            if($request->input('amount5') == ""){
                $validator->errors()->add('amount5', 'El monto de OG5 no puede ser vacio');
                return back()->withErrors($validator)->withInput();
            }

            if($request->input('amount5') == 0){
                $validator->errors()->add('amount5', 'El monto de OG5 no puede ser 0');
                return back()->withErrors($validator)->withInput();
            }
            $contract->amount5 = str_replace('.', '',($request->input('amount5')));
        }

        if($request->input('expenditure_object5_id') == 0 ){
            if($request->input('amount5') == ""){
                $validator->errors()->add('amount5', 'El monto de OG5 debe ser 0');
                return back()->withErrors($validator)->withInput();
            }

            if($request->input('amount5') > 0){
                $validator->errors()->add('amount5', 'El monto de OG5 debe ser 0');
                return back()->withErrors($validator)->withInput();

            }
            $contract->amount5 = str_replace('.', '',($request->input('amount5')));
        }

        // Controlamos OG6 y montos de OG6
        if($request->input('expenditure_object6_id') >0 ){
            if($request->input('amount6') == ""){
                $validator->errors()->add('amount6', 'El monto de OG6 no puede ser vacio');
                return back()->withErrors($validator)->withInput();
            }

            if($request->input('amount6') == 0){
                $validator->errors()->add('amount6', 'El monto de OG6 no puede ser 0');
                return back()->withErrors($validator)->withInput();
            }
            $contract->amount6 = str_replace('.', '',($request->input('amount6')));
        }

        if($request->input('expenditure_object6_id') == 0 ){
            if($request->input('amount6') == ""){
                $validator->errors()->add('amount6', 'El monto de OG6 debe ser 0');
                return back()->withErrors($validator)->withInput();
            }

            if($request->input('amount6') > 0){
                $validator->errors()->add('amount6', 'El monto de OG6 debe ser 0');
                return back()->withErrors($validator)->withInput();

            }
            $contract->amount6 = str_replace('.', '',($request->input('amount6')));
        }

        //creamos variables de montos de OG y sumamos para ver el total de la sumatoria
        $tot_am_exp1 = str_replace('.', '',($request->input('amount1')));
        $tot_am_exp2 = str_replace('.', '',($request->input('amount2')));
        $tot_am_exp3 = str_replace('.', '',($request->input('amount3')));
        $tot_am_exp4 = str_replace('.', '',($request->input('amount4')));
        $tot_am_exp5 = str_replace('.', '',($request->input('amount5')));
        $tot_am_exp6 = str_replace('.', '',($request->input('amount6')));
        $totales_am_exp = ($tot_am_exp1 + $tot_am_exp2 + $tot_am_exp3 + $tot_am_exp4 + $tot_am_exp5 + $tot_am_exp6);

        // // //creamos variable para capturar el monto total del pedio sacandole el separador de miles
        $total_amount_fin = str_replace('.', '',($request->input('total_amount')));

        // // //validamos si el monto total de los OG no supera el monto total del pedido
        if($totales_am_exp <> $total_amount_fin){
            $validator->errors()->add('total_amount', 'El monto TOTAL de los OG NO COINCIDE con el TOTAL del PEDIDO, verifique los montos de los OG');
            return back()->withErrors($validator)->withInput();
        }


        //CAPTURAMOS LA DEPENDENCIA DEL REQUIRENTE SI ES DE PAC NO SE CAPTURA
        if($request->user()->hasPermission(['admin.orders.update']) && $request->user()->dependency_id == 59){

        }else{
            $contract->dependency_id = $request->user()->dependency_id;//dependencia del usuario
        }

        $contract->responsible = $request->input('responsible');
        $contract->year = $request->input('year');
        $contract->modality_id = $request->input('modality');
        $contract->dncp_pac_id = $request->input('dncp_pac_id');
        // $contract->begin_date = $request->input('begin_date');
        $contract->begin_date = empty($request->input('begin_date')) ? NULL : date('Y-m-d', strtotime(str_replace("/", "-", $request->input('begin_date'))));
        $contract->sub_program_id = $request->input('sub_program');
        $contract->funding_source_id = $request->input('funding_source');
        $contract->financial_organism_id = $request->input('financial_organism');

        // $item->total_amount = str_replace('.', '',$total_amount);
        $contract->total_amount = str_replace('.', '',($request->input('total_amount')));

        $contract->description = $request->input('description');
        $contract->ad_referendum = $request->input('ad_referendum');
        $contract->plurianualidad = $request->input('plurianualidad');
        $contract->system_awarded_by = $request->input('system_awarded_by');
        $contract->fonacide = $request->input('fonacide');
        $contract->catalogs_technical_annexes = $request->input('catalogs_technical_annexes');
        $contract->alternative_offers = $request->input('alternative_offers');
        $contract->open_contract = $request->input('open_contract');
        $contract->period_time = $request->input('period_time');
        $contract->manufacturer_authorization = $request->input('manufacturer_authorization');
        $contract->financial_advance_percentage_amount = $request->input('financial_advance_percentage_amount');
        $contract->technical_specifications = $request->input('technical_specifications');
        $contract->samples = $request->input('samples');
        $contract->delivery_plan = $request->input('delivery_plan');
        $contract->evaluation_committee_proposal = $request->input('evaluation_committee_proposal');
        $contract->payment_conditions = $request->input('payment_conditions');
        $contract->contract_guarantee = $request->input('contract_guarantee');
        $contract->product_guarantee = $request->input('product_guarantee');
        $contract->contract_administrator = $request->input('contract_administrator');
        $contract->contract_validity = $request->input('contract_validity');
        $contract->additional_technical_documents = $request->input('additional_technical_documents');
        $contract->additional_qualified_documents = $request->input('additional_qualified_documents');
        $contract->price_sheet = $request->input('price_sheet');
        $contract->property_title = $request->input('property_title');
        $contract->magnetic_medium = $request->input('magnetic_medium');
        $contract->referring_person_data = $request->input('referring_person_data');
        $contract->form4_city = $request->input('form4_city');
        // $contract->form4_date = $request->input('form4_date');
        //$contract->form4_date = date('Y-m-d', strtotime(str_replace("/", "-", $request->input('form4_date'))));
        $contract->form4_date = empty($request->input('form4_date')) ? NULL : date('Y-m-d', strtotime(str_replace("/", "-", $request->input('form4_date'))));
        $contract->dncp_resolution_number = $request->input('dncp_resolution_number');
        // $contract->dncp_resolution_date = $request->input('dncp_resolution_date');
        //$contract->dncp_resolution_date = date('Y-m-d', strtotime(str_replace("/", "-", $request->input('dncp_resolution_date'))));
        $contract->dncp_resolution_date = empty($request->input('dncp_resolution_date')) ? NULL : date('Y-m-d', strtotime(str_replace("/", "-", $request->input('dncp_resolution_date'))));

        $contract->expenditure_object_id = $request->input('expenditure_object_id');
        $contract->expenditure_object2_id = $request->input('expenditure_object2_id');
        $contract->expenditure_object3_id = $request->input('expenditure_object3_id');
        $contract->expenditure_object4_id = $request->input('expenditure_object4_id');
        $contract->expenditure_object5_id = $request->input('expenditure_object5_id');
        $contract->expenditure_object6_id = $request->input('expenditure_object6_id');

        //SE CAPTURA EL MONTO DEL OG1 EL RESTO SE CAPTURA EN LAS VALIDACIONES DE ARRIBA
        $contract->amount1 = str_replace('.', '',($request->input('amount1')));

        $contract->covid = $request->input('covid');

        $new_order = new Order;
        $new_order->dependency_id = $request->user()->dependency_id;
        $new_order->responsible = $contract['responsible'];
        $new_order->year = $contract['year'];
        $new_order->modality_id = $contract['modality_id'];
        $new_order->dncp_pac_id = $contract['dncp_pac_id'];
        $new_order->begin_date = empty($contract['begin_date']) ? NULL : date('Y-m-d', strtotime(str_replace("/", "-", $contract['begin_date'])));
        $new_order->sub_program_id = $contract['sub_program_id'];
        $new_order->funding_source_id = $contract['funding_source_id'];
        $new_order->financial_organism_id = $contract['financial_organism_id'];
        $new_order->total_amount = $contract['total_amount'];

        $new_order->expenditure_object_id = $contract['expenditure_object'];
        $new_order->amount1 = $contract['amount1'];
        $new_order->expenditure_object2_id = $contract['expenditure_object2_id'];
        $new_order->amount2 = $contract['amount2'];
        $new_order->expenditure_object3_id = $contract['expenditure_object3_id'];
        $new_order->amount3 = $contract['amount3'];
        $new_order->expenditure_object4_id = $contract['expenditure_object4_id'];
        $new_order->amount4 = $contract['amount4'];
        $new_order->expenditure_object5_id = $contract['expenditure_object5_id'];
        $new_order->amount5 = $contract['amount5'];
        $new_order->expenditure_object6_id = $contract['expenditure_object6_id'];
        $new_order->amount6 = $contract['amount6'];

        $new_order->description = $contract['description'];
        $new_order->ad_referendum = $contract['ad_referendum'];
        $new_order->plurianualidad = $contract['plurianualidad'];
        $new_order->system_awarded_by = $contract['system_awarded_by'];
        // $new_order->expenditure_object_id = $contract['expenditure_object_id'];
        $new_order->fonacide = $contract['fonacide'];
        $new_order->catalogs_technical_annexes = $contract['catalogs_technical_annexes'];
        $new_order->alternative_offers = $contract['alternative_offers'];
        $new_order->open_contract = $contract['open_contract'];
        $new_order->period_time = $contract['period_time'];
        $new_order->manufacturer_authorization = $contract['manufacturer_authorization'];
        $new_order->financial_advance_percentage_amount = $contract['financial_advance_percentage_amount'];
        $new_order->technical_specifications = $contract['technical_specifications'];
        $new_order->samples = $contract['samples'];
        $new_order->delivery_plan = $contract['delivery_plan'];
        $new_order->evaluation_committee_proposal = $contract['evaluation_committee_proposal'];
        $new_order->payment_conditions = $contract['payment_conditions'];
        $new_order->contract_guarantee = $contract['contract_guarantee'];
        $new_order->product_guarantee = $contract['product_guarantee'];
        $new_order->contract_administrator = $contract['contract_administrator'];
        $new_order->contract_validity = $contract['contract_validity'];
        $new_order->additional_technical_documents = $contract['additional_technical_documents'];
        $new_order->additional_qualified_documents = $contract['additional_qualified_documents'];
        $new_order->price_sheet = $contract['price_sheet'];
        $new_order->property_title = $contract['property_title'];
        $new_order->magnetic_medium = $contract['magnetic_medium'];
        $new_order->referring_person_data = $contract['referring_person_data'];
        $new_order->form4_city = $contract['form4_city'];
        //ESTABA COMENTADO
        $new_order->form4_date = date('Y-m-d', strtotime(str_replace("/", "-", $contract['form4_date'])));

        $new_order->dncp_resolution_number = $contract['dncp_resolution_number'];
        //ESTABA COMENTADO
        $new_order->dncp_resolution_date = date('Y-m-d', strtotime(str_replace("/", "-", $contract['dncp_resolution_date'])));

        $new_order->covid = $contract['covid'];



        // Si es usuario de Planificación (59) no actualiza dependencia
        if($request->user()->hasPermission(['admin.orders.update']) && $request->user()->dependency_id == 59){
            $contract->modifier_user_id = $request->user()->id;  // usuario logueado
        }else{
            $contract->dependency_id = $request->user()->dependency_id;//dependencia del usuario
            $contract->modifier_user_id = $request->user()->id;  // usuario logueado
        }
        // $contract->modifier_user_id = $request->user()->id;  // usuario logueado

        $contract->save();


        // Borramos los registros de plurianualidad anteriores
        foreach($contract->orderMultiYears as $multi_year){
            $multi_year->delete();
        }

        // Agregamos nuevos registros de plurianualidad en caso de haber pasado en el formulario
        if($request->input('plurianualidad') == 1){
            $years = $request->input('multi_year_year');
            $amounts = $request->input('multi_year_amount');
            for ($i=0; $i < count($years); $i++) {
                if(is_numeric($amounts[$i])){
                    $contract_multi_year = new OrderMultiYear;
                    $contract_multi_year->order_id = $contract->id;
                    $contract_multi_year->year = $years[$i];
                    $contract_multi_year->amount = $amounts[$i];
                    $contract_multi_year->creator_user_id = $request->user()->id;  // usuario logueado
                    $contract_multi_year->save();
                }
            }
        }
        // Original
        // return redirect()->route('orders.index')->with('success', 'Pedido modificado correctamente');

        // Cuando es derivado desde Planificación, y desea modificar el Pedido
        if($request->user()->hasPermission(['admin.orders.update']) || $request->user()->dependency_id == 59){
            return redirect()->route('plannings.orders.update', $contract->id)->with('success', 'Pedido modificado por PAC');
            // Auth::user()->dependency->description
        }else{
            // return redirect()->route('plannings.orders.update', $contract->id)->with('success', 'Pedido modificado por PAC');
            return redirect()->route('orders.show', $contract->id)->with('success', 'Pedido modificado correctamente');
        }
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
