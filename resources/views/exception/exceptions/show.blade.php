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
                        {{-- <h6>Estado Actual: {{ $order->orderState->id." - ".$order->orderState->description }}</h6>    --}}
                        <h5><p style="font-size: 17px; font-weight: bold; color:#FF0000">Estado Actual: {{ $order->orderState->id." - ".$order->orderState->description }}</p></h5> 
                        {{-- <a href="/pdf/modalities" class="btn btn-info" target="_blank">Plazos de Procesos</a>                         --}}
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
                            <a href="{{ route('exceptions.index') }}">Llamados</a>
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
                                            @endif --}}

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
                                            
                                            {{-- Si fecha de apertura está vacía --}}
                                            @if (is_null($order->queries_deadline))
                                                
                                            @else
                                                <label style="font-size: 17px; font-weight: bold; color:red;background-color:yellow;">Fecha de Apertura: {{ $order->queriesDeadline() }}</label></h5>
                                            @endif
                                        </div>
                                        <div class="col-sm-2">
                                            {{-- @if (in_array($order->actual_state, [20,55])) --}}
                                                @if ($order->actual_state == 20)
                                                    <button class="btn btn-danger dropdown-toggle waves-effect" type="button" id="acciones" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Recibir Llamado</button>
                                                    <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="recibeOrder({{ $order->id }});">Recibir Proceso Llamado</a>
                                                @else
                                                    @if ($order->actual_state == 97)
                                                        <button class="btn btn-danger dropdown-toggle waves-effect" type="button" id="acciones" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Recibir Reparo</button>                                                
                                                        <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="recibeOrderUTA({{ $order->id }});">Recibir PBC de UTA-CON REPARO</a>                                               
                                                    @else
                                                        @if ($order->actual_state == 62)                                                        
                                                            <button class="btn btn-danger dropdown-toggle waves-effect" type="button" id="acciones" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Recibir de PAC con Obs.Solucionado</button>
                                                            <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="recibeOrder({{ $order->id }});">Recibir PAC con Obs.Solucionado</a>
                                                        @else
                                                            <button class="btn btn-primary dropdown-toggle waves-effect" type="button" id="acciones" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones</button>
                                                        @endif
                                                    @endif
                                                @endif                                                                                              
                                            
                                            <div class="dropdown-menu" aria-labelledby="acciones" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                
                                                {{-- Verificamos que el pedido tenga estado 20-PROCESADO PLANIFICACIÓN --}}
                                                {{-- @if ($order->actual_state == 20)                                                
                                                    <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="recibeOrder({{ $order->id }});">Recibir Proceso Llamado</a>
                                                @endif                                                 --}}
                                                
                                                {{-- @if (in_array($order->actual_state, [55,101])&&  (!empty($order->queries_deadline))) --}}
                                                @if (in_array($order->actual_state, [55]))                                                    
                                                    <a style="font-size: 14px; font-weight: bold; color:white;background-color:red;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="derivePAC({{ $order->id }});">Derivar a PAC con Observaciones</a>
                                                    <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="deriveOrder({{ $order->id }});">Derivar a UTA para Control de PBC</a>
                                                @endif

                                                @if (in_array($order->actual_state, [101]))                                                    
                                                    <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="deriveOrder({{ $order->id }});">Derivar a UTA para Control de PBC</a>
                                                @endif
                                            
                                                {{-- Verificamos que el pedido tenga estado VERIFICADO PBC EN UTA CON REPARO --}}
                                                

                                                {{-- @if ($order->actual_state == 97)
                                                <a class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="recibeOrderUTA({{ $order->id }});">Recibir PBC de UTA-CON REPARO</a>
                                                @endif                                                 --}}

                                                {{-- Verificamos que el pedido tenga estado 130-DERIVADO DE DOC PARA PROCESAR PEDIDO --}}
                                                @if ($order->actual_state == 130 && (!empty($order->queries_deadline)))
                                                    <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="deriveComite({{ $order->id }});">Derivar Antecedentes a Comité de Evaluación</a>
                                                @endif

                                                @if ($order->actual_state == 130 && (is_null($order->queries_deadline)))                                                
                                                    <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" data-toggle="modal" data-target="#fechaAperturaSobresModal">Agregar Fecha Apertura de Sobres</a>                                                
                                                @endif

                                                {{-- @if ($order->actual_state == 130 && (!empty($order->queries_deadline))) --}}
                                                @if ($order->actual_state == 130 && (!empty($order->queries_deadline)))
                                                    <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" data-toggle="modal" data-target="#fechaAperturaSobresModal">Modificar Fecha de Apertura de Sobres</a>
                                                @endif
                                        </div>
                                        {{-- @endif --}}
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
                                        {{-- <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab2" role="tab"><i class="fa fa-briefcase"></i> Empresas solicitadas presupuesto</a>
                                            <div class="slide"></div>
                                        </li> --}}
                                                                                
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab3" role="tab"><i class="fa fa-list"></i> Ítems</a>
                                            <div class="slide"></div>
                                        </li>                                        
                                        {{-- <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab4" role="tab"><i class="fa fa-file-pdf-o"></i> Formularios</a>
                                            <div class="slide"></div>
                                        </li> --}}
                                        {{-- NO SE UTILIZARÁ AGREGAR SIMESE --}}
                                        {{-- <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab5" role="tab"><i class="fa fa-file-text-o"></i> SIMESE Relacionado</a>
                                            <div class="slide"></div>
                                        </li> --}}
                                        
                                        {{-- //Se controla que tenga estado recibido en Compras Excepciones para mostrar estos TABS --}}
                                        {{-- @if (($order->actual_state <> 20) && ($order->actual_state <> 97)) --}}
                                        @if ($order->actual_state <> 20)
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tab10" role="tab"><i class="fa fa-briefcase"></i>Empresas invitadas</a>
                                                <div class="slide"></div>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tab11" role="tab"><i class="fa fa-group"></i>Empresas participantes</a>
                                                <div class="slide"></div>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tab12" role="tab"><i class="fa fa-bar-chart"></i>Cuadro Comparativo</a>
                                                <div class="slide"></div>
                                            </li>                                            
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tab6" role="tab"><i class="fa fa-file-archive-o"></i> Archivos (Anteced.)</a>
                                                <div class="slide"></div>
                                            </li>
                                            <li class="nav-item">                                                
                                                <a class="nav-link" data-toggle="tab" href="#tab7" role="tab"><i class="fa fa-folder-open-o"></i> Archivos Excepciones</a>
                                                <div class="slide"></div>
                                            </li>
                                            {{-- @if ($order->actual_state == 130 ))                                              --}}
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#tab8" role="tab"><i class="fa fa-building-o"></i> DNCP-Consultas</a>
                                                    <div class="slide"></div>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" data-toggle="tab" href="#tab9" role="tab"><i class="fa fa-building-o"></i> DNCP-Reparos</a>
                                                    <div class="slide"></div>
                                                </li>
                                            {{-- @endif --}}
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
                                                        <td>{{ $order->modality->description }}</td>
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
                                        
                                        <div class="tab-pane" id="tab2" role="tabpanel">
                                            <table id="budget_request_providers" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Empresa</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @for ($i = 0; $i < count($order->budgetRequestProviders); $i++)
                                                    <tr>
                                                        <td>{{ ($i+1) }}</td>
                                                        <td>{{ $order->budgetRequestProviders[$i]->provider->description }}</td>
                                                    </tr>
                                                @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                            {{-- En caso de no tener solicitudes de presupuesto --}}
                                            @if($order->providers->count() == 0)
                                                @if (Auth::user()->hasPermission(['exceptions.budget_request_providers.create']))
                                                    <a href="{{ route('orders.budget_request_providers.create', $order->id) }}" class="btn btn-primary">Agregar</a>
                                                @endif
                                            @else
                                                @if (Auth::user()->hasPermission(['exceptions.budget_request_providers.update']))
                                                    <a href="{{ route('orders.budget_request_providers.editar', $order->id) }}" class="btn btn-warning">Editar</a>
                                                @endif
                                            @endif
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab3" role="tabpanel">
                                            <div class="row">
                                                <table id="items" class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Lote</th>
                                                            <th>Ítem</th>
                                                            <th>Cód. de Catál.</th>
                                                            <th>Descripción</th>
                                                            <th>EETT</th>
                                                            <th>Present.</th>
                                                            <th>U.M.</th>
                                                            {{-- Mostramos ítemes de Contrato Abierto --}}
                                                            @if ($order->open_contract == 1) 
                                                                <th>Precio Unitario</th> 
                                                                <th>Pedido Mínimo</th>
                                                                <th>Pedido Máximo</th>
                                                                <th>Monto Mínimo</th>
                                                                <th>Monto Máximo</th>
                                                                {{-- <th>Acciones</th> --}}
                                                            @else
                                                                {{-- Mostramos ítemes de Contrato Cerrado --}}
                                                                @if ($order->open_contract == 2) 
                                                                    <th>Cantidad</th>
                                                                    <th>Precio Unitario</th>                                                                
                                                                    <th>Monto Total</th>
                                                                    {{-- <th>Acciones</th> --}}
                                                                @else
                                                                {{-- Mostramos ítemes de Contrato Abierto con Mmin y Mmax --}}
                                                                    <th>Cantidad</th>
                                                                    <th>Precio Unitario IVA INCL.</th>                                                                
                                                                    <th>Monto Mínimo</th>
                                                                    <th>Monto Máximo</th>
                                                                    {{-- <th>Acciones</th> --}}
                                                                @endif    
                                                            @endif
                                                            
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @for ($i = 0; $i < count($order->items); $i++)
                                                        <tr>
                                                            <td>{{ ($i+1) }}</td>
                                                            <td>{{ $order->items[$i]->batch }}</td>
                                                            <td>{{ $order->items[$i]->item_number }}</td>                                                                
                                                            @if ($order->items[$i]->level5CatalogCode->code == '99999999-9999')                                                                    
                                                                <td class="columna3" style="color:red;font-weight: bold">{{ $order->items[$i]->level5CatalogCode->code }}</td>
                                                                <td style="color:red;font-weight: bold">{{ $order->items[$i]->level5CatalogCode->description }}</td>
                                                            @else
                                                                <td class="columna3"> {{ $order->items[$i]->level5CatalogCode->code }}</td>
                                                                <td>{{ $order->items[$i]->level5CatalogCode->description }}</td>
                                                            @endif                                                                
                                                            <td>{{ $order->items[$i]->technical_specifications }}</td>
                                                            <td>{{ $order->items[$i]->orderPresentation->description }}</td>
                                                            <td>{{ $order->items[$i]->orderMeasurementUnit->description }}</td>

                                                            {{-- Mostramos ítemes de Contrato Abierto --}}
                                                            @if ($order->open_contract == 1) 
                                                                <td class="columna12" style="text-align: center">{{ 'Gs. '.$order->items[$i]->unitPriceFormat() }}</td>                                                            
                                                                <td class="columna9"style="text-align: center">{{ $order->items[$i]->min_quantityFormat() }}</td>
                                                                <td class="columna10"style="text-align: center">{{ $order->items[$i]->max_quantityFormat() }}</td>
                                                                <td class="columna11"style="text-align: center">{{ 'Gs. '.$order->items[$i]->totalAmount_min_Format() }}</td>
                                                                <td class="columna12">{{ 'Gs. '.$order->items[$i]->totalAmountFormat() }}</td>
                                                            @else
                                                                {{-- Mostramos ítemes de Contrato Cerrado --}}
                                                                @if ($order->open_contract == 2) 
                                                                    <td class="columna8"style="text-align: center">{{ $order->items[$i]->quantityFormat() }}</td> 
                                                                    <td class="columna12" style="text-align: center">{{ 'Gs. '.$order->items[$i]->unitPriceFormat() }}</td>                                                                                                                                               
                                                                    <td class="columna12">{{ 'Gs. '.$order->items[$i]->totalAmountFormat() }}</td>
                                                                @else
                                                                    {{-- Mostramos ítemes de Contrato Abierto con Mmin y Mmax --}}                                                                        
                                                                    <td class="columna10"style="text-align: center">{{ $order->items[$i]->quantityFormat() }}</td>
                                                                    <td class="columna12" style="text-align: center">{{ 'Gs. '.$order->items[$i]->unitPriceFormat() }}</td> 
                                                                    <td class="columna11"style="text-align: center">{{ 'Gs. '.$order->items[$i]->totalAmount_min_Format() }}</td>
                                                                    <td class="columna12">{{ 'Gs. '.$order->items[$i]->totalAmountFormat() }}</td>
                                                                @endif
                                                            @endif
                                                            
                                                            
                                                            <td style="white-space:nowrap">
                                                                {{-- <button type="button" title="Listado de Precios Referenciales" class="btn btn-primary btn-icon" onclick="itemAwardHistories({{ $order->items[$i]->id }})">
                                                                    <i class="fa fa-list"></i>
                                                                </button> --}}
                                                            @if (Auth::user()->hasPermission(['admin.items.update']) || $order->dependency_id == Auth::user()->dependency_id)
                                                                {{-- <button type="button" title="Editar" class="btn btn-warning btn-icon" onclick="updateItem({{ $order->items[$i]->id }})">
                                                                    <i class="fa fa-pencil"></i>
                                                                </button> --}}
                                                            @endif
                                                            @if (Auth::user()->hasPermission(['admin.items.delete']) || $order->dependency_id == Auth::user()->dependency_id)
                                                                {{-- <button type="button" title="Borrar" class="btn btn-danger btn-icon" onclick="deleteItem({{ $order->items[$i]->id }})">
                                                                    <i class="fa fa-trash"></i>
                                                                </button> --}}
                                                            @endif
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="text-right">
                                                @if (Auth::user()->hasPermission(['admin.items.create']) || $order->dependency_id == Auth::user()->dependency_id)
                                                    <a href="{{ route('orders.items.create', $order->id) }}" class="btn btn-primary">Agregar ítem</a>
                                                    <a href="{{ route('orders.items.uploadExcel', $order->id)}}" title="Cargar Archivo EXCEL" class="btn btn-success btn-icon">
                                                    <i class="fa fa-upload text-white"></i>
                                                    </a>
                                                @endif
                                            </div>
                                            <span style="font-size: 16px; font-weight: bold; color:red;background-color:yellow;" >MONTO TOTAL DEL LLAMADO: {{ $order->totalAmountFormat() }}</span>  
                                    </div>
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
                                                        <th>Año</th>
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
                                                    @for ($i=0; $i < count($related_simese_exception); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        {{-- <td>{{ $related_simese_exception[$i]->year }}</td> --}}
                                                        <td>{{ number_format($related_simese_exception[$i]->simese,'0', ',','.') }}-{{ $related_simese_exception[$i]->year }}</td>
                                                        <td>{{ $related_simese_exception[$i]->dependency->description }}</td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                {{-- Caso en que planificacion no haya cargado ningun documento simese --}}
                                                @if(count($related_simese_exception) == 0)
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
                                                        <th>Acciones</th>
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
                                                    {{-- @for ($i=0; $i < count($exception_files); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $exception_files[$i]->description }}</td>                                                        
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$exception_files[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('orders.files.download', $exception_files[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                            <button title="Eliminar Archivo" onclick="deleteFile({{ $exception_files[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endfor --}}
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                {{-- <a href="{{ route('orders.files.create', $order->id) }}" class="btn btn-primary">Cargar Archivos</a>                                                 --}}
                                            </div>
                                            <div class="col-sm-10 text-left">                                               
                                                {{-- <h4>Adjuntar: Pliego de Bases y Condiciones</h4> --}}
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab7" role="tabpanel">
                                            <label class="col-form-label f-w-600">Archivos de Adjudicaciones:</label>
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Descripción</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>                                                   
                                                    @for ($i=0; $i < count($exception_files); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $exception_files[$i]->description }}</td>                                                        
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$exception_files[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('orders.files.download', $exception_files[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                            <button title="Eliminar Archivo" onclick="deleteFile({{ $exception_files[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">                                                
                                                <a href="{{ route('orders.files.create', $order->id) }}" class="btn btn-primary">Cargar Archivos</a>                                                                                                
                                            </div>
                                            <div class="col-sm-10 text-left">
                                                <h6 style="font-weight: bold; color:blue"> Adjuntar:</h6> 
                                                <h6>• Nota dirigida al Director Nacional comunicando el llamado (firmada y escaneada).</h6>
                                                <h6>• Pliego de bases y condiciones del llamado, redactado en base al pliego estándar aprobado por la DNCP.</h6>
                                                <h6>• CDP (escaneado y firmado en concordancia al registro de firmas remitido a la DNCP) o Constancia Ad referéndum (según corresponda).</h6>                                                    
                                                <h6>• Constancia de previsión de plurianualidad (en caso de que el llamado sea ejecutado con cargo a más de un ejercicio fiscal).</h6>
                                                <h6>• Resolución que aprueba el PBC, firmada por la Máxima Autoridad de la Convocante o autorizado, por delegación.</h6>                                                                                                                                                   
                                            </div>
                                        </div>
                                        
                                        <div class="tab-pane" id="tab8" role="tabpanel">
                                            <label class="col-form-label f-w-600">Consultas obtenidas en el portal de la DNCP</label><br>
                                            <label style="font-weight: bold; color:red" class="col-form-label f-w-600">Fecha Apertura de Sobres:</label> <label id="queries_deadline_label" style="font-weight: bold; color:red;background-color:yellow" class="col-form-label">{{ empty($order->queries_deadline) ? "No se ha cargado la fecha Apertura de Sobres." : $order->queriesDeadline() }}</label>                                           
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Fecha</th>
                                                        <th>Descripción</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @for ($i=0; $i < count($queries); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ date('d/m/Y', strtotime($queries[$i]->query_date)) }}</td>
                                                        <td>{{ $queries[$i]->query }}</td>
                                                        <td style="white-space: nowrap">
                                                            <a href="{{ route('exceptions.queries.edit', [$order->id, $queries[$i]->id]) }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Modificar</a>
                                                            <a data-toggle="collapse" data-parent="#accordion" href="#queryResponses{{ $queries[$i]->id }}" aria-expanded="true" aria-controls="collapseOne" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> Ver Respuestas</a>
                                                            <button title="Eliminar Consulta" onclick="deleteQuery({{ $queries[$i]->id }})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Eliminar</a>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="4">
                                                        <div id="queryResponses{{ $queries[$i]->id }}" class="panel-collapse in collapse" role="tabpanel">
                                                            @if ($queries[$i]->queryResponses->count() == 0)
                                                                <h6 class="text-center">Sin respuestas a Consulta {{ $queries[$i]->id }}</h6>
                                                            @else
                                                                <h6 class="text-center">Respuestas a Consulta {{ $queries[$i]->id }}</h6>
                                                            @endif
                                                            <table class="table m-b-0">
                                                                @foreach ($queries[$i]->queryResponses as $query_response)
                                                                <tr>
                                                                    <td>{{ $query_response->response }}</td>
                                                                    <td>{{ $query_response->updated_atDateFormat() }}</td>
                                                                    <td><a href="{{ route('exceptions.queries_responses.edit', [$queries[$i]->id, $query_response->id]) }}" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i> Modificar</a></td>
                                                                    <td><button title="Eliminar Respuesta a Consulta" onclick="deleteQueryResponse({{ $queries[$i]->id.','.$query_response->id }})" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Eliminar</a></td>
                                                                </tr>
                                                                @endforeach
                                                            </table>
                                                            <div class="m-t-10 m-b-20">
                                                                <a href="{{ route('exceptions.queries_responses.create', $queries[$i]->id) }}" class="btn btn-sm btn-primary">Cargar Respuesta</a>
                                                            </div>
                                                        </div>
                                                        </td>
                                                    </tr>
                                                @endfor

                                                </tbody>
                                            </table>
                                            <div class="text-right m-t-20">
                                                <a href="{{ route('exceptions.queries.create', $order->id) }}" class="btn btn-primary">Cargar Consultas</a>
                                            </div>
                                            {{-- *** Para cargar archivos relacionados a Reparos ** --}}
                                            <label class="col-form-label f-w-600">Archivos relacionados a consultas:</label>
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
                                                    @for ($i=0; $i < count($exception_filedncp_con); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $exception_filedncp_con[$i]->description }}</td>
                                                        {{-- <td>{{ $exception_filesdncp[$i]->file_type }}</td>                                                         --}}
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$exception_filedncp_con[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('orders.files.download', $exception_filedncp_con[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                            <button title="Eliminar Archivo" onclick="deleteFile({{ $exception_filedncp_con[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>                                                            
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">                                                
                                                <a href="{{ route('orders.files.create_filedncp_con', $order->id) }}" class="btn btn-primary">Cargar Archivos de Consultas</a>                                                
                                            </div>                                            
                                        </div>   



                                        <div class="tab-pane" id="tab9" role="tabpanel">
                                            <label class="col-form-label f-w-600">Reparos obtenidos en el portal de la DNCP:</label>
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
                                                                <a href="{{ route('exceptions.objections.edit', [$order->id, $objections[$i]->id]) }}" class="btn btn-warning"><i class="fa fa-edit"></i> Modificar</a>
                                                                <a data-toggle="collapse" data-parent="#accordion" href="#objectionResponses{{ $objections[$i]->id }}" aria-expanded="true" aria-controls="collapseOne" class="btn btn-info"><i class="fa fa-eye"></i> Ver Respuestas</a>
                                                                <button title="Eliminar Reparo" onclick="deleteObjection({{ $objections[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <div id="objectionResponses{{ $objections[$i]->id }}" class="panel-collapse in collapse" role="tabpanel">
                                                    {{-- <h6>Respuestas a reparo {{ $objections[$i]->id }}</h6> --}}
                                                    <table class="table m-b-0">
                                                        @foreach ($objections[$i]->objectionResponses as $obj_response)
                                                        <tr>
                                                            <td>{{ $obj_response->response }}</td>
                                                            <td>{{ $obj_response->updated_atDateFormat() }}</td>
                                                            <td><a href="{{ route('exceptions.objections_responses.edit', [$objections[$i]->id, $obj_response->id]) }}" class="btn btn-warning"><i class="fa fa-edit"></i> Modificar</a></td>
                                                            <td><button title="Eliminar Respuesta a Reparo" onclick="deleteObjectionResponse({{ $objections[$i]->id.','.$obj_response->id }})" class="btn btn-danger"><i class="fa fa-trash"></i> Eliminar</a></td>
                                                        </tr>
                                                        @endforeach
                                                    </table>
                                                    <div class="m-b-20">
                                                        <a href="{{ route('exceptions.objections_responses.create', $objections[$i]->id) }}" class="btn btn-primary">Cargar Respuesta</a>
                                                    </div>
                                                </div>
                                            @endfor                                            
                                            <div class="text-right m-t-20">
                                                <a href="{{ route('exceptions.objections.create', $order->id) }}" class="btn btn-primary">Cargar Reparos</a>
                                            </div>
                                            {{-- *** Para cargar archivos relacionados a Reparos ** --}}
                                            <label class="col-form-label f-w-600">Archivos relacionados a reparos:</label>
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
                                                    @for ($i=0; $i < count($exception_filedncp); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $exception_filedncp[$i]->description }}</td>
                                                        {{-- <td>{{ $exception_filesdncp[$i]->file_type }}</td>                                                         --}}
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$exception_filedncp[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('orders.files.download', $exception_filedncp[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                            <button title="Eliminar Archivo" onclick="deleteFile({{ $exception_filedncp[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>                                                            
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">                                                
                                                <a href="{{ route('orders.files.create_filedncp', $order->id) }}" class="btn btn-primary">Cargar Archivos de Reparos</a>                                                
                                            </div>                                              
                                        </div>
                                        
                                        {{-- PARA EMPRESAS INVITADAS --}}
                                        <div class="tab-pane" id="tab10" role="tabpanel">
                                            <table id="budget_request_providers" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="font-weight: bold; color:red">#</th>
                                                        <th style="font-weight: bold; color:red">Empresa invitada</th>
                                                        <th style="font-weight: bold; color:red">RUC</th>
                                                        <th style="font-weight: bold; color:red">Teléfono</th>
                                                        <th style="font-weight: bold; color:red">Email para Ofertas</th>
                                                        <th style="font-weight: bold; color:red">Email para Ord. Compras</th>
                                                        <th style="font-weight: bold; color:red">Representante</th>                                                        
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @for ($i = 0; $i < count($order->budgetRequestProviders); $i++)
                                                    <tr>
                                                        {{-- Muestra las empresas invitadas (request_provider_type=2) --}}
                                                        @if ($order->budgetRequestProviders[$i]->request_provider_type==2)
                                                            <td>{{ ($i+1) }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->description }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->ruc }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->telefono }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->email_oferta }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->email_ocompra }}</td>                                                            
                                                            <td>{{ $order->budgetRequestProviders[$i]->provider->representante }}</td>
                                                        @else
                                                            {{-- {{ ($i+0) }} --}}
                                                        @endif
                                                    </tr>
                                                @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">                                                
                                                {{-- En caso de no tener pedidos de tipo 2 (empresas invitadas) --}}                                                
                                                @if($order->budgetRequestProviders->where('request_provider_type', 2)->count() == 0)
                                                    {{-- Si pedido está anulado no muestra botón agregar --}}
                                                    @if ($order->actual_state == 0)
                                                    @else
                                                        {{-- Se pregunta por permisos de Licitaciones --}}
                                                        @if (Auth::user()->hasPermission(['admin.budget_request_providers.create','exceptions.budget_request_providers.create']))
                                                            <a href="{{ route('orders.budget_request_providers.create_providers_guests', $order->id) }}" class="btn btn-primary">Agregar Empresa invitada</a>
                                                        @endif
                                                    @endif
                                                @else
                                                    {{-- Si pedido está anulado no muestra botón agregar --}}
                                                    @if ($order->actual_state == 0)
                                                    @else
                                                        @if (Auth::user()->hasPermission(['admin.budget_request_providers.update','exceptions.budget_request_providers.update']))
                                                            <a href="{{ route('orders.budget_request_providers.edit_providers_guests', $order->id) }}" class="btn btn-success">Editar Empresas invitadas</a> 
                                                            {{-- SI LLAMADO YA SE ENCUENTRA EN ADJUDICACION     --}}
                                                            {{-- @if ($order->actual_state <> 65)                                                                
                                                                <a href="{{ route('orders.budget_request_providers.edit_providers_guests', $order->id) }}" class="btn btn-success">Editar Empresas invitadas</a> 
                                                            @else
                                                                    <br><br>
                                                                    <h4 style="background-color:yellow;font-weight: bold; color:red" class="text-rigth">El Llamado ya se encuentra en Adjudicaciones</h4>
                                                            @endif   --}}
                                                        @endif
                                                        {{-- @if (Auth::user()->hasPermission(['admin.budget_request_providers.delete','exceptions.budget_request_providers.delete']) || $order->dependency_id == Auth::user()->dependency_id)
                                                            <button type="button" title="Borrar" class="btn btn-danger" onclick="deleteProviders_providers_guests({{ $order->id }})">
                                                                Borrar
                                                            </button>
                                                        @endif --}}
                                                    @endif
                                                @endif
                                                {{-- <br><br> --}}
                                                <br><br>                                                
                                                <div class="col-sm-2">
                                                    {{-- Para editar empresas invitadas (con permisos de index, update y create de proiders en Roles de Licit,compras men y excepciones) --}}
                                                    @if ((Auth::user()->hasPermission(['admin.providers.update'])))
                                                        <a href="{{ route('providers.index')}}" class="btn btn-danger waves-effect" target="_blank">Crear/Editar datos de Empresas</a>
                                                    @endif
                                                </div>
                                            </div>                                            
                                        </div>
                                        

                                        {{-- PARA EMPRESAS PARTICIPANTES --}}
                                        <div class="tab-pane" id="tab11" role="tabpanel">
                                            {{-- @php
                                                var_dump($order->budgetRequestProviders->where('request_provider_type', 3)->count());
                                            @endphp --}}

                                            <table id="budget_request_providers" class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th style="font-weight: bold; color:red">#</th>                                                       
                                                        <th style="font-weight: bold; color:red">Empresa participante</th>
                                                        <th style="font-weight: bold; color:red">RUC</th>
                                                        <th style="font-weight: bold; color:red">Teléfono</th>
                                                        <th style="font-weight: bold; color:red">Email para Ofertas</th>
                                                        <th style="font-weight: bold; color:red">Email para Ord. Compras</th>
                                                        <th style="font-weight: bold; color:red">Representante</th>
                                                    </tr>
                                                </thead>
                                                <tbody>

                                                @for ($j = 0; $j < count($order->budgetRequestProviders);($j++))
                                                    <tr>                                                        
                                                        {{-- Muestra las empresas invitadas (request_provider_type=3) --}}
                                                        @if ($order->budgetRequestProviders[$j]->request_provider_type==3)                                                                
                                                            <td>{{ ($j+1) }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$j]->provider->description }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$j]->provider->ruc }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$j]->provider->telefono }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$j]->provider->email_oferta }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$j]->provider->email_ocompra }}</td>
                                                            <td>{{ $order->budgetRequestProviders[$j]->provider->representante }}</td>
                                                        @endif
                                                    </tr>
                                                @endfor
                                                </tbody>                                                
                                            </table>
                                            <div class="text-right">
                                                {{-- En caso de no tener pedidos de tipo 3 (empresas participantes) --}}                                                
                                                @if($order->budgetRequestProviders->where('request_provider_type', 3)->count() == 0)
                                                    {{-- Si pedido está anulado no muestra botón agregar --}}
                                                    @if ($order->actual_state == 0)
                                                    @else
                                                        {{-- Se pregunta por permisos de Licitaciones --}}
                                                        @if (Auth::user()->hasPermission(['admin.budget_request_providers.create','exceptions.budget_request_providers.create']))
                                                            <a href="{{ route('orders.budget_request_providers.create_providers_participants', $order->id) }}" class="btn btn-primary">Agregar Empresa participante</a>
                                                        @endif
                                                    @endif
                                                @else
                                                    {{-- Si pedido está anulado no muestra botón agregar --}}
                                                    @if ($order->actual_state == 0)
                                                    @else
                                                        @if (Auth::user()->hasPermission(['admin.budget_request_providers.update','exceptions.budget_request_providers.update']))
                                                            <a href="{{ route('orders.budget_request_providers.edit_providers_participants', $order->id) }}" class="btn btn-success">Editar Empresas participantes</a>

                                                            {{-- SI LLAMADO YA SE ENCUENTRA EN ADJUDICACION     --}}
                                                            {{-- @if ($order->actual_state <> 65)                                                                
                                                                    <a href="{{ route('orders.budget_request_providers.edit_providers_participants', $order->id) }}" class="btn btn-success">Editar Empresas participantes</a>
                                                            @else
                                                                    <br><br>
                                                                    <h4 style="background-color:yellow;font-weight: bold; color:red" class="text-rigth">El Llamado ya se encuentra en Adjudicaciones</h4>
                                                            @endif --}}

                                                        @endif
                                                        {{-- @if (Auth::user()->hasPermission(['admin.budget_request_providers.delete','exceptions.budget_request_providers.delete']) || $order->dependency_id == Auth::user()->dependency_id)
                                                            <button type="button" title="Borrar" class="btn btn-danger" onclick="deleteProviders_providers_participants({{ $order->id }})">
                                                                Borrar
                                                            </button>
                                                        @endif --}}
                                                    @endif
                                                @endif                                            
                                            </div>
                                        </div>                                       

                                        {{-- PARA CUADRO COMPARATIVO --}}
                                        <div class="tab-pane" id="tab12" role="tabpanel">
                                            <label class="col-form-label f-w-600">Cuadro Comparativo de Ofertas:</label>                                            
                                            <table class="table table-striped table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Descripción</th>
                                                        <th>Fecha/Hora</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>                                                   
                                                    @for ($i=0; $i < count($exception_files2); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $exception_files2[$i]->description }}</td>
                                                        <td>{{ $exception_files2[$i]->updated_atDateFormat() }}</td>                                                        
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$exception_files2[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('orders.files.download', $exception_files2[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                            <button title="Eliminar Archivo" onclick="deleteFile({{ $exception_files2[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">                                                                                                
                                                <a href="{{ route('orders.files.create_cuadro_compar', $order->id) }}" class="btn btn-primary">Cargar Archivo de Cuadro Comparativo</a>                                                                                                
                                            </div>
                                            <div class="col-sm-10 text-left">                                                
                                                <h6 style="font-weight: bold; color:red"> Adjuntar: Archivo de Cuadro Comparativo de Ofertas</h6>
                                            </div>
                                        </div>

                                        <div class="tab-pane" id="tab3" role="tabpanel">
                                            <div class="row">
                                                <table id="items" class="table table-striped table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Lote</th>
                                                            <th>Ítem</th>
                                                            <th>Cód. de Catál.</th>
                                                            <th>Descripción</th>
                                                            <th>EETT</th>
                                                            <th>Present.</th>
                                                            <th>U.M.</th>
                                                            {{-- Mostramos ítemes de Contrato Abierto --}}
                                                            @if ($order->open_contract == 1) 
                                                                <th>Precio Unitario</th> 
                                                                <th>Pedido Mínimo</th>
                                                                <th>Pedido Máximo</th>
                                                                <th>Monto Mínimo</th>
                                                                <th>Monto Máximo</th>
                                                                <th>Acciones</th>
                                                            @else
                                                                {{-- Mostramos ítemes de Contrato Cerrado --}}
                                                                @if ($order->open_contract == 2) 
                                                                    <th>Cantidad</th>
                                                                    <th>Precio Unitario</th>                                                                
                                                                    <th>Monto Total</th>
                                                                    <th>Acciones</th>
                                                                @else
                                                                {{-- Mostramos ítemes de Contrato Abierto con Mmin y Mmax --}}
                                                                    <th>Cantidad</th>
                                                                    <th>Precio Unitario IVA INCL.</th>                                                                
                                                                    <th>Monto Mínimo</th>
                                                                    <th>Monto Máximo</th>
                                                                    <th>Acciones</th>
                                                                @endif    
                                                            @endif
                                                            
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    @for ($i = 0; $i < count($order->items); $i++)
                                                        <tr>
                                                            <td>{{ ($i+1) }}</td>
                                                            <td>{{ $order->items[$i]->batch }}</td>
                                                            <td>{{ $order->items[$i]->item_number }}</td>                                                                
                                                            @if ($order->items[$i]->level5CatalogCode->code == '99999999-9999')                                                                    
                                                                <td class="columna3" style="color:red;font-weight: bold">{{ $order->items[$i]->level5CatalogCode->code }}</td>
                                                                <td style="color:red;font-weight: bold">{{ $order->items[$i]->level5CatalogCode->description }}</td>
                                                            @else
                                                                <td class="columna3"> {{ $order->items[$i]->level5CatalogCode->code }}</td>
                                                                <td>{{ $order->items[$i]->level5CatalogCode->description }}</td>
                                                            @endif                                                                
                                                            <td>{{ $order->items[$i]->technical_specifications }}</td>
                                                            <td>{{ $order->items[$i]->orderPresentation->description }}</td>
                                                            <td>{{ $order->items[$i]->orderMeasurementUnit->description }}</td>

                                                            {{-- Mostramos ítemes de Contrato Abierto --}}
                                                            @if ($order->open_contract == 1) 
                                                                <td class="columna12" style="text-align: center">{{ 'Gs. '.$order->items[$i]->unitPriceFormat() }}</td>                                                            
                                                                <td class="columna9"style="text-align: center">{{ $order->items[$i]->min_quantityFormat() }}</td>
                                                                <td class="columna10"style="text-align: center">{{ $order->items[$i]->max_quantityFormat() }}</td>
                                                                <td class="columna11"style="text-align: center">{{ 'Gs. '.$order->items[$i]->totalAmount_min_Format() }}</td>
                                                                <td class="columna12">{{ 'Gs. '.$order->items[$i]->totalAmountFormat() }}</td>
                                                            @else
                                                                {{-- Mostramos ítemes de Contrato Cerrado --}}
                                                                @if ($order->open_contract == 2) 
                                                                    <td class="columna8"style="text-align: center">{{ $order->items[$i]->quantityFormat() }}</td> 
                                                                    <td class="columna12" style="text-align: center">{{ 'Gs. '.$order->items[$i]->unitPriceFormat() }}</td>                                                                                                                                               
                                                                    <td class="columna12">{{ 'Gs. '.$order->items[$i]->totalAmountFormat() }}</td>
                                                                @else
                                                                    {{-- Mostramos ítemes de Contrato Abierto con Mmin y Mmax --}}                                                                        
                                                                    <td class="columna10"style="text-align: center">{{ $order->items[$i]->quantityFormat() }}</td>
                                                                    <td class="columna12" style="text-align: center">{{ 'Gs. '.$order->items[$i]->unitPriceFormat() }}</td> 
                                                                    <td class="columna11"style="text-align: center">{{ 'Gs. '.$order->items[$i]->totalAmount_min_Format() }}</td>
                                                                    <td class="columna12">{{ 'Gs. '.$order->items[$i]->totalAmountFormat() }}</td>
                                                                @endif
                                                            @endif
                                                            
                                                            
                                                            <td style="white-space:nowrap">
                                                                <button type="button" title="Listado de Precios Referenciales" class="btn btn-primary btn-icon" onclick="itemAwardHistories({{ $order->items[$i]->id }})">
                                                                    <i class="fa fa-list"></i>
                                                                </button>
                                                            @if (Auth::user()->hasPermission(['admin.items.update']) || $order->dependency_id == Auth::user()->dependency_id)
                                                                <button type="button" title="Editar" class="btn btn-warning btn-icon" onclick="updateItem({{ $order->items[$i]->id }})">
                                                                    <i class="fa fa-pencil"></i>
                                                                </button>
                                                            @endif
                                                            @if (Auth::user()->hasPermission(['admin.items.delete']) || $order->dependency_id == Auth::user()->dependency_id)
                                                                <button type="button" title="Borrar" class="btn btn-danger btn-icon" onclick="deleteItem({{ $order->items[$i]->id }})">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            @endif
                                                            </td>
                                                        </tr>
                                                    @endfor
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="text-right">
                                                @if (Auth::user()->hasPermission(['admin.items.create']) || $order->dependency_id == Auth::user()->dependency_id)
                                                    <a href="{{ route('orders.items.create', $order->id) }}" class="btn btn-primary">Agregar ítem</a>
                                                    <a href="{{ route('orders.items.uploadExcel', $order->id)}}" title="Cargar Archivo EXCEL" class="btn btn-success btn-icon">
                                                    <i class="fa fa-upload text-white"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
{{-- Modal para cargar la fecha Apertura de Sobres --}}
<div id="fechaAperturaSobresModal" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Consultas de Oferentes</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
            <div class="col-sm-12">
                <label class="col-form-label @error('queries_deadline') has-danger @enderror">Fecha Apertura de Sobres:</label>
                <div class="input-group @error('queries_deadline') has-danger @enderror">
                    <input type="text" id="queries_deadline" name="queries_deadline" value="{{ is_null($order->queries_deadline) ? date('d/m/Y') : date('d/m/Y', strtotime($order->queries_deadline)) }}" class="form-control" autocomplete="off">
                    <span class="input-group-append" id="basic-addon" onclick="show();">
                        <label class="input-group-text"><i class="fa fa-calendar"></i></label>
                    </span>
                </div>
            </div>
            <div class="col-sm-12">
                <div id="queries_deadline_warning" class="alert alert-warning hide" style="display: none">
                    <span id="queries_deadline_warning_message"></span>
                </div>
                <div id="queries_deadline_success" class="alert alert-success hide" style="display: none">
                    <span id="queries_deadline_success_message"></span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" onclick="updateQueriesDeadline({{ $order->id }})" class="btn btn-primary">Guardar</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$(document).ready(function(){

    $('#queries_deadline').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    });

    updateQueriesDeadline = function(order_id){
        $('#queries_deadline_warning').hide();
        $('#queries_deadline_success').hide();
        if($('#queries_deadline').val() == ""){
            $('#queries_deadline_warning_message').text('Advertencia! Debe seleccionar una fecha Apertura de Sobres.');
            $('#queries_deadline_warning').show();
            return;
        }

        $.ajax({
            url : '/exceptions/update_queries_deadline/'+order_id,
            method: 'POST',
            data: {queries_deadline: $('#queries_deadline').val(), _token: '{{ csrf_token() }}'},
            success: function(data){
                $('#fechaAperturaSobresModal').modal('toggle');
                
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        swal({title: "Exito", text: response.message, type: "success", },
                        function(isConfirm){ location.reload(); });
                    }else{
                        swal({title: "Advertencia", text: response.message, type: "warning", },
                        function(isConfirm){ location.reload(); });
                    }
                }catch(error){
                    swal({title: "Advertencia", text: "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la página.", type: "warning", },
                        function(isConfirm){ location.reload(); });
                    console.log(error);
                }
            },
            error: function(error){
                swal({title: "Advertencia", text: "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la página.", type: "warning", },
                        function(isConfirm){ location.reload(); });
                console.log(error);
            }
        });
    }

    recibeOrder = function(order_id){
        $.ajax({
            url : '/exceptions/recibe_order/'+order_id,
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

    recibeOrderUTA = function(order_id){
        $.ajax({
            url : '/exceptions/recibe_order_uta/'+order_id,
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
        swal({
            title: "Atención",
            text: "Está agregado el Archivo de Pliego de Bases y Condiciones (PBC)?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, derivar",
            cancelButtonText: "Cancelar",
        },
        function(isConfirm){
        if(isConfirm){
        $.ajax({
            url : '/exceptions/derive_order/'+order_id,
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
        }
      );
    };    

    derivePAC = function(order_id){
        swal({
            title: "Atención",
            text: "Derivar el Pedido al Dpto. de Planificación (PAC) por Observaciones?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, derivar a PAC por Observaciones",
            cancelButtonText: "Cancelar",
        },
        function(isConfirm){
        if(isConfirm){
        $.ajax({
            url : '/exceptions/derivePAC/'+order_id,
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
        }
      );
    };

    deriveComite = function(order_id){
        swal({
            title: "Atención",
            text: "Derivar Antecedentes al Comité Evaluador?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#55dd6b",
            confirmButtonText: "Sí, derivar",
            cancelButtonText: "Cancelar",
        },
        function(isConfirm){
        if(isConfirm){
        $.ajax({
            url : '/exceptions/derive_comite/'+order_id,
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
        }
      );
    };

    itemAwardHistories = function(item){
        location.href = '/items/'+item+'/item_exception_histories';
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

    deleteQuery = function(query){
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
              url : '/exceptions/{{ $order->id }}/queries/'+query,
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

    deleteQueryResponse = function(query, query_response){
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
              url : '/exceptions/'+query+'/queries_responses/'+query_response,
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
              url : '/exceptions/{{ $order->id }}/objections/'+objection,
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
              url : '/exceptions/'+objection+'/objections_responses/'+objection_response,
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