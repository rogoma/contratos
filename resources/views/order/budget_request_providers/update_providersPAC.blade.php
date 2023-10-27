@extends('layouts.app')

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-sitemap bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Solicitud de Presupuestos</h5>
                        <span>Editar Solicitud de Presupuesto</span>
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
                                    <h5>Editar Solicitud de Presupuesto a Empresas cotizantes</h5>
                                </div>
                                <div class="card-block">
                                    <form method="POST" action="{{ route('orders.budget_request_providers.update_providersPAC', $order->id) }}">

                                    {{-- @if (Auth::user()->hasPermission(['plannings.budget_request_providers.update']))
                                        <form method="POST" action="{{ route('orders.budget_request_providers.update_providersPAC', $order->id) }}">
                                    @else
                                        @if (Auth::user()->hasPermission(['admin.budget_request_providers.create']))
                                            <form method="POST" action="{{ route('orders.budget_request_providers.update_providers', $order->id) }}">
                                        @endif        
                                    @endif                                       --}}

                                        @csrf
                                        @method('PUT')

                                        {{-- @if($related_providers->count() > 0)
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Empresas ya relacionadas con ítems</label>
                                            <div class="col-sm-10">
                                                    @foreach ($related_providers as $provider)
                                                        <input type="text" class="form-control" value="{{ $provider->provider->description }}" readonly>
                                                        <input type="hidden" name="before_providers[]" value="{{ $provider->provider->id }}" readonly>
                                                    @endforeach
                                            </div>
                                        </div>
                                        @endif

                                        @if($other_dependencies->count() > 0)
                                        <div class="form-group row">
                                            <label class="col-sm-2 col-form-label">Empresas cargadas por otras dependencias</label>
                                            <div class="col-sm-10">
                                                    @foreach ($other_dependencies as $provider)
                                                        <input type="text" class="form-control" value="{{ $provider->provider->description }}" readonly>
                                                        <input type="hidden" name="before_providers[]" value="{{ $provider->provider->id }}" readonly>
                                                    @endforeach
                                            </div>
                                        </div>
                                        @endif --}}

                                        <div class="form-group row @error('providers') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Empresas</label>
                                            <div class="col-sm-10">
                                                <select id="providers" name="providers[]" class="form-control" multiple="multiple">                                                     
                                                        @foreach ($providers as $row)                                                                                                                        
                                                                <option value="{{ $row->id }}" @if (in_array($row->id, old('providers', $empresas_cotizantesPAC->pluck('provider_id')->toArray()))) selected @endif>{{ $row->description."  RUC:".$row->ruc }}</option>                                                            
                                                        @endforeach                                                    
                                                </select>
                                                @error('providers')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

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

    $('#providers').select2({ placeholder: "   --- Digite Nombre o RUC de la Empresa ---" });

});
</script>
@endpush