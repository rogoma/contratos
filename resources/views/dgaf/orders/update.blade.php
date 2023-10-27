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
                        <span>Cargar datos Llamado</span>
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
                            <a href="{{ route('dgafs.index') }}">Llamados</a>
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
                                <div class="card-block">
                                    <form class="row" method="POST" action="{{ route('dgafs.orders.update', $order->id) }}">
                                        @csrf
                                        @method('PUT')

                                        <div class="col-sm-6">
                                            <div class="form-group @error('number') has-danger @enderror">
                                                <label class="col-form-label">Llamado <br><small>(Nro de llamado)</small></label>
                                                <input type="number" id="number" name="number" value="{{ old('number', $order->number) }}" class="form-control">
                                                @error('number')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group @error('dncp_pac_id') has-danger @enderror">
                                                <label class="col-form-label">PAC ID <br><small>(ID del PAC)</small></label>
                                                <input type="number" id="dncp_pac_id" name="dncp_pac_id" value="{{ old('dncp_pac_id', $order->dncp_pac_id) }}" class="form-control">
                                                @error('dncp_pac_id')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group @error('dncp_resolution_number') has-danger @enderror">
                                                <label class="col-form-label">Resolución DNCP Nº</label>
                                                <input type="text" id="dncp_resolution_number" name="dncp_resolution_number" value="{{ old('dncp_resolution_number', $order->dncp_resolution_number) }}" class="form-control">
                                                @error('dncp_resolution_number')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <label class="col-form-label @error('dncp_resolution_date') has-danger @enderror">Fecha de la Resolución DNCP</label>
                                            <div class="input-group @error('dncp_resolution_date') has-danger @enderror">
                                                <input type="text" id="dncp_resolution_date" name="dncp_resolution_date" value="{{ old('dncp_resolution_date', date('d/m/Y', strtotime($order->dncp_resolution_date))) }}" class="form-control" autocomplete="off">
                                                <span class="input-group-append" id="basic-addon">
                                                    <label class="input-group-text" onclick="show('dncp_resolution_date');"><i class="fa fa-calendar"></i></label>
                                                </span>
                                                @error('dncp_resolution_date')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                            <div class="form-group text-center">
                                                <button type="submit" class="btn btn-warning m-b-0 f-12">Guardar datos</button>
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

});
</script>
@endpush