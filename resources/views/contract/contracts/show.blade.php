@extends('layouts.app')

@push('styles')
<style type="text/css">
.table td, .table th {
    padding: 0.2rem 0.5rem;
    font-size: 14px
}
.tab-content.card-block {
    padding: 1.25rem 0.5rem;
}
}
.columna1 { width: 1%; text-align: center;}
.columna2 { width: 10%; text-align: left;}
.columna3 { width: 9%; text-align: left;}
.columna4 { width: 16%; text-align: left;}
.columna5 { width: 2%; text-align: center;}
.columna6 { width: 4%; text-align: center;}
.columna7 { width: 4%; text-align: center;}
.columna8 { width: 3%; text-align: center;}
.columna9 { width: 3%; text-align: left;}
.columna10 { width: 3%; text-align: center;}
.columna11 { width: 9%; text-align: left;}
.columna12 { width: 10%; text-align: left;}

p.centrado {

}
</style>
@endpush

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-list bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Visualizar Llamado</h5>
                        <span>Llamado Nº {{ $order->number }}</span>
                        <br><br>
                        {{-- <h6>Estado Actual: {{ $order->orderState->id." - ".$order->orderState->description }}</h6> --}}
                        <h5><p style="font-size: 17px; font-weight: bold; color:#FF0000">Estado Actual: {{ $order->orderState->id." - ".$order->orderState->description }}</p></h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="page-header-breadcrumb">
                    <ul class=" breadcrumb breadcrumb-title">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}"><i class="feather icon-home"></i></a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('contracts.index') }}">Llamados</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="pcoded-inner-content">
        <div class="main-body">
            <div class="page-wrapper">
                <div class="page-body">
                    <div class="row">
                        <div class="col-sm-12">

                            <div class="card">
                                <div class="card-header">
                                    <div class="row">
                                        <div class="col-sm-10 text-left">
                                            {{-- <h5>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."-".$order->description }} --}}
                                            {{-- @if ($order->covid==0)
                                                <h5>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."/".$order->year."-".$order->description }}
                                            @else
                                                <h5>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."/".$order->year."-".$order->description }}
                                                <label style="font-size: 16px; font-weight: bold; color:blue;background-color:yellow;">Proceso COVID</label></h5>
                                            @endif  --}}

                                            {{-- PARA MOSTRAR SI ES COVID Y SI ES URGENCIA IMPOSTERGABLE --}}
                                            @if ($order->covid==0)
                                                @if ($order->unpostponable==0)
                                                    <h5>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."/".$order->year."-".$order->description }}
                                                @else
                                                    <h5>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."/".$order->year."-".$order->description }}
                                                    <label style="color:red;font-weight: bold;background-color:yellow">(URGENCIA IMPOSTERGABLE)</span> </td></label></h5>
                                                @endif
                                            @else
                                                @if ($order->unpostponable==0)
                                                    <h5>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."/".$order->year."-".$order->description }}
                                                    <label style="font-size: 16px; font-weight: bold; color:blue;background-color:yellow;">Proceso COVID</label></h5>
                                                @else
                                                    <h5>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."/".$order->year."-".$order->description }}
                                                        <label style="font-size: 16px; font-weight: bold; color:blue;background-color:yellow;">Proceso COVID </label>
                                                        <label style="color:red;font-weight: bold;background-color:yellow"> (URGENCIA IMPOSTERGABLE)</label></h5>
                                                @endif
                                            @endif

                                            @if ($order->urgency_state == "ALTA")
                                                <label class="label label-danger m-l-5">Prioridad {{ $order->urgency_state }}</label></h5>
                                            @else
                                                @if ($order->urgency_state == "MEDIA")
                                                    <label class="label label-warning m-l-5">Prioridad {{ $order->urgency_state }}</label></h5>
                                                @else
                                                    <label class="label label-info m-l-5">Prioridad {{ $order->urgency_state }}</label></h5>
                                                @endif
                                            @endif

                                            <h5><p style="font-size: 17px; font-weight: bold; color:blue">SIMESE: {{ is_null($order->simese->first()) ? '' : number_format($order->simese->first()['simese'],'0', ',','.')."/".$order->simese->first()['year'] }}</p></h5>

                                            @if ($order->open_contract == 1)
                                                <h5><a style="font-size: 15px; font-weight: bold; color:red"> Tipo Contrato: Abierto</a></h5>
                                            @else
                                                @if ($order->open_contract == 2)
                                                    <h5><a style="font-size: 15px; font-weight: bold; color:red"> Tipo Contrato: Cerrado</a></h5>
                                                @else
                                                    <h5><a style="font-size: 15px; font-weight: bold; color:red"> Tipo Contrato: Abierto con MontoMin y MontoMáx</a></h5>
                                                @endif
                                            @endif

                                        </div>
                                            <div class="col-sm-2">

                                            @if (in_array($order->actual_state, [70]))
                                                <button class="btn btn-danger dropdown-toggle waves-effect" type="button" id="acciones" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Recibir Proceso</button>
                                                {{-- <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="recibeOrder({{ $order->id }});">Recibir Proceso Llamado</a> --}}
                                                <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="recibeOrder({{ $order->id }});">Recibir de Adjudicaciones</a>
                                            @else
                                                <button class="btn btn-primary dropdown-toggle waves-effect" type="button" id="acciones" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones</button>
                                            @endif
                                            <div class="dropdown-menu" aria-labelledby="acciones" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">

                                            {{-- Verificamos que el pedido tenga estados PROCESADO ADJUDICACIONES 1ra ETAPA 1(70) --}}
                                            {{-- @if (in_array($order->actual_state, [70]))
                                                <button class="btn btn-primary dropdown-toggle waves-effect" type="button" id="acciones" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones</button>
                                                <div class="dropdown-menu" aria-labelledby="acciones" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                    <a class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="recibeOrder({{ $order->id }});">Recibir de Adjudicaciones</a>
                                                </div>
                                            @endif --}}

                                            {{-- Verificamos que el pedido tenga estado RECIBIDO CONTRATOS 1RA ETAPA --}}
                                            @if ($order->actual_state == 75)
                                                <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="deriveOrder({{ $order->id }});">Derivar a Adjudicaciones para continuar proceso</a>
                                                <a style="font-size: 14px; font-weight: bold; color:white;background-color:red;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="deriveAwardObs({{ $order->id }});">Derivar a Adjudicaciones con observaciones</a>
                                                <a style="font-size: 14px; font-weight: bold; color:white;background-color:red;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="deriveComité({{ $order->id }});">Derivar a Comité con observaciones</a>
                                            @endif

                                            {{-- @if ($order->actual_state == 199)
                                                <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="deriveAwardObs({{ $order->id }});">Derivar a Adjudicaciones con observaciones</a>
                                            @endif

                                            @if ($order->actual_state == 200)
                                                <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="deriveComité({{ $order->id }});">Derivar a Comité con observaciones</a>
                                            @endif --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-block">

                                    <ul class="nav nav-tabs md-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab"><i class="fa fa-tasks"></i> Datos del Llamado</a>
                                            <div class="slide"></div>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab3" role="tab"><i class="fa fa-list"></i> Ítems adjudicados</a>
                                            <div class="slide"></div>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab2" role="tab"><i class="fa fa-briefcase"></i> Empresas adjudicadas</a>
                                            <div class="slide"></div>
                                        </li>

                                        {{-- <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab4" role="tab"><i class="fa fa-file-pdf-o"></i> Formularios</a>
                                            <div class="slide"></div>
                                        </li> --}}
                                        {{-- <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab5" role="tab"><i class="fa fa-file-text-o"></i> SIMESE Relacionado</a>
                                            <div class="slide"></div>
                                        </li> --}}
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab6" role="tab"><i class="fa fa-file-archive-o"></i> Archivos (Anteced.)</a>
                                            <div class="slide"></div>
                                        </li>
                                        {{-- //Se controla que tenga estado recibido Contratos para mostrar estos TABS --}}
                                        @if ($order->actual_state >= 75)
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tab7" role="tab"><i class="fa fa-folder-open-o"></i> Contratos</a>
                                                <div class="slide"></div>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tab8" role="tab"><i class="fa fa-building-o"></i> Addendas</a>
                                                <div class="slide"></div>
                                            </li>
                                        @endif
                                    </ul>
                                    <div class="tab-content card-block">
                                        <div class="tab-pane active" id="tab1" role="tabpanel">
                                            <h5 class="text-center">Datos Proyecto de PAC</h5>
                                            <table class="table table-striped table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td><label class="col-form-label f-w-600">Dependencia:</label></td>
                                                        <td><label class="col-form-label f-w-600">Responsable:</label></td>
                                                        <td><label class="col-form-label f-w-600">Modalidad:</label></td>
                                                        <td><label class="col-form-label f-w-600">DNCP PAC:</label></td>
                                                        <td><label class="col-form-label f-w-600">AÑO:</label></td>
                                                        <td><label class="col-form-label f-w-600">Fecha de Inicio:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $order->dependency->description }}</td>
                                                        <td>{{ $order->responsible }}</td>
                                                        <td>{{ $order->modality->description }}</td>
                                                        <td>{{ $order->dncpPacIdFormat() }}</td>
                                                        <td>{{ $order->year }}</td>
                                                        <td>{{ $order->beginDateFormat() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><label class="col-form-label f-w-600">Línea Presupuestaria:</label></td>
                                                        <td><label class="col-form-label f-w-600">Fuente de Financiamiento:</label></td>
                                                        <td><label class="col-form-label f-w-600">Organismo Financiero:</label></td>
                                                        {{-- <td colspan="2"><label class="col-form-label f-w-600">Monto total:</label></td> --}}
                                                        <td colspan="2" style="font-size: 16px;color:blue;font-weight: bold">{{ 'Gs. '.$order-> totalAmountFormat() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $order->subProgram->budgetStructure() }}</td>
                                                        <td>{{ $order->fundingSource->description }}</td>
                                                        <td>{{ $order->financialOrganism->description }}</td>
                                                        <td colspan="2">{{ 'Gs. '.$order->totalAmountFormat() }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <h5 class="text-center">Requisitos de Solicitud de Adquisición de Bienes y Servicios</h5>
                                            <table class="table table-striped table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td><label class="col-form-label f-w-600">Ad Referendum:</label></td>
                                                        <td><label class="col-form-label f-w-600">Plurianualidad:</label></td>
                                                        <td><label class="col-form-label f-w-600">Sistema de Adjudicación por:</label></td>
                                                        <td><label class="col-form-label f-w-600">Sub Objeto de Gasto:</label></td>
                                                        <td><label class="col-form-label f-w-600">Fonacide:</label></td>
                                                        <td><label class="col-form-label f-w-600">Modalidad del Llamado:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $order->ad_referendum ? "SÍ" : "NO" }}</td>
                                                        <td>{{ $order->plurianualidad ? "SÍ" : "NO" }}</td>
                                                        <td>{{ $order->system_awarded_by }}</td>
                                                        <td>{{ $order->expenditureObject->code }}</td>
                                                        <td>{{ $order->fonacide ? "SÍ" : "NO" }}</td>
                                                        <td>{{ $order->modality_description }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"><label class="col-form-label f-w-600">La convocante aceptará catálogos, anexos técnicos, folletos y otros textos:</label></td>
                                                        <td><label class="col-form-label f-w-600">Se considerarán ofertas alternativas:</label></td>
                                                        <td><label class="col-form-label f-w-600">Se utilizará la modalidad de contrato abierto:</label></td>
                                                        <td colspan="2"><label class="col-form-label f-w-600">El período de tiempo estimado de funcionamiento de los bienes:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">{{ $order->catalogs_technical_annexes ? "SÍ" : "NO" }}</td>
                                                        <td>{{ $order->alternative_offers ? "SÍ" : "NO" }}</td>
                                                        <td>{{ $order->open_contract ? "SÍ" : "NO" }}</td>
                                                        <td colspan="2">{{ $order->period_time }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><label class="col-form-label f-w-600">Autorización del Fabricante:</label></td>
                                                        <td><label class="col-form-label f-w-600">Anticipo financiero, porcentaje, monto:</label></td>
                                                        <td colspan="3"><label class="col-form-label f-w-600">Especificaciones Técnicas detalladas del bien o servicio a ser adquirido,
                                                        en caso de obras anexar el programa de entrega, en caso de combustibles describir el valor en cupos y tarjetas:</label></td>
                                                        <td><label class="col-form-label f-w-600">Solicitud de muestras:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $order->manufacturer_authorization ? "SÍ" : "NO" }}</td>
                                                        <td>{{ $order->financial_advance_percentage_amount ? "SÍ" : "NO" }}</td>
                                                        <td colspan="3">{{ $order->technical_specifications }}</td>
                                                        <td>{{ $order->samples ? "SÍ" : "NO" }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><label class="col-form-label f-w-600">Plan de entregas:</label></td>
                                                        <td colspan="3"><label class="col-form-label f-w-600">Propuesta de representantes de miembros del Comité de Evaluación:</label></td>
                                                        <td colspan="2"><label class="col-form-label f-w-600">Garantía del Llamado:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $order->delivery_plan }}</td>
                                                        <td colspan="3">{{ $order->evaluation_committee_proposal }}</td>
                                                        <td colspan="2">{{ $order->contract_guarantee }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6"><label class="col-form-label f-w-600">Condiciones de Pago:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6">{{ $order->payment_conditions }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6"><label class="col-form-label f-w-600">Garantía del Bien o Servicio:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="6">{{ $order->product_guarantee }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><label class="col-form-label f-w-600">Administrador del Contrato:</label></td>
                                                        <td><label class="col-form-label f-w-600">Vigencia del Contrato:</label></td>
                                                        <td colspan="2"><label class="col-form-label f-w-600">Documentos adicionales que deberá presentar el oferente que
                                                            demuestran que los bienes ofertados cumplen con las especificaciones técnicas:</label></td>
                                                        <td colspan="2"><label class="col-form-label f-w-600">Documentos adicionales que deberá presentar el oferente que demuestran
                                                            que el oferente se halla calificado para ejecutar el contrato:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $order->contract_administrator }}</td>
                                                        <td>{{ $order->contract_validity }}</td>
                                                        <td colspan="2">{{ $order->additional_technical_documents }}</td>
                                                        <td colspan="2">{{ $order->additional_qualified_documents }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"><label class="col-form-label f-w-600">Planilla de Precios (Anexo 1):</label></td>
                                                        <td colspan="2"><label class="col-form-label f-w-600">Título de propiedad, planos aprobados por la municipalidad, licencia ambiental:</label></td>
                                                        <td><label class="col-form-label f-w-600">Medio Magnético:</label></td>
                                                        <td><label class="col-form-label f-w-600">Datos de la persona referente:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">{{ $order->price_sheet }}</td>
                                                        <td colspan="2">{{ $order->property_title }}</td>
                                                        <td>{{ $order->magnetic_medium }}</td>
                                                        <td>{{ $order->referring_person_data }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <h5 class="text-center">Datos Análisis de Precio Referencial</h5>
                                            <table class="table table-striped table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td><label class="col-form-label f-w-600">Lugar y fecha:</label></td>
                                                        <td><label class="col-form-label f-w-600">Resolución DNCP Nº:</label></td>
                                                        <td><label class="col-form-label f-w-600">Fecha Resolución DNCP:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $order->form4_city.', '.$order->form4DateFormat() }}</td>
                                                        <td>{{ $order->dncp_resolution_number }}</td>
                                                        <td>{{ $order->dncpResolutionDateFormat() }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- PARA EMPRESAS ADJUDICADAS --}}
                                        <div class="tab-pane" id="tab2" role="tabpanel">
                                            {{-- @php
                                                var_dump($order->budgetRequestProviders->where('request_provider_type', 3)->count());
                                            @endphp --}}

                                            <table id="budget_request_providers" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        {{-- <th style="font-weight: bold; color:red">#</th>                                                        --}}
                                                        <th style="font-weight: bold; color:red">Empresa participante</th>
                                                        <th style="font-weight: bold; color:red">RUC</th>
                                                        <th style="font-weight: bold; color:red">Teléfono</th>
                                                        <th style="font-weight: bold; color:red">Email para Ofertas</th>
                                                        <th style="font-weight: bold; color:red">Email para Ord. Compras</th>
                                                        <th style="font-weight: bold; color:red">Representante</th>
                                                        <th style="font-weight: bold; color:red">Monto adjudicado</th>
                                                        <th style="font-weight: bold; color:red">Contrato N°</th>
                                                        <th style="font-weight: bold; color:red">Fecha Contrato</th>
                                                        <th style="font-weight: bold; color:red">Monto Contrato</th>


                                                        <th style="font-weight: bold; color:red">Acción</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                @for ($i = 0; $i < count($order->budgetRequestProviders);($i++))
                                                    <tr>
                                                        {{-- Muestra las empresas invitadas (request_provider_type=3) --}}
                                                        @if ($order->budgetRequestProviders[$i]->request_provider_type==4)
                                                            {{-- <td>{{ ($i+1) }}</td> --}}
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->description }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->ruc }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->telefono }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->email_oferta }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->email_ocompra }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->representante }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->montoAdjudicaFormat()}}</td>

                                                            @if ($order->budgetRequestProviders[$i]->contract_number == 0)
                                                                <td>{{ $order->budgetRequestProviders[$i]->contractNumberFormat()}}</td>
                                                            @else
                                                                <td>{{ $order->budgetRequestProviders[$i]->contractNumberFormat()}}/{{ $order->budgetRequestProviders[$i]->contractYearFormat() }}</td>
                                                            @endif

                                                            <td>{{ $order->budgetRequestProviders[$i]->contractDateFormat() }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->montoContractFormat()}}</td>
                                                            <td style="white-space:nowrap">
                                                                @if (Auth::user()->hasPermission(['admin.items_budget.update']) || $order->dependency_id == Auth::user()->dependency_id)
                                                                    {{-- <a href="{{ route('orders.budget_request_providers.edit_providers_contracts', $order->id, $order->budgetRequestProviders[$i]->id)}}" title="Edit" class="btn btn-primary btn-icon">  --}}
                                                                    {{-- VERIFICAMOS QUE TENGA CDP PARA QUE SE PUEDA CARGAR DATOS DE CONTRATOS --}}
                                                                    @if ($order->cdp_number > 0)
                                                                        <button type="button" title="Cargar Datos de Contratos" class="btn btn-warning btn-icon" onclick="updateContracts({{ $order->budgetRequestProviders[$i]->id }})">
                                                                    @endif
                                                                @endif
                                                                <i class="fa fa-pencil text-white"></i>

                                                                {{-- @if (Auth::user()->hasPermission(['admin.items_budget.delete']) || $order->dependency_id == Auth::user()->dependency_id)
                                                                    <button type="button" title="Borrar empresa" class="btn btn-danger btn-icon" onclick="deleteBudget({{ $order->budgetRequestProviders[$i]->id }})">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                @endif --}}
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endfor
                                                </tbody>
                                            </table>
                                            <br>
                                            {{-- MOSTRAMOS COMPARATIVO ENTRE MONTO ITEMS Y MONTO ITEMS ADJUDICADO --}}
                                            @if ($order->total_amount == $order->total_amount_award || $order->total_amount_award == 0 )
                                                <span style="font-size: 16px; font-weight: bold; color:blue" >MONTO TOTAL DE LA ADJUDICACIÓN: {{ $order->totalAmountAwardFormat() }}</span>
                                            @else
                                                <span style="font-size: 16px; font-weight: bold; color:red;background-color:yellow;" >MONTO TOTAL DE LA ADJUDICACIÓN: {{ $order->totalAmountAwardFormat() }} DISTINTO A MONTO TOTAL DE ITEMS</span>
                                            @endif
                                            <br><br>
                                            {{-- MOSTRAMOS SI TIENE CDP --}}
                                            @if ($order->cdp_number > 0)
                                                <span style="color:blue;font-weight: bold;background-color:yellow">CDP Nº: {{ $order->cdpNumberFormat()}} de fecha: {{ $order->cdpDateFormat()}}</span>
                                            @else
                                                <span style="color:red;font-weight: bold;background-color:yellow">OBS: LLAMADO NO CUENTA CON CDP</span>
                                            @endif
                                        </div>

                                        {{-- MUESTRA ITEMS DE ADJUDICACIONES --}}
                                        <div class="tab-pane" id="tab3" role="tabpanel">
                                            <div class="row">
                                                <table id="items" class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                                {{-- <th style="font-weight: bold; color:red">#</th> --}}
                                                                <th style="font-weight: bold; color:red">Lote</th>
                                                                <th style="font-weight: bold; color:red">Ítem</th>
                                                                <th style="font-weight: bold; color:red">Cód. de Catál.</th>
                                                                <th style="font-weight: bold; color:red">Descripción</th>
                                                                <th style="font-weight: bold; color:red">Present.</th>
                                                                <th style="font-weight: bold; color:red">U.M.</th>

                                                                <th style="font-weight: bold; color:red">Marca</th>
                                                                <th style="font-weight: bold; color:red">Procedencia</th>
                                                                <th style="font-weight: bold; color:red">Fabricante</th>
                                                                <th style="font-weight: bold; color:red">Precio Unitario</th>
                                                                <th style="font-weight: bold; color:red">Cantidad Adjudicada</th>
                                                                <th style="font-weight: bold; color:red">Monto Total</th>
                                                                <th style="font-weight: bold; color:red">ID_Empresa.</th>
                                                                <th style="font-weight: bold; color:red">Empresa</th>
                                                                {{-- <th style="font-weight: bold; color:red">Acciones</th>                                                                  --}}
                                                            </tr>
                                                    </thead>
                                                        <tbody>

                                                        {{-- MUESTRA DATOS DE LA TABLA ITEM ADJUDICACIONES ORDER->ITEMSADJU --}}
                                                        @for ($i = 0; $i < count($order->itemAwards); $i++)
                                                            <tr>
                                                                {{-- <td>{{ ($i+1) }}</td> --}}
                                                                <td>{{ $order->itemAwards[$i]->batch }}</td>
                                                                <td>{{ $order->itemAwards[$i]->item_number }}</td>
                                                                @if ($order->itemAwards[$i]->level5CatalogCode->code == '99999999-9999')
                                                                    <td class="columna3" style="color:red;font-weight: bold">{{ $order->itemAwards[$i]->level5CatalogCode->code }}</td>
                                                                    <td style="color:red;font-weight: bold">{{ $order->itemAwards[$i]->level5CatalogCode->description }}</td>
                                                                @else
                                                                    <td class="columna3"> {{ $order->itemAwards[$i]->level5CatalogCode->code }}</td>
                                                                    <td>{{ $order->itemAwards[$i]->level5CatalogCode->description }}</td>
                                                                @endif
                                                                <td>{{ $order->itemAwards[$i]->orderPresentation->description }}</td>
                                                                <td>{{ $order->itemAwards[$i]->orderMeasurementUnit->description }}</td>
                                                                <td>{{ $order->itemAwards[$i]->trademark }}</td>
                                                                <td>{{ $order->itemAwards[$i]->origin }}</td>
                                                                <td>{{ $order->itemAwards[$i]->maker }}</td>
                                                                <td style="text-align: center">{{ 'Gs. '.$order->itemAwards[$i]->unitPriceFormat() }}</td>
                                                                <td style="text-align: center">{{ $order->itemAwards[$i]->quantityFormat() }}</td>
                                                                <td class="columna2">{{ 'Gs. '.$order->itemAwards[$i]->totalAmountFormat() }}</td>
                                                                <td>{{ $order->itemAwards[$i]->provider_id }}</td>
                                                                <td>{{ $order->itemAwards[$i]->provider->description }}</td>

                                                                {{-- Mostramos ítemes de Contrato Abierto --}}
                                                                {{-- @if ($order->open_contract == 1)
                                                                    <td class="columna12" style="text-align: center">{{ 'Gs. '.$order->itemAwards[$i]->unitPriceFormat() }}</td>
                                                                    <td class="columna9"style="text-align: center">{{ $order->itemAwards[$i]->min_quantityFormat() }}</td>
                                                                    <td class="columna10"style="text-align: center">{{ $order->itemAwards[$i]->max_quantityFormat() }}</td>
                                                                    <td class="columna11"style="text-align: center">{{ 'Gs. '.$order->itemAwards[$i]->totalAmount_min_Format() }}</td>
                                                                    <td class="columna12">{{ 'Gs. '.$order->itemAwards[$i]->totalAmountFormat() }}</td>
                                                                @else --}}
                                                                    {{-- Mostramos ítemes de Contrato Cerrado --}}
                                                                    {{-- @if ($order->open_contract == 2)
                                                                        <td class="columna8"style="text-align: center">{{ $order->itemAwards[$i]->quantityFormat() }}</td>
                                                                        <td class="columna12" style="text-align: center">{{ 'Gs. '.$order->itemAwards[$i]->unitPriceFormat() }}</td>
                                                                        <td class="columna12">{{ 'Gs. '.$order->itemAwards[$i]->totalAmountFormat() }}</td>
                                                                    @else --}}
                                                                        {{-- Mostramos ítemes de Contrato Abierto con Mmin y Mmax --}}
                                                                        {{-- <td class="columna10"style="text-align: center">{{ $order->itemAwards[$i]->quantityFormat() }}</td>
                                                                        <td class="columna12" style="text-align: center">{{ 'Gs. '.$order->itemAwards[$i]->unitPriceFormat() }}</td>
                                                                        <td class="columna11"style="text-align: center">{{ 'Gs. '.$order->itemAwards[$i]->totalAmount_min_Format() }}</td>
                                                                        <td class="columna12">{{ 'Gs. '.$order->itemAwards[$i]->totalAmountFormat() }}</td>
                                                                    @endif
                                                                @endif --}}

                                                                {{-- ESO DEBE MOSTRAR DE ACUERDO AL TIPO DE CONTRATO --}}
                                                                {{-- <td>{{ $order->itemAwards[$i]->unit_price }}</td>
                                                                <td>{{ $order->itemAwards[$i]->max_quantity }}</td>
                                                                <td>{{ $order->itemAwards[$i]->total_amount }}</td>  --}}
                                                                {{-- *********************************************** --}}

                                                                <td style="white-space:nowrap">

                                                                {{-- @if (Auth::user()->hasPermission(['admin.items_adjudica.update']) || $order->dependency_id == Auth::user()->dependency_id)
                                                                    <button type="button" title="Editar Item" class="btn btn-warning btn-icon" onclick="updateItem({{ $order->itemAwards[$i]->id }})">
                                                                        <i class="fa fa-pencil"></i>
                                                                    </button>
                                                                @endif
                                                                @if (Auth::user()->hasPermission(['admin.items_adjudica.delete']) || $order->dependency_id == Auth::user()->dependency_id)
                                                                    <button type="button" title="Borrar" class="btn btn-danger btn-icon" onclick="deleteItemAw({{ $order->itemAwards[$i]->id }})">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                @endif --}}
                                                            </tr>
                                                        @endfor
                                                        </tbody>
                                                    </table>
                                            </div>
                                            <div class="text-right">
                                                {{-- @if (Auth::user()->hasPermission(['admin.items.create']) || $order->dependency_id == Auth::user()->dependency_id) --}}
                                                    {{-- Si pedido está anulado no muestra agregar ítems --}}
                                                    {{-- @if ($order->actual_state == 0)
                                                    @else
                                                        <a href="{{ route('orders.items_adjudica.create', $order->id) }}" class="btn btn-primary">Agregar ítem</a>
                                                        <a href="{{ route('orders.items_adjudica.uploadExcelAw', $order->id)}}" title="Cargar Archivo EXCEL" class="btn btn-success btn-icon">
                                                    @endif
                                                        <i class="fa fa-upload text-white"></i>
                                                    </a> --}}
                                                {{-- @endif --}}

                                            </div>

                                            {{-- MOSTRAMOS COMPARATIVO ENTRE MONTO ITEMS Y MONTO ITEMS ADJUDICADO --}}
                                            @if ($order->total_amount == $order->total_amount_award || $order->total_amount_award == 0 )
                                                <span style="font-size: 16px; font-weight: bold; color:blue" >MONTO TOTAL DE LA ADJUDICACIÓN: {{ $order->totalAmountAwardFormat() }}</span>
                                            @else
                                                <span style="font-size: 16px; font-weight: bold; color:red;background-color:yellow;" >MONTO TOTAL DE LA ADJUDICACIÓN: {{ $order->totalAmountAwardFormat() }} DISTINTO A MONTO TOTAL DE ITEMS</span>
                                            @endif

                                            @if (Auth::user()->hasPermission(['admin.items_adjudica.update']))
                                                <div class="float-center">
                                                    <h5  style="color:blue">Archivo Excel para Descargar y realizar importación de items</h5>
                                                </div>
                                                <a href="/excel/itemsAw" title="Descargar Archivo Excel" class="btn btn-danger" target="_blank">Archivo</a>
                                            @endif
                                        </div>
                                        <br>
                                        {{-- <a href="/items/export-excel/{{ $order->id }} " class="btn btn-mm btn-danger float-left">Listado en Excel</a> --}}

                                        <div class="tab-pane" id="tab4" role="tabpanel">
                                            <table id="forms" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Formulario</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Formulario 1</td>
                                                        <td><a href="/pdf/reporte1/{{ $order->id }}" class="btn btn-default" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Ver Formulario 1</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Formulario 2</td>
                                                        <td><a href="/pdf/reporte2/{{ $order->id }}" class="btn btn-default" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Ver Formulario 2</a></td>
                                                    </tr>
                                                    {{-- Verificamos que el pedido tenga ítems cargados --}}
                                                    @if ($order->items->count() > 0)
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Formulario 3</td>
                                                        <td><a href="/pdf/reporte3/{{ $order->id }}" class="btn btn-default" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Ver Formulario 3</a></td>
                                                    </tr>
                                                    @endif
                                                    {{-- Verificamos que el pedido tenga ítems y solicitudes de presupuesto cargados --}}
                                                    @if ($order->items->count() > 0 && $order->budgetRequestProviders->count() > 0)
                                                    <tr>
                                                        <td>4</td>
                                                        <td>Formulario 4</td>
                                                        <td><a href="/pdf/reporte4/{{ $order->id }}" class="btn btn-default" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Ver Formulario 4</a></td>
                                                    </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="tab-pane" id="tab5" role="tabpanel">
                                            <label class="col-form-label f-w-600">Documentos SIMESE relacionados al llamado:</label>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        {{-- <th>Año</th> --}}
                                                        <th>Nro.SIMESE/Año</th>
                                                        <th>Dependencia</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i=0; $i < count($related_simese); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        {{-- <td>{{ $related_simese[$i]->year }}</td> --}}
                                                        <td>{{ number_format($related_simese[$i]->simese,'0', ',','.') }}-{{ $related_simese[$i]->year }}</td>
                                                        <td>{{ $related_simese[$i]->dependency->description }}</td>
                                                    </tr>
                                                    @endfor
                                                    @for ($i=0; $i < count($related_simese_contract); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        {{-- <td>{{ $related_simese_contract[$i]->year }}</td> --}}
                                                        <td>{{ number_format($related_simese_contract[$i]->simese,'0', ',','.') }}-{{ $related_simese_contract[$i]->year }}</td>
                                                        <td>{{ $related_simese_contract[$i]->dependency->description }}</td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                {{-- Caso en que planificacion no haya cargado ningun documento simese --}}
                                                @if(count($related_simese_contract) == 0)
                                                <a href="{{ route('orders.simese.create', $order->id) }}" class="btn btn-primary">Cargar N° SIMESE</a>
                                                @else
                                                {{-- Caso planificacion ya cargo documentos simese --}}
                                                <a href="{{ route('orders.simese.edit', $order->id) }}" class="btn btn-warning">Editar Documentos</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab6" role="tabpanel">
                                            <label class="col-form-label f-w-600">Archivos cargados al llamado:</label>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Descripción</th>
                                                        <th>Dependencia</th>
                                                        <th>Fecha/Hora</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i=0; $i < count($other_files); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $other_files[$i]->description }}</td>
                                                        <td>{{ $other_files[$i]->dependency->description }}</td>
                                                        <td>{{ $other_files[$i]->updated_atDateFormat() }}</td>
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$other_files[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('orders.files.download', $other_files[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                    {{-- @for ($i=0; $i < count($contract_files); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $contract_files[$i]->description }}</td>
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$contract_files[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('orders.files.download', $contract_files[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                            <button title="Eliminar Archivo" onclick="deleteFile({{ $contract_files[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endfor --}}
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                {{-- <a href="{{ route('orders.files.create', $order->id) }}" class="btn btn-primary">Cargar Archivos</a>                                                                                                --}}
                                            </div>
                                            <div class="col-sm-10 text-left">
                                                {{-- <h6>Adjuntar: Resolución/Nota a DNCP/Acta de Apertura/Notificaciones a las empresas/Cargar items adjudicados con precio adjudicado.</h6>                                                                                                                                                     --}}
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab7" role="tabpanel">
                                            <label class="col-form-label f-w-600">Archivos de Contratos y Garantías:</label>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Descripción</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i=0; $i < count($contract_files); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $contract_files[$i]->description }}</td>
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$contract_files[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('orders.files.download', $contract_files[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                            <button title="Eliminar Archivo" onclick="deleteFile({{ $contract_files[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                <a href="{{ route('orders.files.create', $order->id) }}" class="btn btn-primary">Cargar Contratos</a>
                                            </div>
                                            <div class="col-sm-10 text-left">
                                                <h6>Adjuntar: Contratos</h6>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab8" role="tabpanel">
                                            {{-- <label class="col-form-label f-w-600">Reparos obtenidos en el portal de la DNCP:</label>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Descripción</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                            @for ($i=0; $i < count($objections); $i++)
                                            <table class="table table-striped table-bordered">
                                                <tbody>
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $objections[$i]->objection }}</td>
                                                        <td style="white-space: nowrap">
                                                            <a href="{{ route('contracts.objections.edit', [$order->id, $objections[$i]->id]) }}" class="btn btn-warning"><i class="fa fa-edit"></i> Modificar</a>
                                                            <a data-toggle="collapse" data-parent="#accordion" href="#objectionResponses{{ $objections[$i]->id }}" aria-expanded="true" aria-controls="collapseOne" class="btn btn-info"><i class="fa fa-eye"></i> Ver Respuestas</a>
                                                            <button title="Eliminar Reparo" onclick="deleteObjection({{ $objections[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</a>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div id="objectionResponses{{ $objections[$i]->id }}" class="panel-collapse in collapse" role="tabpanel">
                                                <h6>Respuestas a reparo {{ $objections[$i]->id }}</h6>
                                                <table class="table m-b-0">
                                                    @foreach ($objections[$i]->objectionResponses as $obj_response)
                                                    <tr>
                                                        <td>{{ $obj_response->response }}</td>
                                                        <td>{{ $obj_response->updated_atDateFormat() }}</td>
                                                        <td><a href="{{ route('contracts.objections_responses.edit', [$objections[$i]->id, $obj_response->id]) }}" class="btn btn-warning"><i class="fa fa-edit"></i> Modificar</a></td>
                                                        <td><button title="Eliminar Respuesta a Reparo" onclick="deleteObjectionResponse({{ $objections[$i]->id.','.$obj_response->id }})" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</a></td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                                <div class="m-b-20">
                                                    <a href="{{ route('contracts.objections_responses.create', $objections[$i]->id) }}" class="btn btn-primary">Cargar Respuesta</a>
                                                </div>
                                            </div>
                                            @endfor
                                            <div class="text-right m-t-20">
                                                <a href="{{ route('contracts.objections.create', $order->id) }}" class="btn btn-primary">Cargar Reparo</a>
                                            </div> --}}
                                            {{-- *** Para cargar archivos relacionados a Reparos ** --}}
                                            <label class="col-form-label f-w-600">Archivos relacionados a Addendas:</label>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Descripción</th>
                                                        <th>Tipo de Archivo</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i=0; $i < count($contract_filedncp); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $contract_filedncp[$i]->description }}</td>
                                                        {{-- <td>{{ $contract_filesdncp[$i]->file_type }}</td>                                                         --}}
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$contract_filedncp[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('orders.files.download', $contract_filedncp[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                            <button title="Eliminar Archivo" onclick="deleteFile({{ $contract_filedncp[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                <a href="{{ route('orders.files.create_filedncp', $order->id) }}" class="btn btn-primary">Cargar Addendas</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MOSTRAMOS TIMELINE --}}
                            <div class="card latest-update-card">
                                <div class="card-header">
                                    <h5 style="font-size: 14px; font-weight: bold; color:red" class="text-left">Movimientos del pedido</h5>
                                </div>
                                <div class="card-block">
                                    <div class="latest-update-box">
                                    @foreach ($order->ordersOrderStates()->orderBy('id', 'desc')->get() as $item)
                                        <div class="row p-t-20 p-b-30 borde-alternado">
                                            <div class="col-auto text-right update-meta p-r-0">
                                                <i class="update-icon ring"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="javascript:void(0);">
                                                    <h6 style="font-size: 14px; font-weight: bold; color:Black">{{ $item->orderState->id}}-{{ $item->orderState->description}}</h6>
                                                </a>
                                                {{-- <p class="text-muted m-b-0">{{ $item->creatorUser->getFullName()}}</p> --}}
                                                <p style="font-size: 14px; font-weight: bold; color:Black">{{ $item->creatorUser->getFullName()}}</p>
                                                <small style="font-size: 13px; font-weight: bold; color:Black">Fecha: {{ $item->createdAtDateFormat()}}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$(document).ready(function(){

    updateContracts = function(budget){
        // persistirTab();
        location.href = '/orders/{{ $order->id }}/items_budget/'+budget+'/edit/';

        // location.href = '/orders/{{ $order->id }}/budget_request_providers/'+budget+'/edit_providers_contracts/';

        //  /items_budget = Route::resource('orders.items_budget', BudgetRequestProvidersController::class);
        //  /edit = public function edit(Request $request, $order_id) de BudgetRequestProvidersController
    }

    recibeOrder = function(order_id){
        $.ajax({
            url : '/contracts/recibe_order/'+order_id,
            method: 'POST',
            data: '_token='+'{{ csrf_token() }}',
            success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        swal({
                            title: "Exito!",
                            text: response.message,
                            type: "success"
                        },
                        function(isConfirm){
                            location.reload();
                        });
                    }else{
                        swal("Error!", response.message, "error");
                    }
                }catch(error){
                    swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                    console.log(error);
                }
              },
              error: function(error){
                swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                console.log(error);
              }
        });
    }

    deriveOrder = function(order_id){
        $.ajax({
            url : '/contracts/derive_order/'+order_id,
            method: 'POST',
            data: '_token='+'{{ csrf_token() }}',
            success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        swal({
                            title: "Exito!",
                            text: response.message,
                            type: "success"
                        },
                        function(isConfirm){
                            location.reload();
                        });
                    }else{
                        swal("Error!", response.message, "error");
                    }
                }catch(error){
                    swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                    console.log(error);
                }
              },
              error: function(error){
                swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                console.log(error);
              }
        });
    }

    itemAwardHistories = function(item){
        location.href = '/items/'+item+'/item_contract_histories';
    }

    deleteFile = function(file){
      swal({
            title: "Atención",
            text: "Está seguro que desea eliminar el registro?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        },
        function(isConfirm){
          if(isConfirm){
            $.ajax({
              url : '/orders/files/'+file+'/delete/',
              method : 'POST',
              data: {_method: 'DELETE', _token: '{{ csrf_token() }}'},
              success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        location.reload();
                    }else{
                        swal("Error!", response.message, "error");
                    }
                }catch(error){
                    swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                    console.log(error);
                }
              },
              error: function(error){
                swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                console.log(error);
              }
            });
          }
        }
      );
    };

    deleteObjection = function(objection){
      swal({
            title: "Atención",
            text: "Está seguro que desea eliminar el registro?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        },
        function(isConfirm){
          if(isConfirm){
            $.ajax({
              url : '/contracts/{{ $order->id }}/objections/'+objection,
              method : 'POST',
              data: {_method: 'DELETE', _token: '{{ csrf_token() }}'},
              success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        location.reload();
                    }else{
                        swal("Error!", response.message, "error");
                    }
                }catch(error){
                    swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                    console.log(error);
                }
              },
              error: function(error){
                swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                console.log(error);
              }
            });
          }
        }
      );
    };

    deleteObjectionResponse = function(objection, objection_response){
      swal({
            title: "Atención",
            text: "Está seguro que desea eliminar el registro?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        },
        function(isConfirm){
          if(isConfirm){
            $.ajax({
              url : '/contracts/'+objection+'/objections_responses/'+objection_response,
              method : 'POST',
              data: {_method: 'DELETE', _token: '{{ csrf_token() }}'},
              success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        location.reload();
                    }else{
                        swal("Error!", response.message, "error");
                    }
                }catch(error){
                    swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                    console.log(error);
                }
              },
              error: function(error){
                swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                console.log(error);
              }
            });
          }
        }
      );
    };

});
</script>
@endpush
