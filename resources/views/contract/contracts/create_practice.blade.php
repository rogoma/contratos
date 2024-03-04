<!-- Incluye las librerías de Datepicker y jQuery -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@extends('layouts.app')

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-file-text bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Llamados</h5>
                        <span>Agregar Llamados</span>
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
                                    <h5>Agregar Llamado</h5>
                                    <label id="fecha_actual" name="fecha_actual"  style="font-size: 20px;color: #FF0000;float: right;" for="fecha_actual">{{ Carbon\Carbon::now()->format('d/m/Y') }}</label>
                                    <label style="font-size: 20px;color: #FF0000;float: right;">FECHA: </label>
                                    

                                </div>
                                <div class="card-block">
                                    <div class="form-group">                                        

                                    <h3 class="text-center">Datos para cargar Llamados</h3>
                                    <form class="row" method="POST" action="{{ route('contracts.store') }}">
                                        @csrf

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
                                        
                                        <!-- Agrega estos enlaces en la sección head de tu archivo de diseño o la vista directamente -->
                                        {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css">
                                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                        <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script> --}}

                                        <div class="container">
                                            {{-- <h3 style="text-align: center;">Opciones de Pólizas</h3>                                             --}}

                                            {{-- <div class="form-group">
                                                <label for="fecha_inicio">Fecha de inicio:</label>
                                                <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="fecha_fin">Fecha de fin:</label>
                                                <input type="date" id="fecha_fin" name="fecha_fin" class="form-control">
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="diferencia_dias">Diferencia en días:</label>
                                                <input type="text" id="diferencia_dias" name="diferencia_dias" class="form-control" readonly>
                                            </div> --}}

                                            <div class="row">
                                                {{-- #13 ANTICIPO--}}
                                                <div class="col-md-3">
                                                        <label class="col-form-label @error('advance_validity_from') has-danger @enderror">Anticipo-Vigencia Desde</label>
                                                        <div class="input-group @error('advance_validity_from') has-danger @enderror">
                                                            <input type="text" id="advance_validity_from" name="advance_validity_from" value="{{ old('advance_validity_from') }}" class="form-control text-align: left" autocomplete="off">
                                                            <span class="input-group-append" id="basic-addon">
                                                                <label class="input-group-text" onclick="show('advance_validity_from');"><i class="fa fa-calendar"></i></label>
                                                            </span>
                                                        </div>
                                                        @error('advance_validity_from')
                                                        <div class="has-danger">
                                                            <div class="col-form-label">{{ $message }}</div>
                                                        </div>
                                                        @enderror
                                                </div>
                                                {{-- #14 ANTICIPO--}}
                                                <div class="col-md-3">
                                                        <label class="col-form-label @error('advance_validity_to') has-danger @enderror">Anticipo-Vigencia Hasta</label>
                                                        <div class="input-group @error('advance_validity_to') has-danger @enderror">
                                                            <input type="text" id="advance_validity_to" name="advance_validity_to" value="{{ old('advance_validity_to') }}" class="form-control" autocomplete="off">
                                                            <span class="input-group-append" id="basic-addon">
                                                                <label class="input-group-text" onclick="show('advance_validity_to');"><i class="fa fa-calendar"></i></label>
                                                            </span>
                                                        </div>
                                                        @error('advance_validity_to')
                                                        <div class="has-danger">
                                                            <div class="col-form-label">{{ $message }}</div>
                                                        </div>
                                                        @enderror
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group @error('control_1') has-danger @enderror">
                                                        <label class="col-form-label">Días Vigencia</label>
                                                        <input type="text" id="control_1" disabled="disabled" name="control_1" class="form-control">
                                                    </div>    
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group @error('control_a') has-danger @enderror">
                                                        <label class="col-form-label">Días para Vencer</label>
                                                        <input type="text" id="control_a" disabled="disabled" name="control_a" class="form-control">
                                                    </div>    
                                                </div>

                                                {{-- <div class="col-md-3">
                                                    <label for="diferenciaDias">Diferencia de Días:</label>
                                                    <input type="text" id="diferenciaDias" name="diferenciaDias" readonly>
                                                    <button type="button" onclick="calcularDiferencia()">Calcular</button>
                                                </div> --}}
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

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

@push('scripts')
<script type="text/javascript">

$(document).ready(function(){

    $('#advance_validity_from').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });
    $('#advance_validity_to').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });

    show = function(id){
        $('#'+id).datepicker('show');
    }

    //VALIDACIÓN DE FECHAS DE ANTICIPOS
    $('#advance_validity_from').on('changeDate', function() {        
        var fechaInicio = $(this).datepicker('getDate');
        var fechaFin = $('#advance_validity_to').datepicker('getDate');
        

        if (fechaFin == null){

        }else{    
            if (fechaInicio > fechaFin) {
                alert('La fecha de inicio no puede ser mayor a la fecha final.');
                $('#advance_validity_to').datepicker('date', null); // Limpiar el datapicker
                $('#advance_validity_to').val('');
                $('#control_1').val('');         
            }else{

                //controla días para vigencia
                restaFechas = function(f1,f2)
                {
                    var aFecha1 = f1.split('/'); 
                    var aFecha2 = f2.split('/'); 
                    var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1]-1,aFecha1[0]); 
                    var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1]-1,aFecha2[0]); 
                    var dif = fFecha2 - fFecha1;
                    var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
                    return dias;
                }               
                
                $('#control_1').val(restaFechas(f1,f2));              
                
            }
        }   
    });

    $('#advance_validity_to').on('changeDate', function() {        
        var fechaInicio = $('#advance_validity_from').datepicker('getDate');
        var fechaFin = $(this).datepicker('getDate');
        
        if (fechaInicio > fechaFin) {
            alert('La fecha de inicio no puede ser mayor a la fecha final.');
            $('#advance_validity_to').datepicker('date', null); // Limpiar el datapicker
            $('#advance_validity_to').val('');        
        }else{
            
            ///calcula dias de vigencia
            restaFechas = function(f1,f2)
            {
                var aFecha1 = f1.split('/'); 
                var aFecha2 = f2.split('/');                  
                var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1]-1,aFecha1[0]); 
                var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1]-1,aFecha2[0]); 
                var dif = fFecha2 - fFecha1;
                var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
                return dias;
            }

            ///calcula dias que faltan para vencer
            restaFechas2 = function(f2,f3)
            {
                var aFecha1 = f3.split('/'); 
                var aFecha2 = f2.split('/');                  
                var fFecha1 = Date.UTC(aFecha1[2],aFecha1[1]-1,aFecha1[0]); 
                var fFecha2 = Date.UTC(aFecha2[2],aFecha2[1]-1,aFecha2[0]); 
                var dif = fFecha2 - fFecha1;
                var dias = Math.floor(dif / (1000 * 60 * 60 * 24)); 
                return dias;
            }

            var f1 = $('#advance_validity_from').val();//fecha dtpicker inicio
            var f2=  $('#advance_validity_to').val(); //fecha dtpicker final
            var f3= $('#fecha_actual').text();//fecha actual
           
            $('#control_1').val(restaFechas(f1,f2));//resultado fecha vigencia
            $('#control_a').val(restaFechas2(f2,f3));//resultado fecha días para vencer
        }             
    });

    
});
</script>
@endpush