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
/* .columna1 { width: 3%; text-align: center;}
.columna2 { width: 60%; text-align: left;}
.columna3 { width: 2%; text-align: left;}
.columna4 { width: 10%; text-align: left;}
.columna5 { width: 10%; text-align: center;}
.columna6 { width: 3%; text-align: center;}
.columna7 { width: 3%; text-align: center;}
.columna8 { width: 3%; text-align: center;}
.columna9 { width: 3%; text-align: left;}
.columna10 { width: 3%; text-align: center;}
.columna11 { width: 8%; text-align: left;}
.columna12 { width: 5%; text-align: left;} */
p.centrado { */

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
                        <h5>Llamados</h5>
                        <span>Listado de Llamados de Compras Menores</span>
                        <a href="pdf/panel_minor" class="btn btn-danger" target="_blank">PANEL DE LLAMADOS</a>
                        {{-- <a href="pdf/panel_uta" class="btn btn-danger" target="_blank">PANEL DE LLAMADOS</a> --}}
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
                            <a href="{{ route('minor_purchases.index') }}">Llamados</a>
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
                                        <h5>Listado de Llamados de Compras Menores</h5>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <div class="dt-responsive table-responsive">
                                        <table id="orders" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    {{-- <th>Llamado N°</th> --}}
                                                    <th>Descripción</th>     
                                                    {{-- <th>Proc.COVID</th>                                                --}}
                                                    <th>Dependencia</th>
                                                    <th>SIMESE N°</th>
                                                    {{-- <th>Modalidad</th> --}}
                                                    <th>Monto total</th>
                                                    <th>PAC ID</th>
                                                    <th>Prioridad</th>
                                                    <th>Estimado Apertura</th>
                                                    <th>Fecha Apertura</th>
                                                    <th>Estado</th>
                                                   
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @for ($i = 0; $i < count($orders); $i++)
                                                <tr>
                                                    <td class="columna1">{{ ($i+1) }}</td>   

                                                    @if ($orders[$i]->covid==0)                                                            
                                                        <td>{{ is_null($orders[$i]->number)? $orders[$i]->description : $orders[$i]->modality->code." N° ".$orders[$i]->number."/".$orders[$i]->year." - ".$orders[$i]->description }}</td>
                                                    @else                                                            
                                                        <td>{{ is_null($orders[$i]->number)? $orders[$i]->description : $orders[$i]->modality->code." N° ".$orders[$i]->number."/".$orders[$i]->year." - ".$orders[$i]->description}} - <span style="color:red;font-weight: bold"> (PROCESO COVID)</span></td>
                                                    @endif  
                                                      
                                                    {{-- <td class="columna2">{{ is_null($orders[$i]->number)? $orders[$i]->description : $orders[$i]->modality->code." N° ".$orders[$i]->number." - ".$orders[$i]->description }}</td>                                                     --}}
                                                        
                                                    {{-- @if (is_null($orders[$i]->covid)) --}}
                                                    {{-- @if ($orders[$i]->covid==0)                                                            
                                                        <td>-</td>
                                                    @else                                                            
                                                        <td class="columna3" style="font-size: 13px; font-weight: bold; color:blue;background-color:yellow;">Proceso COVID</td>
                                                    @endif --}}
                                                    
                                                    <td class="columna4">{{ $orders[$i]->dependency->description }}</td>
                                                    {{-- <td class="columna5">{{ is_null($orders[$i]->simese->first()) ? '' : number_format($orders[$i]->simese->first()['simese'],'0', ',','.') }}</td>                             --}}
                                                    <td>{{ is_null($orders[$i]->simese->first()) ? '' : number_format($orders[$i]->simese->first()['simese'],'0', ',','.')."/".$orders[$i]->simese->first()['year'] }}</td>
                                                    {{-- <td>{{ $orders[$i]->modality->description }}</td>                                                                                                         --}}
                                                    <td class="columna6"> Gs.{{ number_format($orders[$i]->total_amount,'0', ',','.') }} </td>
                                                    <td class="columna7">{{ $orders[$i]->dncpPacIdFormat() }}</td>
                                                    
                                                        @if ($orders[$i]->urgency_state == "ALTA")
                                                            <td class="columna8" style="color:red;font-weight: bold">{{ $orders[$i]->urgency_state }}</td>
                                                        @else
                                                            @if ($orders[$i]->urgency_state == "MEDIA")
                                                                <td class="columna8" style="color:orange;font-weight: bold">{{ $orders[$i]->urgency_state }}</td>
                                                            @else
                                                                <td class="columna8">{{ $orders[$i]->urgency_state }}</td>
                                                            @endif
                                                        @endif
                                                    
                                                    <td class="columna9">{{ $orders[$i]->beginDateFormatmY() }}</td>
                                                    <td class="columna10">{{ $orders[$i]->queriesDeadline() }}</td>

                                                        @if (in_array($orders[$i]->orderState->id, [20,45,62,97,101,125,130,131]))                                                        
                                                            <td class="columna11" style="color:#ff0000">{{ $orders[$i]->orderState->id."-".$orders[$i]->orderState->description }}</td>
                                                        @else
                                                            <td class="columna11">{{ $orders[$i]->orderState->id."-".$orders[$i]->orderState->description }}</td>
                                                        @endif

                                                        <td>
                                                            @if ($orders[$i]->actual_state == 20)
                                                                <a href="{{ route('minor_purchases.show', $orders[$i]->id) }}" class="btn btn-success">Recibir Llamado</a>
                                                            @else
                                                                {{-- @if ($orders[$i]->actual_state == 130) --}}
                                                                @if (in_array($orders[$i]->actual_state, [25,130])) 
                                                                    <a href="{{ route('minor_purchases.show', $orders[$i]->id) }}" class="btn btn-danger">Procesar</a>
                                                                @else                                                                    
                                                                    <a href="{{ route('minor_purchases.show', $orders[$i]->id) }}" class="btn btn-primary">Ver Más</a>
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