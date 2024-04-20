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
.columna1 { width: 1%; text-align: center;}
.columna2 { width: 10%; text-align: left;}
.columna3 { width: 9%; text-align: left;}
.columna4 { width: 16%; text-align: left;}
.columna5 { width: 2%; text-align: center;}
.columna6 { width: 4%; text-align: center;}
.columna7 { width: 4%; text-align: center;}
.columna8 { width: 3%; text-align: center;}
.columna9 { width: 3%; text-align: left;}
.columna10 { width: 3%; text-align: center;}
.columna11 { width: 9%; text-align: left;}
.columna12 { width: 10%; text-align: left;}

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
                        <h5>Visualizar Llamado</h5>
                        <span>Llamado Nº {{ $contract->number_year }}</span>
                        <br><br>
                        {{-- <h6>Estado Actual: {{ $contract->contractstate->id." - ".$contract->contractstate->description }}</h6> --}}
                        <h5><p style="font-size: 17px; font-weight: bold; color:#FF0000">Estado Actual: {{ $contract->contractState->id." - ".$contract->contractState->description }}</p></h5>
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
                                    <div class="row">
                                        <div class="col-sm-10 text-left">
                                            <h5>Llamado: {{ $contract->description." - ".$contract->modality->description." N° ".$contract->number_year." - ".$contract->provider->description }}
                                            {{-- @if ($contract->covid==0)
                                                <h5>{{ is_null($contract->number)? $contract->description : $contract->modality->description." N° ".$contract->number."/".$contract->year."-".$contract->description }}
                                            @else
                                                <h5>{{ is_null($contract->number)? $contract->description : $contract->modality->description." N° ".$contract->number."/".$contract->year."-".$contract->description }}
                                                <label style="font-size: 16px; font-weight: bold; color:blue;background-color:yellow;">Proceso COVID</label></h5>
                                            @endif  --}}

                                            {{-- PARA MOSTRAR SI ES COVID Y SI ES URGENCIA IMPOSTERGABLE --}}


                                        </div>
                                            <div class="col-sm-2">

                                            @if (in_array($contract->contract_state_id, [1,3, 2]))
                                                <button class="btn btn-primary dropdown-toggle waves-effect" type="button" id="acciones" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Acciones</button>
                                            @endif
                                            <div class="dropdown-menu" aria-labelledby="acciones" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">

                                                {{-- Verificamos permisos de edición del usuario --}}
                                                @if ((Auth::user()->hasPermission(['contracts.contracts.update']) && $contract->contract_state_id >= 1) || Auth::user()->hasPermission(['admin.contracts.update']))
                                                    <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="{{ route('contracts.edit', $contract->id)}}">Editar Pedido</a>
                                                @endif

                                                {{-- Verificamos permisos de eliminación del usuario --}}
                                                {{-- @if ((Auth::user()->hasPermission(['contracts.contracts.delete']) && $contract->dependency_id == Auth::user()->dependency_id && $contract->contract_state_id >= 1) || Auth::user()->hasPermission(['admin.contracts.delete']))
                                                <a style="font-size: 14px; font-weight: bold; color:white;background-color:red;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="anulecontract({{ $contract->id }})">Anular Pedido</a>
                                                @endif --}}

                                                {{-- Verificamos que el pedido tenga estado 1 y Verificamos que el pedido tenga ítems, y que tenga SIMESE--}}
                                                {{-- @if (Auth::user()->hasPermission(['contracts.contracts.derive']) && $contract->contract_state_id == 1 && $contract->items->count() > 0 && count($related_simese_user) <> 0)
                                                    <a style="font-size: 14px; font-weight: bold; color:blue;background-color:lightblue;" class="dropdown-item waves-effect f-w-600" href="javascript::void(0);" onclick="derivecontract({{ $contract->id }});">Derivar Pedido a DGAF</a>
                                                @endif --}}

                                                {{-- Verificamos permisos de derivación del pedido y que el pedido tenga estado PROCESADO PEDIDO --}}
                                                @if (Auth::user()->hasPermission(['derive_contracts.contracts.derive']) && $contract->contract_state_id == 4)
                                                <a class="dropdown-item waves-effect f-w-600" href="{{ route('derive_contracts.create', $contract->id) }}">Procesar Pedido en DGAF</a>
                                                @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-block">

                                    <ul class="nav nav-tabs md-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#tab1" role="tab"><i class="fa fa-tasks"></i> Datos del Llamado</a>
                                            <div class="slide"></div>
                                        </li>
                                        {{-- <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab3" role="tab"><i class="fa fa-list"></i> Ítems adjudicados</a>
                                            <div class="slide"></div>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab2" role="tab"><i class="fa fa-briefcase"></i> Empresas adjudicadas</a>
                                            <div class="slide"></div>
                                        </li> --}}

                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab6" role="tab"><i class="fa fa-file-archive-o"></i> Archivos (Anteced.)</a>
                                            <div class="slide"></div>
                                        </li>
                                    </ul>

                                    <div class="tab-content card-block">
                                        <div class="tab-pane active" id="tab1" role="tabpanel">
                                            <h5 class="text-center">Datos del LLAMADO</h5>
                                            <table class="table table-striped table-bcontracted">
                                                <tbody>
                                                    <tr>
                                                        <td><label class="col-form-label f-w-600" >Nombre del Llamado:</label></td>
                                                        <td><label class="col-form-label f-w-600" >Tipo Llamado:</label></td>
                                                        <td><label class="col-form-label f-w-600">IDDNCP:</label></td>
                                                        <td><label class="col-form-label f-w-600">Link DNCP:</label></td>
                                                        <td><label class="col-form-label f-w-600">N° Contrato/Año:</label></td>
                                                        <td><label class="col-form-label f-w-600">AÑO:</label></td>
                                                        <td><label class="col-form-label f-w-600">Fecha firma contrato:</label></td>
                                                    </tr>
                                                    <tr>
                                                        <td>{{ $contract->description }}</td>
                                                        <td>{{ $contract->modality->description }}</td>
                                                        <td> {{ number_format($contract->iddncp,'0', ',','.') }} </td>
                                                        <td style="color:blue">{{ $contract->linkdncp }}</td>
                                                        <td>{{ $contract->number_year }}</td>
                                                        <td> {{ number_format($contract->year_adj,'0', ',','.') }} </td>
                                                        <td>{{ $contract->signDateFormat() }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><label class="col-form-label f-w-600">Contratista:</label></td>
                                                        <td><label class="col-form-label f-w-600">Estado:</label></td>
                                                        <td><label class="col-form-label f-w-600">Modalidad:</label></td>
                                                        <td><label class="col-form-label f-w-600">Organismo Finaciador:</label></td>
                                                        <td><label class="col-form-label f-w-600">Tipo de Contrato:</label></td>
                                                        <td><label class="col-form-label f-w-600">Monto Total:</label></td>

                                                    </tr>
                                                    <tr>
                                                        <td>{{ $contract->provider->description }}</td>
                                                        <td>{{ $contract->contractState->description }}</td>
                                                        <td>{{ $contract->modality->description }}</td>
                                                        <td>{{ $contract->financialOrganism->description }}</td>
                                                        <td>{{ $contract->contractType->description }}</td>
                                                        <td colspan="2" style="font-size: 16px;color:blue;font-weight: bold">{{ 'Gs. '.$contract-> totalAmountFormat() }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="tab-pane" id="tab6" role="tabpanel">
                                            <label class="col-form-label f-w-600">Archivos cargados al llamado:</label>
                                            <table class="table table-striped table-bcontracted">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Descripción</th>
                                                        <th>Dependencia</th>
                                                        <th>Fecha/Hora</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @for ($i=0; $i < count($other_files); $i++)
                                                    <tr>
                                                        <td>{{ $i+1 }}</td>
                                                        <td>{{ $other_files[$i]->description }}</td>
                                                        <td>{{ $other_files[$i]->dependency->description }}</td>
                                                        <td>{{ $other_files[$i]->updated_atDateFormat() }}</td>
                                                        <td>
                                                            <a href="{{ asset('storage/files/'.$other_files[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                            <a href="{{ route('contracts.files.download', $other_files[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endfor
                                                        @for ($i=0; $i < count($user_files); $i++)
                                                        <tr>
                                                            <td>{{ $i+1 }}</td>
                                                            <td>{{ $user_files[$i]->description }}</td>
                                                            <td>{{ $user_files[$i]->dependency->description }}</td>
                                                            <td>{{ $user_files[$i]->updated_atDateFormat() }}</td>
                                                            <td>
                                                                <a href="{{ asset('storage/files/'.$user_files[$i]->file) }}" title="Ver Archivo" target="_blank" class="btn btn-primary"><i class="fa fa-eye"></i></a>
                                                                <a href="{{ route('contracts.files.download', $user_files[$i]->id) }}" title="Descargar Archivo" class="btn btn-info"><i class="fa fa-download"></i></a>
                                                                <button title="Eliminar Archivo" onclick="deleteFile({{ $user_files[$i]->id }})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                                            </td>
                                                        </tr>
                                                        @endfor
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                <a href="{{ route('contracts.files.create', $contract->id) }}" class="btn btn-primary">Cargar Archivos</a>
                                            </div>
                                                {{-- Mostrar esto una vez que Adjudicaciones haya notificado al Oferente --}}
                                                {{-- @if ($contract->actual_state == 65)
                                                    <div class="col-sm-10 text-center">
                                                        <h5><p style="font-size: 18px; font-weight: bold; color:#FF0000;background-color:yellow">Adjuntar: Nota de Adjudicación</p></h5>
                                                    </div>
                                                @endif --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                                    </div>
                                </div>
                            </div>

                            {{-- MOSTRAMOS TIMELINE --}}
                            {{-- <div class="card latest-update-card">
                                <div class="card-header">
                                    <h5 style="font-size: 14px; font-weight: bold; color:red" class="text-left">Movimientos del pedido</h5>
                                </div>
                                <div class="card-block">
                                    <div class="latest-update-box">
                                    @foreach ($contract->contractscontractstates()->contractBy('id', 'desc')->get() as $item)
                                        <div class="row p-t-20 p-b-30 borde-alternado">
                                            <div class="col-auto text-right update-meta p-r-0">
                                                <i class="update-icon ring"></i>
                                            </div>
                                            <div class="col p-l-5">
                                                <a href="javascript:void(0);">
                                                    <h6 style="font-size: 14px; font-weight: bold; color:Black">{{ $item->contractstate->id}}-{{ $item->contractstate->description}}</h6>
                                                </a>
                                                <p style="font-size: 14px; font-weight: bold; color:Black">{{ $item->creatorUser->getFullName()}}</p>
                                                <small style="font-size: 13px; font-weight: bold; color:Black">Fecha: {{ $item->createdAtDateFormat()}}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                            </div> --}}
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

    updateContracts = function(budget){
        // persistirTab();
        location.href = '/contracts/{{ $contract->id }}/items_budget/'+budget+'/edit/';

        // location.href = '/contracts/{{ $contract->id }}/budget_request_providers/'+budget+'/edit_providers_contracts/';

        //  /items_budget = Route::resource('contracts.items_budget', BudgetRequestProvidersController::class);
        //  /edit = public function edit(Request $request, $contract_id) de BudgetRequestProvidersController
    }

    recibecontract = function(contract_id){
        $.ajax({
            url : '/contracts/recibe_contract/'+contract_id,
            method: 'POST',
            data: '_token='+'{{ csrf_token() }}',
            success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        swal({
                            title: "Exito!",
                            text: response.message,
                            type: "success"
                        },
                        function(isConfirm){
                            location.reload();
                        });
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
    }

    derivecontract = function(contract_id){
        $.ajax({
            url : '/contracts/derive_contract/'+contract_id,
            method: 'POST',
            data: '_token='+'{{ csrf_token() }}',
            success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        swal({
                            title: "Exito!",
                            text: response.message,
                            type: "success"
                        },
                        function(isConfirm){
                            location.reload();
                        });
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
    }

    itemAwardHistories = function(item){
        location.href = '/items/'+item+'/item_contract_histories';
    }

    deleteFile = function(file){
      swal({
            title: "Atención",
            text: "Está seguro que desea eliminar el registro?",

            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        },
        function(isConfirm){
          if(isConfirm){
            $.ajax({
              url : '/contracts/files/'+file+'/delete/',
              method : 'POST',
              data: {_method: 'DELETE', _token: '{{ csrf_token() }}'},
              success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        location.reload();
                    }else{
                        swal("Error!", response.message, "error");
                    }
                }catch(error){
                    swal("Error!", "Ocurrió un error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                    console.log(error);
                }
              },
              error: function(error){
                swal("Error!", "Ocurrió 1 error intentado resolver la solicitud, por favor complete todos los campos o recargue de vuelta la pagina", "error");
                console.log(error);
              }
            });
          }
        }
      );
    };

    deleteObjection = function(objection){
      swal({
            title: "Atención",
            text: "Está seguro que desea eliminar el registro?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        },
        function(isConfirm){
          if(isConfirm){
            $.ajax({
              url : '/contracts/{{ $contract->id }}/objections/'+objection,
              method : 'POST',
              data: {_method: 'DELETE', _token: '{{ csrf_token() }}'},
              success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        location.reload();
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
          }
        }
      );
    };

    deleteObjectionResponse = function(objection, objection_response){
      swal({
            title: "Atención",
            text: "Está seguro que desea eliminar el registro?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        },
        function(isConfirm){
          if(isConfirm){
            $.ajax({
              url : '/contracts/'+objection+'/objections_responses/'+objection_response,
              method : 'POST',
              data: {_method: 'DELETE', _token: '{{ csrf_token() }}'},
              success: function(data){
                try{
                    response = (typeof data == "object") ? data : JSON.parse(data);
                    if(response.status == "success"){
                        location.reload();
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
          }
        }
      );
    };

});
</script>
@endpush
