@extends('layouts.app')

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-sitemap bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Agregar Empresa Adjudicada</h5>
                        <span>Agregar Empresa</span>
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
                            <a href="{{ $orders_route }}">Pedido Nº {{ $order->id }}</a>
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
                                    <h5>Agregar Empresa Adjudicada del Llamado</h5>
                                    {{-- <h5>Agregar Empresa Adjudicada del Llamado</h5> --}}
                                    <h5>MONTO TOTAL DEL LLAMADO: Gs. {{ $order->totalAmountFormat() }}</h5>
                                    {{-- <h5>SALDO = : Gs. {{ $order->totalAmountFormat() }}</h5> --}}
                                </div>
                                <div class="card-block">
                                    <form method="POST" action="{{ route('orders.budget_request_providers.store_providers_awarded', $order->id) }}">
                                        @csrf

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
                                        
                                        <div class="form-group row @error('providers') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Empresa</label>
                                            <div class="col-sm-6">
                                                <select id="providers" name="providers" class="form-control">
                                                    <option value="">--- Seleccionar Empresa ---</option>
                                                    @foreach ($providers as $provider)
                                                        <option value="{{ $provider->id }}" @if ($provider->id == old('providers')) selected @endif>{{ $provider->description."  RUC:".$provider->ruc }}</option>
                                                    @endforeach
                                                </select>
                                                @error('providers')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('cc_number') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Código de Contratación</label>
                                            <div class="col-sm-2">
                                                <input type="text" id="cc_number" name="cc_number" value="{{ old('cc_number') }}" class="form-control">
                                                @error('cc_number')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('cc_date') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Fecha de C.C.</label>
                                            <div class="col-sm-2">                                                
                                                <input type="text" id="cc_date" name="cc_date" value="{{ old('cc_date', empty($order->cc_date) ? '' : date('d/m/Y', strtotime($order->cc_date))) }}" class="form-control" autocomplete="off">
                                                {{-- <span class="input-group-append" id="basic-addon">
                                                    <label class="input-group-text" onclick="show('cc_date');"><i class="fa fa-calendar"></i></label>
                                                </span> --}}

                                                @error('cc_date')
                                                <div class="has-danger">
                                                    <div class="col-form-label">{{ $message }}</div>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('monto_adjudica') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Monto de la Adjudicación</label>
                                            <div class="col-sm-4">
                                                {{-- <input type="number" id="monto_adjudica" name="monto_adjudica" value="{{ old('monto_adjudica') }}" class="form-control"> --}}
                                                <input type="text" id="monto_adjudica" name="monto_adjudica" value="{{ old('monto_adjudica')}}" class="form-control monto_adjudica autonumber" data-a-sep="." data-a-dec=","> 
                                                {{-- <span style="font-size: 16px; font-weight: bold; color:BLUE" >MONTO TOTAL DEL LLAMADO: {{ $order->totalAmountFormat() }}</span>   --}}
                                                @error('monto_adjudica')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>              
                                        
                                        <div class="form-group row @error('obs_contract') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Observación</label>
                                            <div class="col-sm-10">                                                                                                
                                                <input type="text  " id="obs_contract" name="obs_contract" value="{{ old('obs_contract') }}" class="form-control"> 

                                                @error('obs_contract')
                                                <div class="has-danger">
                                                    <div class="col-form-label">{{ $message }}</div>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2"></label>
                                            <div class="col-sm-10">
                                                <button type="submit" class="btn btn-primary m-b-0">Guardar</button>
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

{{-- @push('scripts')
<script type="text/javascript">
$(document).ready(function(){

    $('#providers').select2({ placeholder: "   --- Digite Nombre o RUC de la Empresa ---" });

});
</script> --}}
{{-- @endpush --}}

@push('scripts')
<script type="text/javascript">
$(document).ready(function(){

    $('#providers').select2({ placeholder: "   --- Digite Nombre o RUC de la Empresa ---" });

    // $('#dncp_resolution_date').datepicker({
    //     language: 'es',
    //     format: 'dd/mm/yyyy'
    // });

    // $('#begin_date').datepicker({
    //     language: 'es',
    //     format: 'dd/mm/yyyy'
    // });
   
    $('#cc_date').datepicker({
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