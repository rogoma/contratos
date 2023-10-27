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
                        <h5>Pedidos</h5>
                        {{-- <span>Listado de Pedidos</span>                         --}}
                            <a href="pdf/panel_uta" class="btn btn-primary" target="_blank">LLAMADOS EN CURSO</a>
                            <a href="pdf/panel_uta2" class="btn btn-primary" target="_blank">LLAMADOS ADJUDICADOS</a>
                            <a href="orders/exportarexcel" class="btn btn-success">EnCurso-Excel</a>
                            <a href="orders/exportarexcel2" class="btn btn-success">Adjudicados-Excel</a>
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
                            <a href="{{ route('plannings.index') }}">Pedidos</a>
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
                                    <div class="float-left">
                                        <h5>Listado de Pedidos</h5>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <div class="dt-responsive table-responsive">
                                        <table id="orders" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Modalidad</th>
                                                    <th>Descripción/Dependencia</th>                                                    
                                                    <th>SIMESE N°</th>                                                    
                                                    <th>Prioridad</th>
                                                    <th>Monto Total</th>
                                                    <th>PAC ID</th>
                                                    <th>Estimado Apertura</th>
                                                    <th>N° CDP - FECHA</th>
                                                    {{-- <th>FECHA CDP</th> --}}
                                                    <th>MONTO CDP</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @for ($i = 0; $i < count($orders); $i++)
                                                <tr>
                                                    <td>{{ ($i+1) }}</td>

                                                    {{-- <td>{{ $orders[$i]->modality->description }}</td> --}}
                                                    @if (in_array($orders[$i]->modality->id, [28]))
                                                        <td style="color:#ff0000">{{ $orders[$i]->modality->description }}</td>

                                                    @else
                                                        <td>{{ $orders[$i]->modality->description }}</td>
                                                    @endif

                                                    {{-- @if ($orders[$i]->covid==0)                                                            
                                                        <td>{{ is_null($orders[$i]->number)? $orders[$i]->description." - ".$orders[$i]->dependency->description : $orders[$i]->modality->code." N° ".$orders[$i]->number." - ".$orders[$i]->description." - ".$orders[$i]->dependency->description }}</td>
                                                    @else                                                            
                                                        <td>{{ is_null($orders[$i]->number)? $orders[$i]->description." - ".$orders[$i]->dependency->description." - PROCESO COVID" : $orders[$i]->modality->code." N° ".$orders[$i]->number." - ".$orders[$i]->description." - (PROCESO COVID)"." - ".$orders[$i]->dependency->description }}</td>                                                        
                                                    @endif --}}

                                                    {{-- PARA MOSTRAR SI ES COVID Y SI ES URENCIA IMPOSTERGABLE --}}
                                                    @if ($orders[$i]->covid==0)
                                                        @if ($orders[$i]->unpostponable==0)
                                                            <td>{{ is_null($orders[$i]->number)? $orders[$i]->description." - ".$orders[$i]->dependency->description : $orders[$i]->modality->code." N° ".$orders[$i]->number."/".$orders[$i]->year." - ".$orders[$i]->description." - ".$orders[$i]->dependency->description }}</td>
                                                        @else
                                                            <td>{{ is_null($orders[$i]->number)? $orders[$i]->description." - ".$orders[$i]->dependency->description : $orders[$i]->modality->code." N° ".$orders[$i]->number."/".$orders[$i]->year." - ".$orders[$i]->description." - ".$orders[$i]->dependency->description}} - <span style="color:red;font-weight: bold;background-color:yellow">(URGENCIA IMPOSTERGABLE)</span> </td>
                                                        @endif
                                                    @else
                                                        @if ($orders[$i]->unpostponable==0)
                                                            <td>{{ is_null($orders[$i]->number)? $orders[$i]->description : $orders[$i]->modality->code." N° ".$orders[$i]->number."/".$orders[$i]->year." - ".$orders[$i]->description." - ".$orders[$i]->dependency->description}} - <span style="color:red;font-weight: bold"> (PROCESO COVID)</span></td>
                                                        @else
                                                            <td>{{ is_null($orders[$i]->number)? $orders[$i]->description : $orders[$i]->modality->code." N° ".$orders[$i]->number."/".$orders[$i]->year." - ".$orders[$i]->description." - ".$orders[$i]->dependency->description}} - <span style="color:red;font-weight: bold"> (PROCESO COVID) - <span style="color:red;font-weight: bold;background-color:yellow">(URGENCIA IMPOSTERGABLE)</span></td>                                                        
                                                        @endif                                                        
                                                    @endif
                                                    
                                                    {{-- <td>{{ is_null($orders[$i]->simese->first()) ? '' : number_format($orders[$i]->simese->first()['simese'],'0', ',','.') }}</td> --}}
                                                    <td>{{ is_null($orders[$i]->simese->first()) ? '' : number_format($orders[$i]->simese->first()['simese'],'0', ',','.')."/".$orders[$i]->simese->first()['year'] }}</td>
                                                    
                                                        @if ($orders[$i]->urgency_state == "ALTA")
                                                            <td style="color:red;font-weight: bold">{{ $orders[$i]->urgency_state }}</td>
                                                        @else
                                                            @if ($orders[$i]->urgency_state == "MEDIA")
                                                                <td style="color:orange;font-weight: bold">{{ $orders[$i]->urgency_state }}</td>
                                                            @else
                                                                <td>{{ $orders[$i]->urgency_state }}</td>
                                                            @endif
                                                        @endif

                                                    {{-- <td>{{ $orders[$i]->expenditureObject->code }}</td> --}}
                                                    <td>{{ $orders[$i]->totalAmountFormat() }}</td>
                                                    <td>{{ $orders[$i]->dncpPacIdFormat() }}</td>
                                                    <td>{{ $orders[$i]->beginDateFormatmY() }}</td>
                                                    <td class="text-center">{{ $orders[$i]->cdpNumberFormat()}} {{ $orders[$i]->cdpDateFormat()}}</td>
                                                    {{-- <td>{{ $orders[$i]->cdpDateFormat()}}</td> --}}
                                                    <td>{{ $orders[$i]->cdpAmountFormat()}}</td>
                                                        @if (in_array($orders[$i]->orderState->id, [12,15,16,57,58,59,68]))
                                                            <td style="color:#ff0000">{{ $orders[$i]->orderState->id."-".$orders[$i]->orderState->description }}</td>
                                                        @else
                                                            <td>{{ $orders[$i]->orderState->id."-".$orders[$i]->orderState->description }}</td>
                                                        @endif
                                                    <td>                                                        
                                                        {{-- @switch($orders[$i]->actual_state)
                                                            @case(12)
                                                                <a href="{{ route('plannings.show', $orders[$i]->id) }}" class="btn btn-success">Procesar</a>
                                                                @break
                                                            @case(20)
                                                                <a href="{{ route('plannings.show', $orders[$i]->id) }}" class="btn btn-primary">Ver más</a>
                                                                @break
                                                            @case(25)
                                                                <a href="{{ route('plannings.show', $orders[$i]->id) }}" class="btn btn-warning">Consultar</a>
                                                                @break
                                                        @endswitch --}}

                                                        {{-- @if ($orders[$i]->actual_state == 12) --}}
                                                        @if (in_array($orders[$i]->actual_state, [12,15]))                                                        
                                                            <a href="{{ route('plannings.show', $orders[$i]->id) }}" class="btn btn-success">Procesar</a>
                                                        @else
                                                            @if ($orders[$i]->actual_state >= 20)                                                                                    
                                                                <a href="{{ route('plannings.show', $orders[$i]->id) }}" class="btn btn-danger">Consultar</a>
                                                            @else
                                                                {{-- <a href="{{ route('plannings.show', $orders[$i]->id) }}" class="btn btn-danger">Consultar</a> --}}
                                                            @endif
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endfor
                                            </tbody>
                                        </table>
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

    $('#orders').DataTable();

});
</script>
@endpush