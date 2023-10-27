@extends('layouts.app')

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-sitemap bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Pedidos</h5>
                        <span>Consultas</span>
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
                            <a href="{{ route('minor_purchases.show', $order->id) }}">Pedido Nº {{ $order->id }}</a>
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
                                <div class="col-sm-8 text-left">                                    
                                    <h5>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."-".$order->description }}<label class="label label-info m-l-5">Prioridad {{ $order->urgency_state }}</label></h5>
                                    <h5>SIMESE: {{ number_format($order->simese->first()['simese'],'0', ',','.') }} </h5>
                                </div>
                                <div class="card-header">
                                    <h5>Cargar Consulta</h5>
                                </div>
                                <div class="card-block">
                                    <form method="POST" action="{{ route('minor_purchases.queries.store', $order->id) }}">
                                    @csrf

                                        <div class="form-group @error('query_date') has-danger @enderror">
                                            <label class="col-form-label">Fecha de Consulta</label>
                                            <input type="text" id="query_date" name="query_date" value="{{ old('query_date', date('d/m/Y')) }}" class="form-control" autocomplete="off">
                                            @error('query_date')
                                                <div class="col-form-label">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group @error('query') has-danger @enderror">
                                            <label class="col-form-label">Descripción</label>
                                            <textarea id="query" name="query" class="form-control">{{ old('query') }}</textarea>
                                            @error('query')
                                                <div class="col-form-label">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-12 text-center">
                                            <button id="guardar" type="submit" class="btn btn-primary m-b-0">Guardar</button>
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
    $('#query_date').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    });
});
</script>
@endpush