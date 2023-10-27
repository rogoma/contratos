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
                            <a href="{{ route('exceptions.show', $query->order_id) }}">Pedido Nº {{ $query->order_id }}</a>
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
                                    <h5>Modificar Consulta</h5>
                                </div>
                                <div class="card-block">
                                    <form method="POST" action="{{ route('exceptions.queries.update', [$query->order_id, $query->id]) }}">
                                    @csrf
                                    @method('PUT')

                                        <div class="form-group @error('query_date') has-danger @enderror">
                                            <label class="col-form-label">Fecha de Consulta</label>
                                            <input type="text" id="query_date" name="query_date" value="{{ old('query_date', empty($query->query_date) ? '' : date('d/m/Y', strtotime($query->query_date))) }}" class="form-control" autocomplete="off">
                                            @error('query_date')
                                                <div class="col-form-label">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group @error('query') has-danger @enderror">
                                            <label class="col-form-label">Descripción</label>
                                            <textarea id="query" name="query" class="form-control">{{ old('query', $query->query) }}</textarea>
                                            @error('query')
                                                <div class="col-form-label">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-sm-12 text-center">
                                            <button id="guardar" type="submit" class="btn btn-warning m-b-0">Modificar</button>
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