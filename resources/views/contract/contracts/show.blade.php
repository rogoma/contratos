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
                        <span>Llamado Nº {{ $contract->number_year }}</span>
                        <br><br>
                        {{-- <h6>Estado Actual: {{ $contract->orderState->id." - ".$contract->orderState->description }}</h6> --}}
                        <h5><p style="font-size: 17px; font-weight: bold; color:#FF0000">Estado Actual: {{ $contract->contractState->id." - ".$contract->contractState->description }}</p></h5>
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
                                            {{-- <h5>{{ is_null($contract->number)? $contract->description : $contract->modality->description." N° ".$contract->number."-".$contract->description }} --}}
                                            {{-- @if ($contract->covid==0)
                                                <h5>{{ is_null($contract->number)? $contract->description : $contract->modality->description." N° ".$contract->number."/".$contract->year."-".$contract->description }}
                                            @else
                                                <h5>{{ is_null($contract->number)? $contract->description : $contract->modality->description." N° ".$contract->number."/".$contract->year."-".$contract->description }}
                                                <label style="font-size: 16px; font-weight: bold; color:blue;background-color:yellow;">Proceso COVID</label></h5>
                                            @endif  --}}

                                            {{-- PARA MOSTRAR SI ES COVID Y SI ES URGENCIA IMPOSTERGABLE --}}


                                        </div>
                                            <div class="col-sm-2">

                                            @if (in_array($contract->contract_state, [1]))
                                                <button class="btn btn-primary dropdown-toggle waves-effect" type="button" id="acciones" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones</button>
                                            @endif
                                            <div class="dropdown-menu" aria-labelledby="acciones" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
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

                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab6" role="tab"><i class="fa fa-file-archive-o"></i> Archivos (Anteced.)</a>
                                            <div class="slide"></div>
                                        </li>
                                        {{-- //Se controla que tenga estado recibido Contratos para mostrar estos TABS --}}
                                        {{-- @if ($contract->actual_state >= 75)
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tab7" role="tab"><i class="fa fa-folder-open-o"></i> Contratos</a>
                                                <div class="slide"></div>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" data-toggle="tab" href="#tab8" role="tab"><i class="fa fa-building-o"></i> Addendas</a>
                                                <div class="slide"></div>
                                            </li>
                                        @endif --}}
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
                                                        <td>{{ $contract->description }}</td>
                                                        <td>{{ $contract->modality->description }}</td>
                                                        <td>{{ $contract->iddncp }}</td>
                                                        <td>{{ $contract->year }}</td>
                                                        <td>{{ $contract->beginDateFormat() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><label class="col-form-label f-w-600">Línea Presupuestaria:</label></td>
                                                        <td><label class="col-form-label f-w-600">Fuente de Financiamiento:</label></td>
                                                        <td><label class="col-form-label f-w-600">Organismo Financiero:</label></td>
                                                        {{-- <td colspan="2"><label class="col-form-label f-w-600">Monto total:</label></td> --}}
                                                        <td colspan="2" style="font-size: 16px;color:blue;font-weight: bold">{{ 'Gs. '.$contract-> totalAmountFormat() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $contract->financialOrganism->description }}</td>
                                                        <td colspan="2">{{ 'Gs. '.$contract->totalAmountFormat() }}</td>
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
                                                        {{-- <td>{{ $contract->ad_referendum ? "SÍ" : "NO" }}</td>
                                                        <td>{{ $contract->plurianualidad ? "SÍ" : "NO" }}</td>
                                                        <td>{{ $contract->system_awarded_by }}</td>
                                                        <td>{{ $contract->expenditureObject->code }}</td>
                                                        <td>{{ $contract->fonacide ? "SÍ" : "NO" }}</td>
                                                        <td>{{ $contract->modality_description }}</td> --}}
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
                                                        {{-- <td>{{ $contract->contract_administrator }}</td>
                                                        <td>{{ $contract->contract_validity }}</td>
                                                        <td colspan="2">{{ $contract->additional_technical_documents }}</td>
                                                        <td colspan="2">{{ $contract->additional_qualified_documents }}</td> --}}
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2"><label class="col-form-label f-w-600">Planilla de Precios (Anexo 1):</label></td>
                                                        <td colspan="2"><label class="col-form-label f-w-600">Título de propiedad, planos aprobados por la municipalidad, licencia ambiental:</label></td>
                                                        <td><label class="col-form-label f-w-600">Medio Magnético:</label></td>
                                                        <td><label class="col-form-label f-w-600">Datos de la persona referente:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">{{ $contract->price_sheet }}</td>
                                                        <td colspan="2">{{ $contract->property_title }}</td>
                                                        <td>{{ $contract->magnetic_medium }}</td>
                                                        <td>{{ $contract->referring_person_data }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>


                                        </div>

                                        {{-- PARA EMPRESAS ADJUDICADAS --}}
                                        <div class="tab-pane" id="tab2" role="tabpanel">
                                            {{-- @php
                                                var_dump($contract->budgetRequestProviders->where('request_provider_type', 3)->count());
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

                                                </tbody>
                                            </table>
                                            <br>
                                            {{-- MOSTRAMOS COMPARATIVO ENTRE MONTO ITEMS Y MONTO ITEMS ADJUDICADO --}}
                                            @if ($contract->total_amount == $contract->total_amount_award || $contract->total_amount_award == 0 )
                                                <span style="font-size: 16px; font-weight: bold; color:blue" >MONTO TOTAL DE LA ADJUDICACIÓN: {{ $contract->total_amount }}</span>
                                            @else
                                                <span style="font-size: 16px; font-weight: bold; color:red;background-color:yellow;" >MONTO TOTAL DE LA ADJUDICACIÓN: {{ $contract->total_amount }} DISTINTO A MONTO TOTAL DE ITEMS</span>
                                            @endif
                                            <br><br>
                                            {{-- MOSTRAMOS SI TIENE CDP --}}
                                            @if ($contract->cdp_number > 0)
                                                <span style="color:blue;font-weight: bold;background-color:yellow">CDP Nº: {{ $contract->cdpNumberFormat()}} de fecha: {{ $contract->cdpDateFormat()}}</span>
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

                                                        </tbody>
                                                    </table>
                                            </div>
                                            <div class="text-right">
                                                {{-- @if (Auth::user()->hasPermission(['admin.items.create']) || $contract->dependency_id == Auth::user()->dependency_id) --}}
                                                    {{-- Si pedido está anulado no muestra agregar ítems --}}
                                                    {{-- @if ($contract->actual_state == 0)
                                                    @else
                                                        <a href="{{ route('orders.items_adjudica.create', $contract->id) }}" class="btn btn-primary">Agregar ítem</a>
                                                        <a href="{{ route('orders.items_adjudica.uploadExcelAw', $contract->id)}}" title="Cargar Archivo EXCEL" class="btn btn-success btn-icon">
                                                    @endif
                                                        <i class="fa fa-upload text-white"></i>
                                                    </a> --}}
                                                {{-- @endif --}}
                                            </div>
                                        </div>
                                        <br>
                                        {{-- <a href="/items/export-excel/{{ $contract->id }} " class="btn btn-mm btn-danger float-left">Listado en Excel</a> --}}

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
                                                        <td><a href="/pdf/reporte1/{{ $contract->id }}" class="btn btn-default" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Ver Formulario 1</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Formulario 2</td>
                                                        <td><a href="/pdf/reporte2/{{ $contract->id }}" class="btn btn-default" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Ver Formulario 2</a></td>
                                                    </tr>
                                                    {{-- Verificamos que el pedido tenga ítems cargados --}}
                                                    {{-- @if ($contract->items->count() > 0) --}}
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Formulario 3</td>
                                                        <td><a href="/pdf/reporte3/{{ $contract->id }}" class="btn btn-default" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Ver Formulario 3</a></td>
                                                    </tr>
                                                    {{-- @endif --}}
                                                    {{-- Verificamos que el pedido tenga ítems y solicitudes de presupuesto cargados --}}
                                                    {{-- @if ($contract->items->count() > 0 && $contract->budgetRequestProviders->count() > 0) --}}
                                                    <tr>
                                                        <td>4</td>
                                                        <td>Formulario 4</td>
                                                        <td><a href="/pdf/reporte4/{{ $contract->id }}" class="btn btn-default" target="_blank"><i class="fa fa-file-pdf-o"></i> &nbsp;Ver Formulario 4</a></td>
                                                    </tr>
                                                    {{-- @endif --}}
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

                                                </tbody>
                                            </table>
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

                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                {{-- <a href="{{ route('orders.files.create', $contract->id) }}" class="btn btn-primary">Cargar Archivos</a>                                                                                                --}}
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
                                                <a href="{{ route('orders.files.create', $contract->id) }}" class="btn btn-primary">Cargar Contratos</a>
                                            </div>
                                            <div class="col-sm-10 text-left">
                                                <h6>Adjuntar: Contratos</h6>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab8" role="tabpanel">

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
                                                    {{-- @for ($i=0; $i < count($contract_filedncp); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $contract_filedncp[$i]->description }}</td>
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$contract_filedncp[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('orders.files.download', $contract_filedncp[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                            <button title="Eliminar Archivo" onclick="deleteFile({{ $contract_filedncp[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endfor --}}
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                <a href="{{ route('orders.files.create_filedncp', $contract->id) }}" class="btn btn-primary">Cargar Addendas</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MOSTRAMOS TIMELINE --}}
                            {{-- <div class="card latest-update-card">
                                <div class="card-header">
                                    <h5 style="font-size: 14px; font-weight: bold; color:red" class="text-left">Movimientos del pedido</h5>
                                </div>
                                <div class="card-block">
                                    <div class="latest-update-box">
                                    @foreach ($contract->ordersOrderStates()->orderBy('id', 'desc')->get() as $item)
                                        <div class="row p-t-20 p-b-30 borde-alternado">
                                            <div class="col-auto text-right update-meta p-r-0">
                                                <i class="update-icon ring"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="javascript:void(0);">
                                                    <h6 style="font-size: 14px; font-weight: bold; color:Black">{{ $item->orderState->id}}-{{ $item->orderState->description}}</h6>
                                                </a>
                                                <p style="font-size: 14px; font-weight: bold; color:Black">{{ $item->creatorUser->getFullName()}}</p>
                                                <small style="font-size: 13px; font-weight: bold; color:Black">Fecha: {{ $item->createdAtDateFormat()}}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            </div> --}}
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
        location.href = '/orders/{{ $contract->id }}/items_budget/'+budget+'/edit/';

        // location.href = '/orders/{{ $contract->id }}/budget_request_providers/'+budget+'/edit_providers_contracts/';

        //  /items_budget = Route::resource('orders.items_budget', BudgetRequestProvidersController::class);
        //  /edit = public function edit(Request $request, $contract_id) de BudgetRequestProvidersController
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
              url : '/contracts/{{ $contract->id }}/objections/'+objection,
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
