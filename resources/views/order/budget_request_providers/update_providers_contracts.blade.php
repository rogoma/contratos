@extends('layouts.app')

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-sitemap bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Cargar Contratos de Empresas adjudicadas</h5>
                        <span>Contratos de Empresas adjudicadas</span>
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
                                    <h5>Cargar contratos de Empresas adjudicadas</h5>
                                </div>
                                <div class="card-block">
                                    
                                    <form method="POST" action="{{ route('orders.budget_request_providers.update_providers_contracts', [$order->id, $budget->id]) }}">
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
                                                        <option disabled="true" value="{{ $provider->id}}" @if ($provider->id== old('providers', $budget->provider_id)) selected @endif>{{ $provider->description }} </option>                                                        
                                                    @endforeach
                                                </select>
                                                @error('providers')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('monto_adjudica') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Monto de la Adjudicación</label>
                                            <div class="col-sm-2">
                                                {{-- ORIGINAL --}}
                                                {{-- <input type="number" id="monto_adjudica" name="monto_adjudica" value="{{ old('monto_adjudica', $budget->monto_adjudica)}}" class="form-control @error('monto_adjudica') form-control-danger @enderror"> --}}                                                
                                                <input type="text" id="monto_adjudica" name="monto_adjudica" value="{{ old('monto_adjudica', $budget->monto_adjudica)}}" class="form-control monto_adjudica autonumber" data-a-sep="." data-a-dec="," readonly>
                                                                                                
                                                @error('monto_adjudica')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('contract_number') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Contrato N°</label>
                                            <div class="col-sm-2">
                                                <input type="text" id="contract_number" name="contract_number" value="{{ old('contract_number',$budget->contract_number) }}" class="form-control monto_contract autonumber" data-a-sep="." data-a-dec=",">                                                
                                                @error('contract_number')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('contract_date') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Fecha de Contrato</label>
                                            <div class="col-sm-2">                                                                                                
                                                <input type="text" id="contract_date" name="contract_date" value="{{ old('contract_date', empty($budget->contract_date) ? '' : date('d/m/Y', strtotime($budget->contract_date))) }}" class="form-control" autocomplete="off" >

                                                @error('contract_date')
                                                <div class="has-danger">
                                                    <div class="col-form-label">{{ $message }}</div>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('monto_contract') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Monto del Contrato</label>
                                            <div class="col-sm-2">
                                                {{-- ORIGINAL --}}
                                                {{-- <input type="number" id="monto_contract" name="monto_contract" value="{{ old('monto_contract', $budget->monto_contract)}}" class="form-control @error('monto_contract') form-control-danger @enderror"> --}}                                                
                                                <input type="text" id="monto_contract" name="monto_contract" value="{{ old('monto_contract', $budget->monto_contract)}}" class="form-control monto_contract autonumber" data-a-sep="." data-a-dec=",">
                                                                                                
                                                @error('monto_contract')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row @error('obs_contract') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Observación</label>
                                            <div class="col-sm-10">                                                                                                
                                                <input type="text  " id="obs_contract" name="obs_contract" value="{{ old('obs_contract',$budget->obs_contract) }}" class="form-control @error('obs_contract') form-control-danger @enderror" > 

                                                @error('obs_contract')
                                                <div class="has-danger">
                                                    <div class="col-form-label">{{ $message }}</div>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    
                                        {{-- @php
                                            var_dump($budget->provider_id);
                                            var_dump($budget->monto_contract);
                                        @endphp --}}                                       

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

    $('#contract_date').datepicker({
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