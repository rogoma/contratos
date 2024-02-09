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
.columna1 { width: 3%; text-align: center;}
.columna2 { width: 70%; text-align: left;}
.columna3 { width: 10%; text-align: center;}
.columna4 { width: 10%; text-align: left;}
.columna5 { width: 2%; text-align: center;}
/* .columna6 { width: 4%; text-align: center;}
.columna7 { width: 4%; text-align: center;}
.columna8 { width: 3%; text-align: center;}
.columna9 { width: 3%; text-align: left;}
.columna10 { width: 3%; text-align: center;}
.columna11 { width: 9%; text-align: left;}
.columna12 { width: 10%; text-align: left;} */

p.centrado {

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
                        <span>Listado de Llamados de Licitaciones</span>
                            <a href="pdf/panel_uta" class="btn btn-primary" target="_blank">LLAMADOS EN CURSO</a>
                            <a href="pdf/panel_uta2" class="btn btn-primary" target="_blank">LLAMADOS ADJUDICADOS</a>
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
                            <a href="{{ route('contracts.index') }}">Llamados</a>
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
                                        <h5>Listado de Llamados de Licitaciones</h5>
                                    </div>
                                </div>
                                <div class="card-block">
                                    <div class="dt-responsive table-responsive">
                                        <table id="orders" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    {{-- <th>Modalidad</th> --}}
                                                    <th>Llamado</th>
                                                    {{-- <th>Dependencia</th> --}}
                                                    <th>PAC ID</th>
                                                    <th>Link DNCP</th>
                                                    <th>Monto total LLAMADO</th>
                                                    {{-- <th>Prioridad</th>
                                                    <th>N° CDP - FECHA</th>
                                                    <th>Monto CDP</th>
                                                    <th>Monto ADJUD.</th>
                                                    <th>Estado</th> --}}
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            @for ($i = 0; $i < count($orders); $i++)
                                                <tr>
                                                    <td class="columna1">{{ ($i+1) }}</td>
                                                    <td class="columna2">{{ $orders[$i]->description }}</td>
                                                    <td class="columna3"> {{ number_format($orders[$i]->iddncp,'0', ',','.') }} </td>
                                                    <td class="columna4"></td> <!-- Deja este td vacío para ser llenado por DataTables -->
                                                    {{-- <td class="columna4">{{ $orders[$i]->linkdncp }}</td> --}}
                                                    {{-- <td class="columna4"><link href="{{ $orders[$i]->linkdncp}}"></td> --}}
                                                    {{-- <td><link rel="icon" href="{{ $orders[$i]->linkdncp}}" type="image/x-icon"></td> --}}
                                                    {{-- <link rel="icon" href="{{ $orders[$i]->linkdncp }}" type="image/x-icon"> --}}


                                                    <td class="columna5"> Gs.{{ number_format($orders[$i]->total_amount,'0', ',','.') }} </td>

                                                    <td>
                                                        <a href="{{ route('contracts.show', $orders[$i]->id) }}" class="btn btn-primary">Ver Más</a>
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

$(document).ready(function() {
            $('#orders').DataTable({
                "columnDefs": [
                    {
                        "targets": 3, // Índice de la columna que deseas personalizar
                        "render": function (order, type, row, meta) {
                            // Puedes personalizar el contenido de la columna aquí
                            return '<a href="' + order + '">Enlace</a>'; // Suponiendo que el campo a enlazar está en el índice 2
                        }
                    }
                ]
            });
        });
</script>
@endpush
