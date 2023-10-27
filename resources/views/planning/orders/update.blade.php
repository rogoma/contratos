@extends('layouts.app')
@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-file-text bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Pedidos</h5>
                        <span>Cargar datos de Llamado</span>
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
                                    <h5>Cargar datos llamado</h5>
                                </div>
                                <div class="col-sm-8 text-left">
                                    {{-- <h6>{{ $order->description }} </h6> --}}
                                    {{-- <h6>Modalidad: {{ $order->modality->description }}</label> <label class="label label-info m-l-5">Prioridad {{ $order->urgency_state }}</label><label class="label label-info m-l-5"> SIMESE: {{ number_format($order->simese->first()['simese'],'0', ',','.') }} </label></h6> --}}
                                    <h6>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."-".$order->description }}
                                        @if ($order->urgency_state == "ALTA")
                                            <label class="label label-danger m-l-5">Prioridad {{ $order->urgency_state }} </label> <label style="font-size: 17px; font-weight: bold; color:blue">SIMESE: {{ is_null($order->simese->first()) ? '' : number_format($order->simese->first()['simese'],'0', ',','.')."/".$order->simese->first()['year'] }}</label> </h6>
                                        @else
                                            @if ($order->urgency_state == "MEDIA")
                                                <label class="label label-warning m-l-5">Prioridad {{ $order->urgency_state }}</label> </label> <label style="font-size: 17px; font-weight: bold; color:blue">SIMESE: {{ is_null($order->simese->first()) ? '' : number_format($order->simese->first()['simese'],'0', ',','.')."/".$order->simese->first()['year'] }}</label> </h6>
                                            @else
                                                <label class="label label-info m-l-5">Prioridad {{ $order->urgency_state }}</label> </label> <label style="font-size: 17px; font-weight: bold; color:blue">SIMESE: {{ is_null($order->simese->first()) ? '' : number_format($order->simese->first()['simese'],'0', ',','.')."/".$order->simese->first()['year'] }}</label></h6>
                                            @endif
                                        @endif

                                        {{-- SI MODALIDAD SE ELIGIO SOLO COMO CVE EN PEDIDO DE REQUIRIENTE --}}
                                        @if ($order->modality->id == 28)
                                            <label style="font-size: 17px; font-weight: bold; color:red;background-color:yellow;" class="label label-danger m-1-5">Modalidad debe ser CVE-IC o CVE-DP</label>
                                        @endif
                                    {{-- <h6><p style="font-size: 17px; font-weight: bold; color:blue">SIMESE: {{ is_null($order->simese->first()) ? '' : number_format($order->simese->first()['simese'],'0', ',','.')."/".$order->simese->first()['year'] }}</p></h6> --}}
                                </div>

                                <div class="card-block">
                                    <form class="row" method="POST" action="{{ route('plannings.orders.update', $order->id) }}">
                                        @csrf
                                        @method('PUT')

                                        {{-- CÓDIGO PARA MOSTRAR ERRORES --}}
                                        <div class="col-sm-12">
                                            @if ($errors->any())
                                           <div class="alert alert-danger">
                                           <ul>
                                               @foreach ($errors->all() as $error)
                                                 <li>{{ $error }}</li>
                                               @endforeach
                                           </ul>
                                           </div>
                                           @endif
                                        </div>

                                        <div class="col-sm-1">
                                            <div class="form-group @error('number') has-danger @enderror">
                                                <label class="col-form-label">Llamado <br><small>(Nro de llamado)</small></label>
                                                <input type="number" id="number" name="number" value="{{ old('number', $order->number) }}" class="form-control">
                                                @error('number')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- MUESTRA EL AÑO DEL PEDIDO, DEBE MOSTRAR EL AÑO DEL LLAMADO --}}
                                        <div class="col-sm-0">
                                            <div class="form-group @error('number') has-danger @enderror">
                                                <label class="col-form-label">Año <br><small>(Año)</small></label>
                                                <br>
                                                <label class="label label-danger m-l-1"><h6>{{ $order->year }}</h6>
                                            </div>
                                        </div>

                                        <div class="col-sm-13">
                                            <div class="form-group @error('dncp_pac_id') has-danger @enderror">
                                                <label class="col-form-label">PAC ID <br><small>(ID del PAC)</small></label>
                                                <input type="number" id="dncp_pac_id" name="dncp_pac_id" value="{{ old('dncp_pac_id', $order->dncp_pac_id) }}" class="form-control">
                                                @error('dncp_pac_id')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-group @error('total_amount') has-danger @enderror">
                                                <label class="col-form-label">Monto Final del Llamado<br><small>(Monto total PAC)</small></label>
                                                {{-- <input type="number" id="total_amount" name="total_amount" value="{{ old('total_amount', $order->total_amount) }}" class="form-control"> --}}
                                                <input type="text" id="total_amount" name="total_amount" value="{{ old('total_amount', $order->total_amount) }}" class="form-control total_amount autonumber" data-a-sep="." data-a-dec="," readonly>
                                                @error('total_amount')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-3">
                                            <label class="col-form-label @error('begin_date') has-danger @enderror">Fecha de Inicio de Llamado <br><small>(Fecha estimada de inicio de llamado)</small></label>
                                            <div class="input-group @error('begin_date') has-danger @enderror">
                                                <input type="text" id="begin_date" name="begin_date" value="{{ old('begin_date', empty($order->begin_date) ? '' : date('d/m/Y', strtotime($order->begin_date))) }}" class="form-control" autocomplete="off">
                                                <span class="input-group-append" id="basic-addon">
                                                    <label class="input-group-text" onclick="show('begin_date');"><i class="fa fa-calendar"></i></label>
                                                </span>
                                            </div>
                                            @error('begin_date')
                                            <div class="has-danger">
                                                <div class="col-form-label">{{ $message }}</div>
                                            </div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-2">
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group @error('cdp_number') has-danger @enderror">
                                                <label class="col-form-label">CDP N°<br><small>(CDP N°)</small></label>
                                                {{-- <input type="number" id="cdp_number" name="cdp_number" value="{{ old('cdp_number', $order->cdp_number) }}" class="form-control"> --}}
                                                <input type="text" id="cdp_number" name="cdp_number" value="{{ old('cdp_number', $order->cdp_number) }}" class="form-control cdp_number autonumber" data-a-sep="." data-a-dec=",">
                                                @error('cdp_number')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <label class="col-form-label @error('cdp_date') has-danger @enderror">Fecha de CDP <br><small>(Fecha de CDP)</small></label>
                                            <div class="input-group @error('cdp_date') has-danger @enderror">
                                                <input type="text" id="cdp_date" name="cdp_date" value="{{ old('cdp_date', empty($order->cdp_date) ? '' : date('d/m/Y', strtotime($order->cdp_date))) }}" class="form-control" autocomplete="off" >
                                                <span class="input-group-append" id="basic-addon">
                                                    <label class="input-group-text" onclick="show('cdp_date');"><i class="fa fa-calendar"></i></label>
                                                </span>
                                            </div>
                                            @error('cdp_date')
                                            <div class="has-danger">
                                                <div class="col-form-label">{{ $message }}</div>
                                            </div>
                                            @enderror
                                        </div>


                                        <div class="col-sm-3">
                                                <div class="form-group @error('cdp_amount') has-danger @enderror">
                                                    <label class="col-form-label">Monto del CDP<br><small>(Monto del CDP)</small></label>
                                                    <input type="text" id="cdp_amount" name="cdp_amount" value="{{ old('cdp_amount', $order->cdp_amount) }}" class="form-control cdp_amount autonumber" data-a-sep="." data-a-dec=",">
                                                    @error('cdp_amount')
                                                        <div class="col-form-label">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                        </div>

                                        {{-- SI MODALIDAD ES CVE- CVEIC O CVEDP CARGADO POR EL REQUIRIENTE --}}
                                        @if (in_array($order->modality->id, [9,10,28]))
                                            <div class="col-sm-2">
                                                <div class="form-group @error('unpostponable') has-danger @enderror">
                                                    <label style="font-size: 17px; font-weight: bold; color:red;background-color:yellow;" class="col-form-label">Urgencia impostergable<br><small>Urgencia impostergable</small></label>
                                                    <select id="unpostponable" name="unpostponable" class="form-control">
                                                        <option value="">Seleccionar</option>
                                                    @foreach (array(0 => 'NO', 1 => 'SI') as $index => $value)
                                                        <option value="{{ $index }}" @if ($index == old('unpostponable', $order->unpostponable)) selected @endif>{{ $value }}</option>
                                                    @endforeach
                                                    </select>
                                                    @error('unpostponable')
                                                        <div class="col-form-label">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        @endif
                                        <br><br>
                                        <div class="col-sm-12">
                                            <button class="btn btn-danger dropdown-toggle waves-effect" type="button" id="acciones" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Cambiar/Agregar Datos del Pedido</button>
                                            <div class="dropdown-menu" aria-labelledby="acciones" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                                    {{-- @if ((Auth::user()->hasPermission(['orders.orders.update']) && $order->dependency_id == Auth::user()->dependency_id && $order->actual_state == 1) || Auth::user()->hasPermission(['admin.orders.update'])) --}}
                                                @if ((Auth::user()->hasPermission(['orders.orders.update'])) || Auth::user()->hasPermission(['admin.orders.update']))
                                                    <a style="font-size: 15px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="{{ route('orders.edit', $order->id)}}">Editar Pedido</a>
                                                @endif
                                            </div>
                                            <label style="font-size: 15px; font-weight: bold; color:red;background-color:yellow;" class="col-form-label"> EDITAR PEDIDO PARA VERIFICAR MONTO TOTAL CON OGs Y PLURIANULIDAD<br></label>
                                        </div>
                                        <br>

                                        <div class="col-sm-12">
                                            <div class="form-group text-center">
                                                <button type="submit" class="btn btn-info m-b-0 f-12">Guardar datos</button>
                                            </div>
                                        </div>
                                    </form>
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

    $('#dncp_resolution_date').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    });

    $('#begin_date').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    });

    $('#cdp_date').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        // startDate: '-7d',
        endDate: '0d'
    });

    show = function(id){
        $('#'+id).datepicker('show');
    }
});
</script>
@endpush
