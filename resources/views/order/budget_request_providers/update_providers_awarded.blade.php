@extends('layouts.app')

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-sitemap bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Editar Empresa adjudicada</h5>
                        <span>Editar Empresa adjudicada</span>
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
                                    <h5>Editar Empresa adjudicada</h5>
                                </div>
                                <div class="card-block">
                                    
                                    <form method="POST" action="{{ route('orders.items_budget.update', [$order->id, $budget->id]) }}">
                                    {{-- <form method="POST" action="{{ route('orders.budget_request_providers.update_providers_awarded', [$order->id, $budget->id]) }}">      --}}
                                                                               
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

                                        <div class="form-group row @error('provider_id') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Empresa</label>
                                            <div class="col-sm-6">
                                                <select id="provider_id" name="provider_id" class="form-control">
                                                    <option value="">--- Seleccionar Empresa ---</option>
                                                    @foreach ($providers as $provider)
                                                        <option value="{{ $provider->id}}" @if ($provider->id== old('providers', $budget->provider_id)) selected @endif>{{ $provider->description }}</option>                                                        
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
                                                <input type="text  " id="cc_number" name="cc_number" value="{{ old('cc_number',$budget->cc_number) }}" class="form-control @error('cc_number') form-control-danger @enderror" >
                                                {{-- <input type="number" id="batch    " name="batch    " value="{{ old('batch    ', $item->batch     ) }}" class="form-control @error('batch'    ) form-control-danger @enderror" readonly> --}}
                                                @error('cc_number')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('cc_date') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Fecha de C.C.</label>
                                            <div class="col-sm-2">                                                
                                                {{-- <input type="text" id="cc_date" name="cc_date" value="{{ old('cc_date',$budget->cc_date  ) }}" class="form-control @error('cc_number') form-control-danger @enderror"> --}}
                                                <input type="text" id="cc_date" name="cc_date" value="{{ old('cc_date', empty($budget->cc_date) ? '' : date('d/m/Y', strtotime($budget->cc_date))) }}" class="form-control" autocomplete="off" >
                                                
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
                                            <div class="col-sm-2">
                                                {{-- ORIGINAL --}}
                                                {{-- <input type="number" id="monto_adjudica" name="monto_adjudica" value="{{ old('monto_adjudica', $budget->monto_adjudica)}}" class="form-control @error('monto_adjudica') form-control-danger @enderror"> --}}                                                
                                                <input type="text" id="monto_adjudica" name="monto_adjudica" value="{{ old('monto_adjudica', $budget->monto_adjudica)}}" class="form-control monto_adjudica autonumber" data-a-sep="." data-a-dec=",">
                                                                                                
                                                @error('monto_adjudica')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('obs_award') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Observación</label>
                                            <div class="col-sm-10">                                                                                                
                                                <input type="text  " id="obs_award" name="obs_award" value="{{ old('obs_award',$budget->obs_award) }}" class="form-control @error('obs_award') form-control-danger @enderror" > 

                                                @error('obs_award')
                                                <div class="has-danger">
                                                    <div class="col-form-label">{{ $message }}</div>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        
                                        {{-- <div class="form-group @error('cdp_amount') has-danger @enderror">
                                                <label class="col-form-label">Monto del CDP<br><small>(Monto del CDP)</small></label>                                                
                                                <input type="text" id="cdp_amount" name="cdp_amount" value="{{ old('cdp_amount', $order->cdp_amount) }}" class="form-control cdp_amount autonumber" data-a-sep="." data-a-dec=",">
                                                @error('cdp_amount')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror                                               
                                        </div> --}}
                                    

                                        <div class="form-group row">
                                            <label class="col-sm-2"></label>
                                            <div class="col-sm-10">
                                                <button type="submit" class="btn btn-warning m-b-0">Modificar</button>
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

    $('#provider_id').select2({ placeholder: "   --- Seleccionar Empresa/s ---" });

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