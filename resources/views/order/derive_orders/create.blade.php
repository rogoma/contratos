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
                        <span>Procesar Pedido</span>
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
                            <a href="{{ route('dgafs.show', $order->id) }}">Pedido Nº {{ $order->id }}</a>
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
                                    @if ($order->covid==0)                                                
                                        <div class="float-left f-w-700">Procesar Pedido Nº {{ $order->id }} <h5 class="label label-warning m-l-5">{{ $order->description }}</h5></div>
                                        {{-- <h5>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."/".$order->year."-".$order->description }}    --}}
                                    @else                                                
                                    <div class="float-left f-w-700">Procesar Pedido Nº {{ $order->id }} <h5 class="label label-warning m-l-5">{{ $order->description }}</h5></div>    
                                        {{-- <h5>{{ is_null($order->number)? $order->description : $order->modality->description." N° ".$order->number."/".$order->year."-".$order->description }}     --}}
                                        <label style="font-size: 16px; font-weight: bold; color:blue;background-color:yellow;">Proceso COVID</label></h5>
                                    @endif
                                </div>
                                <div class="card-block">
                                    <form id="formCreate" method="POST">
                                        <div class="form-group">
                                            <label class="col-form-label">Verificación del Pedido</label>
                                            <div class="col-sm-6">
                                                <select id="verification" name="verification" class="form-control">
                                                    @foreach (array('ACEPTAR', 'RECHAZAR') as $row)
                                                        <option value="{{ $row }}">{{ $row }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-form-label">Urgencia del Pedido</label>
                                            <div class="col-sm-6">
                                                <select id="urgency_state" name="urgency_state" class="form-control">
                                                    @foreach (array('BAJA', 'MEDIA', 'ALTA') as $row)
                                                        <option value="{{ $row }}" @if($row=='MEDIA') selected @endif>{{ $row }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>                                        

                                        <div class="form-group row @error('rejected_obs') has-danger @enderror">
                                            <label class="col-sm-10 col-form-label">Observación</label>
                                            <div class="col-sm-10">                                                                                                
                                                <input type="text  " id="rejected_obs" name="rejected_obs" value="{{ old('rejected_obs') }}" class="form-control @error('rejected_obs') form-control-danger @enderror" > 

                                                @error('rejected_obs')
                                                <div class="has-danger">
                                                    <div class="col-form-label">{{ $message }}</div>
                                                </div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div id="error" class="col-sm-12 d-none">
                                            <div class="alert alert-danger">
                                                <span id="error_message"></span>
                                            </div>
                                        </div>

                                        <div class="col-sm-6 text-center">
                                            <button type="submit" class="btn btn-primary m-b-0">Guardar</button>
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

    $('#formCreate').submit(function(e){
        e.preventDefault();
        $.ajax({
            url: "{{ route('derive_orders.store', $order->id) }}",
            method: 'POST',
            data: $('#formCreate').serialize()+'&_token={{ csrf_token() }}',
            success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        location.href = "{{ route('dgafs.index') }}";
                    }else{
                        swal("Error!", response.message, "error");
                    }
                }catch(error){
                    swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                    console.log(error);
                }
            },
            error: function(error){
                swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                console.log(error);
            }
        });
    });

});
</script>
@endpush