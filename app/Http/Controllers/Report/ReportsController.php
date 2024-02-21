<?php

namespace App\Http\Controllers\Report;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\App;
use Mpdf;
// use Mpdf;

class ReportsController extends Controller{

    public function __construct()
    {
        //$this->middleware('guest');
    }

    public function index()
    {
        // for ($i = 0; $i < 5; $i++) {
        //     $html = '<h1>Hello World '.$i.'</h1>';
        //     PDF2::SetTitle('Hello World'.$i);
        //     PDF2::AddPage();
        //     PDF2::Write(0, 'Hello World'.$i);
        //     // Write the file instead of throw it in the browser
        //     PDF2::Output(public_path('hello_world' . $i . '.pdf'), 'F');
        //     PDF2::reset();
        // }
    }

    public function pdfMPDF()    {
        $tipres = DB::table('order_presentations')
        // $tipres = DB::table('level5_catalog_codes')
        ->select(['id','description'])
        ->orderBy('description','asc')
        ->get();
        return view('reports.tipres', compact('tipres'));
    }


    // Para mostrar todos los llamados que tienen contratos
    public function generarContracts()
    {
        //capturamos el nombre del método para poder cambiar el título del reporte en la vista
        $nombreMetodo = __METHOD__;

        //Donde contracts es una vista
        $contracts = DB::table('vista_contracts')//vista que muestra los datos
        ->select(['llamado', 'iddncp','number_year','year_adj','sign_date','contratista',
        'estado', 'code', 'modalidad', 'org_financ', 'tipo_contrato','contract_begin_date',
        'contract_end_date', 'total_amount', 'advance_validity','fidelity_validity','accidents_validity',
        'risks_validity','civil_resp_validity','comentarios'])
        // ->where('actual_state', '>', 1)
        ->get();

        $view = View::make('reports.contracts', compact('contracts', 'nombreMetodo'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('LLAMADO-CONTRATOS'.'.pdf');
    }

    // Para mostrar todos los llamados que estan en curso (estado = 1)
    public function generarContracts1()
    {
        //capturamos el nombre del método para poder cambiar el título del reporte en la vista
        $nombreMetodo = __METHOD__;

        //Donde contracts es una vista
        $contracts = DB::table('vista_contracts')//vista que muestra los datos
        ->select(['llamado', 'iddncp','number_year','year_adj','sign_date','contratista',
        'estado', 'code', 'modalidad', 'org_financ', 'tipo_contrato','contract_begin_date',
        'contract_end_date', 'total_amount', 'advance_validity','fidelity_validity','accidents_validity',
        'risks_validity','civil_resp_validity','comentarios'])
        ->where('state_id', '=', 1)
        ->get();

        $view = View::make('reports.contracts', compact('contracts', 'nombreMetodo'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('LLAMADO-CONTRATOS EN CURSO'.'.pdf');
    }

    // Para mostrar todos los llamados que estan rescindido (estado = 2)
    public function generarContracts2()
    {
        //capturamos el nombre del método para poder cambiar el título del reporte en la vista
        $nombreMetodo = __METHOD__;

        //Donde contracts es una vista
        $contracts = DB::table('vista_contracts')//vista que muestra los datos
        ->select(['llamado', 'iddncp','number_year','year_adj','sign_date','contratista',
        'estado', 'code', 'modalidad', 'org_financ', 'tipo_contrato','contract_begin_date',
        'contract_end_date', 'total_amount', 'advance_validity','fidelity_validity','accidents_validity',
        'risks_validity','civil_resp_validity','comentarios'])
        ->where('state_id', '=', 2)
        ->get();

        $view = View::make('reports.contracts', compact('contracts', 'nombreMetodo'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('LLAMADO-CONTRATOS CERRADOS'.'.pdf');
    }

    // Para mostrar todos los llamados que estan cerrados (estado = 3)
    public function generarContracts3()
    {
        //capturamos el nombre del método para poder cambiar el título del reporte en la vista
        $nombreMetodo = __METHOD__;

        //Donde contracts es una vista
        $contracts = DB::table('vista_contracts')//vista que muestra los datos
        ->select(['llamado', 'iddncp','number_year','year_adj','sign_date','contratista',
        'estado', 'code', 'modalidad', 'org_financ', 'tipo_contrato','contract_begin_date',
        'contract_end_date', 'total_amount', 'advance_validity','fidelity_validity','accidents_validity',
        'risks_validity','civil_resp_validity','comentarios'])
        ->where('state_id', '=', 3)
        ->get();

        $view = View::make('reports.contracts', compact('contracts', 'nombreMetodo'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('LLAMADO-CONTRATOS RESCINDIDOS'.'.pdf');
    }


    // function to display preview
    public function generarModalities()
    {
        //Donde cargos es una vista
        $modals = DB::table('modalities')
        ->select(['id','description','code','modality_type','dncp_verification','dncp_objections_verification',
        'press_publication','portal_difusion','inquiries_reception','addendas_verification',
        'addenda_publication','clarifications_publication'])
        ->get();

        $view = View::make('reports.modalities', compact('modals'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('Modalidades'.'.pdf');
    }

    // Para mostrar pedidos y llamados a Planificación
    public function generarPanel()
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_orders')//vista que muestra los datos
        ->select(['order_description','dependency','modality_type','dncp_resolution_number','modality_code','number',
        'code_supexp','dncp_pac_id','form4_date','total_amount','begin_date'])
        // ->where('actual_state', '>', 1)
        ->get();

        $view = View::make('reports.panel', compact('orders'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('PANEL-DPP'.'.pdf');
    }

    // Para mostrar llamados de Licitaciones
    public function generarPanelLicit()
    {
        $orders = DB::table('vista_uta')//vista que muestra los datos
                    ->select(['number','dncp_pac_id','order_description','modality_code','exp_obj_code','total_amount',
                    'cdp_number','cdp_date','order_state_description','begin_date','covid','year',
                    'provider','contract_number','contract_date','cc_number','cc_date','monto_adjudica'])
                    ->where('monto_adjudica', '>', 0)
                    ->wherein('modality_id', [1,2,3,7,8,17,20])
                    ->orderBy('provider','asc')
                    ->get();

                    //Donde cargos es una vista
        // $orders = DB::table('vista_orders')//vista que muestra los datos
        // ->select(['number','dncp_pac_id','order_description','modality_code','exp_obj_code','total_amount',
        //             'cdp_number','cdp_date','order_state_description','begin_date','covid','year'])
        //             ->wherein('modality_id', [1,2,3,7,8,17,20])
        //             ->get();
        $view = View::make('reports.panel_licit', compact('orders'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('PROCESOS LICITACIONES'.'.pdf');



    }

    // Para mostrar llamados de Compras Menores
    public function generarPanelMinor()
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_orders')//vista que muestra los datos
        ->select(['number','dncp_pac_id','order_description','modality_code','exp_obj_code','total_amount',
                    'cdp_number','cdp_date','order_state_description','begin_date','covid','year'])
                    ->wherein('modality_id', [4,5,6])
                    ->get();
        $view = View::make('reports.panel_minor', compact('orders'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('PROCESOS COMPRAS MENORES'.'.pdf');
    }

    // Para mostrar llamados de Compras Vía Excepción
    public function generarPanelException()
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_orders')//vista que muestra los datos
        ->select(['number','dncp_pac_id','order_description','modality_code','exp_obj_code','total_amount',
                    'cdp_number','cdp_date','order_state_description','begin_date','covid','year'])
                    ->wherein('modality_id', [9,10,28])
                    ->get();
        $view = View::make('reports.panel_exception', compact('orders'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('PROCESOS COMPRAS VIA EXCEPCIÓN'.'.pdf');
    }

    // Para mostrar Procesos de Pedidos sin filtrar por dependencia que están en proceso aún no adjudicados
    public function generarPanelUta()
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_uta')//vista que muestra los datos
                    ->select(['number','dncp_pac_id','order_description','modality_code','exp_obj_code','total_amount',
                    'cdp_number','cdp_date','order_state_description','begin_date','covid','year',
                    'provider','contract_number','contract_date','cc_number','cc_date','monto_adjudica'])
                    ->where('monto_adjudica', null)
                    ->where('actual_state', '>', 0)
                    ->orderBy('provider','asc')
                    ->get();


        // if ($val = $orders->count()){
        //     var_dump($val);
        // }

        //SE CUENTAN LOS REGISTROS, SI SUPERA MUESTRA COMO HTML, SINO MUESTRA COMO PDF
        $val = $orders->count();

        if ($val > 500) {
            return view('reports.panel_uta_ht', compact('orders'));
        }else{
            $view = View::make('reports.panel_uta', compact('orders'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setPaper('A4', 'landscape');//coloca en apaisado
            return $pdf->stream('LLAMADOS EN CURSO'.'.pdf');
        }

        // PARA MOSTRAR COMO PDF
        // $view = View::make('reports.panel_uta', compact('orders'))->render();
        // $pdf = App::make('dompdf.wrapper');
        // $pdf->loadHTML($view);
        // $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        // return $pdf->stream('LLAMADOS EN CURSO'.'.pdf');

        // PARA MOSTRAR COMO VISTA EN HTML
        return view('reports.panel_uta', compact('orders'));
    }

    // Para mostrar Procesos de Pedidos sin filtrar por dependencia que ya están adjudicados
    public function generarPanelUta2()
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_uta')//vista que muestra los datos
                    ->select(['number','dncp_pac_id','order_description','modality_code','exp_obj_code','total_amount',
                    'cdp_number','cdp_date','order_state_description','begin_date','covid','year',
                    'provider','contract_number','contract_date','cc_number','cc_date','monto_adjudica'])
                    ->where('monto_adjudica', '>', 0)
                    ->orderBy('provider','asc')
                    ->get();

        $val = $orders->count();

        if ($val > 500) {
            return view('reports.panel_uta', compact('orders'));
        }else{
            $view = View::make('reports.panel_uta', compact('orders'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setPaper('A4', 'landscape');//coloca en apaisado
            return $pdf->stream('LLAMADOS ADJUDICADOS'.'.pdf');
        }

        // $view = View::make('reports.panel_uta', compact('orders'))->render();
        // $pdf = App::make('dompdf.wrapper');
        // $pdf->loadHTML($view);
        // $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        // return $pdf->stream('LLAMADOS ADJUDICADOS'.'.pdf');
    }

    // Para mostrar Procesos de Pedidos Anulados
    public function generarPanelUta3()
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_uta')//vista que muestra los datos
                    ->select(['number','dncp_pac_id','order_description','modality_code','exp_obj_code','total_amount',
                    'cdp_number','cdp_date','order_state_description','begin_date','covid','year',
                    'provider','contract_number','contract_date','cc_number','cc_date','monto_adjudica'])
                    ->where('actual_state', '=', 0)
                    ->orderBy('provider','asc')
                    ->get();

        $val = $orders->count();

        if ($val > 500) {
            return view('reports.panel_uta', compact('orders'));
        }else{
            $view = View::make('reports.panel_uta', compact('orders'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setPaper('A4', 'landscape');//coloca en apaisado
            return $pdf->stream('PEDIDOS ANULADOS'.'.pdf');
        }
    }


    // Para mostrar Procesos a usuarios de PEDIDOS
    public function generarPanelPedido($dependency)
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_uta')//vista que muestra los datos
                    ->select(['number','dncp_pac_id','order_description','modality_code','exp_obj_code','total_amount',
                    'cdp_number','cdp_date','order_state_description','begin_date','covid','year',
                    'provider','contract_number','contract_date','cc_number','cc_date','monto_adjudica'])
                    ->where('dependency_id', '=', $dependency)
                    ->where('monto_adjudica', null)
                    ->where('actual_state', '>', 0)
                    ->orderBy('provider','asc')
                    ->get();

        $val = $orders->count();

        if ($val > 500) {
            return view('reports.panel_uta', compact('orders'));
        }else{
            $view = View::make('reports.panel_uta', compact('orders'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setPaper('A4', 'landscape');//coloca en apaisado
            return $pdf->stream('LLAMADOS EN CURSO'.'.pdf');
        }

    }

    // Para mostrar Procesos a usuarios de PEDIDOS
    public function generarPanelPedido2($dependency)
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_uta')//vista que muestra los datos
                    ->select(['number','dncp_pac_id','order_description','modality_code','exp_obj_code','total_amount',
                    'cdp_number','cdp_date','order_state_description','begin_date','covid','year',
                    'provider','contract_number','contract_date','cc_number','cc_date','monto_adjudica'])
                    ->where('dependency_id', '=', $dependency)
                    ->where('monto_adjudica', '>', 0)
                    ->orderBy('provider','asc')
                    ->get();

        $val = $orders->count();

        if ($val > 500) {
            return view('reports.panel_uta', compact('orders'));
        }else{
            $view = View::make('reports.panel_uta', compact('orders'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            $pdf->setPaper('A4', 'landscape');//coloca en apaisado
            return $pdf->stream('LLAMADOS EN CURSO'.'.pdf');
        }
    }

    public function pdfTipres()    {
        $tipres = DB::table('order_presentations')
        ->select(['id','description'])
        ->orderBy('description','asc')
        ->get();
        $view = View::make('reports.tipres', compact('tipres'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('Tipres'.'.pdf');
    }

    public function pdfTipresUnid()    {
        $tipresunid = DB::table('order_measurement_units')
        ->select(['id','description'])
        ->orderBy('description','asc')
        ->get();
//
        $view = View::make('reports.tipresunid', compact('tipresunid'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('TipresUnid'.'.pdf');
    }

    public function pdfPrefs($order_id)
    {
        $prefs = DB::table('vista_award')
        ->select(['nombre_pedido','dncp_pac_id','proveedor','monto','nro_item','nro_llamado','codc5','descrip_cc5'])
        ->where('order_id', '=', $order_id)
        ->orderBy('nro_item','asc')
        ->orderBy('proveedor','asc')
        ->orderBy('dncp_pac_id','asc')
        ->get();
//
        $view = View::make('reports.prefs', compact('prefs'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('Prefs'.'.pdf');
    }

//     public function pdfUsers()
//     {
//         // Donde users es una vista
//         // $users = DB::table('vista_users')
//         // ->select(['document','name','lastname','dependency','rol','position','permission'])
//         // ->get();

//         // Donde users2 es una vista
//         $users = DB::table('vista_users2')
//         ->select(['document','name','lastname','description'])
//         ->get();
// //
//         $view = View::make('reports.users', compact('users'))->render();
//         $pdf = App::make('dompdf.wrapper');
//         $pdf->loadHTML($view);
//         return $pdf->stream('Users'.'.pdf');
//     }

    public function pdfUsers2()
    {
        //SE ORDENA POR CI SE CASTEA VARCHAR A INTEGER EN ORDERBYRAW
        $users = DB::table('vista_users_full')
        ->select(['nombre','apellido','ci','email','dependencia','cargo','rol','nro_uoc','sicp','state'])
        ->orderByRaw('ci::int')
        ->get();

        // PARA MOSTRAR COMO PDF
        $view = View::make('reports.users2', compact('users'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('Users'.'.pdf');

        // PARA MOSTRAR COMO VISTA EN HTML
        // return view('reports.users2', compact('users'));

    }

     //MUESTRA PROVIDERS
    public function pdfProviders()
    {
        //SE ORDENA POR CI SE CASTEA VARCHAR A INTEGER EN ORDERBYRAW
        $providers = DB::table('providers')
        ->select(['description','ruc','telefono','email_oferta','email_ocompra','representante'])
        ->orderBy('description')
        ->get();

        // PARA MOSTRAR COMO PDF
        $view = View::make('reports.providers', compact('providers'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('Contratistas'.'.pdf');

        // PARA MOSTRAR COMO VISTA EN HTML
        // return view('reports.users2', compact('users'));

    }

    //MUESTRA DEPENDENCIAS
    public function dependencies()
    {
        $dependencies = DB::table('vista_dependencies_full')
        ->select(['sup_dependencia','dependencia','tipo_dependencia','tipo_uoc','uoc_number','sicp'])
        ->get();

        // PARA MOSTRAR COMO PDF
        $view = View::make('reports.dependencies', compact('dependencies'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('Dependencies'.'.pdf');

        // PARA MOSTRAR COMO VISTA EN HTML
        // return view('reports.users2', compact('users'));

    }


    public function generarForm1($order_id)
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_pdf_form')//vista que muestra los datos
        ->select(['order_id','number','responsible','dependencies_description','modalities_code',
        'orders_description','begin_date','code_supexp','pt','pc','activity_code','ff','fo',
        'total_amount','anio2022','monto_2022','anio2023','monto_2023','anio2024','monto_2024','anio2025','monto_2025',
        'year','og1','og2','og3','og4','og5','og6'])
        ->where('order_id', '=', $order_id)
        ->get();

        $anio = date("Y");
        //$anio= date(Y);
        $view = View::make('reports.form1', compact('orders'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('Formulario1'.'.pdf');
    }

    public function generarForm2($order_id)
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_orders')//vista que muestra los datos
        ->select(['dependency','order_description','ad_referendum','plurianualidad',
        'system_awarded_by','code_supexp', 'exp_obj_code','program_type_code','program_code','activity_code',
        'ff_code','of_code','total_amount','fonacide','modality_type','catalogs_technical_annexes',
        'alternative_offers','open_contract','period_time','manufacturer_authorization',
        'financial_advance_percentage_amount','technical_specifications','samples','delivery_plan',
        'evaluation_committee_proposal','payment_conditions','contract_guarantee',
        'product_guarantee','contract_administrator','contract_validity','additional_technical_documents',
        'additional_qualified_documents','price_sheet','property_title','magnetic_medium',
        'referring_person_data','og1','og2','og3','og4','og5','og6'])
        ->where('order_id', '=', $order_id)
        ->get();

        $view = View::make('reports.form2', compact('orders'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('Formulario2'.'.pdf');
    }

    //Form3 para conrato abierto
    public function generarForm31($order_id)
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_orders_items')//vista que muestra los datos
        ->select(['dependency','batch','item_number','code_supexp','exp_obj_code',
        'program_type_code','program_code','activity_code',
        'ff_code','code','level5_catalog_codes_description','technical_specifications',
        'order_presentations_description','order_measurement_units_description','min_quantity','max_quantity',
        'unit_price','total_amount','total_amount_min', 'quantity'])
        ->where('order_id', '=', $order_id)
        ->get();

        $view = View::make('reports.form31', compact('orders'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('Formulario3'.'.pdf');

        //return view('reports.form3', compact('orders'));
    }

    //Form3 para conrato cerrado
    public function generarForm3($order_id)
    {
        //Donde cargos es una vista
        $orders = DB::table('vista_orders_items')//vista que muestra los datos
        ->select(['dependency','batch','item_number','code_supexp','exp_obj_code',
        'program_type_code','program_code','activity_code',
        'ff_code','code','level5_catalog_codes_description','technical_specifications',
        'order_presentations_description','order_measurement_units_description','min_quantity','max_quantity',
        'unit_price','total_amount','total_amount_min', 'quantity'])
        ->where('order_id', '=', $order_id)
        ->get();

        $view = View::make('reports.form3', compact('orders'))->render();
        $pdf = App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        $pdf->setPaper('A4', 'landscape');//coloca en apaisado
        return $pdf->stream('Formulario3'.'.pdf');

        //return view('reports.form3', compact('orders'));
    }

    public function generarForm4($order_id)
    {
        $order = Order::find($order_id);
        //Donde cargos es una vista
        $f4_1 = DB::table('vista_form4_1')//vista que muestra los datos
        ->select (['form4_city','form4_date','providers_description','ruc'])
        ->where('order_id', '=', $order_id)
        ->whereIn('request_provider_type', [1])//EMPRESAS PARTICIPANTES
        ->get();

        $f4_2 = DB::table('vista_form4_2')//vista que muestra los datos
        ->select (['order_id','item_number','providers_description','level5_catalog_codes_description','dncp_pac_id','amount'])
        ->where('order_id', '=', $order_id)
        ->get();

        // validamos los datos
        // $validator = Validator::make($item, $rules); // Creamos un objeto validator
        // if ($validator->fails()) {
        //     return back()->withErrors($validator)->withInput()->with('fila', $row);
        // }

        // Chequea si existe el código de catálogo5
        // $level5_catalog_code = Level5CatalogCode::where('code', $item['level5_catalog_code'])->get()->first();
        // if(($request->user()->dependency_id == 59)){
        //     return redirect()->route('plannings.show', $order_id)->with('success', 'Ítem modificado correctamente'); // Caso usuario posee rol pedidos
        // }else{
        //     return redirect()->route('orders.show', $order_id)->with('success', 'Ítem modificado correctamente'); // Caso usuario posee rol pedidos
        // }

        if (is_null($f4_2 )) {
            // return back()->withErrors($validator)->withInput()->with('fila', $row);
            // $view = View::make('reports.form4', compact('f4_1','f4_2', 'order'))->render();
            // $pdf = App::make('dompdf.wrapper');
            // $pdf->loadHTML($view);
            // //$pdf->setPaper('A4', 'landscape');//coloca en apaisado
            // return $pdf->stream('Formulario4'.'.pdf');
        }else{
            $view = View::make('reports.form4', compact('f4_1','f4_2', 'order'))->render();
            $pdf = App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            //$pdf->setPaper('A4', 'landscape');//coloca en apaisado
            return $pdf->stream('Formulario4'.'.pdf');
        }



    }

    //Para mostrar un archivo PDF guardado en el Proyecto
    public function pdfArchivo(){
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=Modalidades_DNCP.pdf");
        readfile("files/Modalities.pdf");
    }

    //Para mostrar un archivo PDF guardado en el Proyecto - SECUENCIA DE LICITACIONES
    public function pdfSecuencia(){
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=Secuencia LICITACIONES.pdf");
        readfile("files/LICITACIONES.pdf");
    }

    //Para mostrar un archivo PDF guardado en el Proyecto - SECUENCIA DE COMPRAS MENORES
    public function pdfSecuencia2(){
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=Secuencia COMPRAS MENORES.pdf");
        readfile("files/COMPRAS MENORES.pdf");
    }

    //Para mostrar un archivo PDF guardado en el Proyecto - SECUENCIA DE CVE
    public function pdfSecuencia3(){
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=Secuencia PROCESOS COMPLEMENTARIOS.pdf");
        readfile("files/PROCESOS COMPLEMENTARIOS.pdf");
    }

    //Para mostrar un archivo PDF guardado en el Proyecto - SECUENCIA DE CVE
    public function pdfChange_pass(){
        header("Content-type: application/pdf");
        header("Content-Disposition: inline; filename=Usuarios - Cambio de Password.pdf");
        readfile("files/users.pdf");
    }
}
