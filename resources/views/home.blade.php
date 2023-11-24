@extends('layouts.app')

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="feather icon-shield bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Página de inicio</h5>
                        <span>Bienvenido</span>
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
                            <a href="{{ route('home') }}">Página de inicio</a>
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
                                    <h5>Bienvenido: {{ Auth::user()->getFullName() }}</h5>
                                </div>
                                <div class="card-block">
                                    <p>
                                        <span class="f-w-600">Dependencia:</span><br>
                                        {{ Auth::user()->dependency->description }}
                                    </p>
                                    <p>
                                        <span class="f-w-600">Rol:</span><br>
                                        {{ Auth::user()->role->description }}
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <p class="p-t-30">
                                        SENASA-Sistema de Gestión de Contratos<br>
                                        {{-- SENASA<br> --}}
                                        Ministerio de Salud Pública y Bienestar Social.<br>
                                        <small>&copy; {{date('Y')}} SENASA - MSPBS</small>
                                    </p>
                                    <br>
                                        {{-- Muestra vídeos de capacitación sólo para los roles Pedidos --}}
                                        {{-- @if ((Auth::user()->role->id == 2))
                                            Vídeos de capacitación: <a href="https://drive.google.com/file/d/1AKuAno8y8wEKBh9fQHPTR_IPeCBe2dzF/view?usp=sharing" class="btn btn-primary" target="_blank">Video1</a>     <a href="https://drive.google.com/file/d/1QmJa_hRIPe6AhqUPIGsYej-q4rpsMMXi/view?usp=sharing" class="btn btn-primary" target="_blank">Video2</a>
                                        @endif     --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @foreach ($usuariosArray as $usuario)
        <div>
            {{-- <ul class="list-group mt-2 mb-4"> --}}
                <li class="list-group-item active">USUARIO: {{$usuario['name']}}</li>
                <li class="list-group-item">MAIL: {{$usuario['email']}}</li>
                <li class="list-group-item">DIRECCIÓN: {{$usuario['address']['street']}}</li>

                <td>{{ is_null($usuario['address']['street']->first()) ? '' : number_format($orders[$i]->simese->last()['simese'],'0', ',','.') }}</td>
                {{-- <td>{{ is_null($usuario['address']['street']->first()) ? '' : number_format($orders[$i]->simese->last()['simese'],'0', ',','.') }}</td> --}}

                @if ($usuario['address']['zipcode'] == '92998-3874')
                    CODIGO 92998-3874
                @else
                    OTRO CODIGO
                @endif

                <li class="list-group-item">CÓDZIP: {{$usuario['address']['zipcode']}}</li>
                <li class="list-group-item">TELÉFONO: {{$usuario['phone']}}</li>
                {{-- <li class="list-group-item">{{$usuario['zipcode']}}</li> --}}
                <li class="list-group-item">WEB: {{$usuario['website']}}</li>
            {{-- </ul> --}}

            {{-- <tr>
                <td>{{ ($i+1) }}</td>
                <td>{{ $orders[$i]->modality->description }}</td>
                @if ($orders[$i]->covid==0)
                    <td>{{ is_null($orders[$i]->number)? $orders[$i]->description : $orders[$i]->modality->code." N° ".$orders[$i]->number."/".$orders[$i]->year." - ".$orders[$i]->description }}</td>
                @else
                    <td>{{ is_null($orders[$i]->number)? $orders[$i]->description : $orders[$i]->modality->code." N° ".$orders[$i]->number."/".$orders[$i]->year." - ".$orders[$i]->description}} - <span style="color:red;font-weight: bold"> (PROCESO COVID)</span></td>
                @endif
                {{-- <td>{{ is_null($orders[$i]->simese->first()) ? '' : number_format($orders[$i]->simese->last()['simese'],'0', ',','.') }}</td> --}}
                {{-- <td>{{ is_null($orders[$i]->simese->first()) ? '' : number_format($orders[$i]->simese->first()['simese'],'0', ',','.')."/".$orders[$i]->simese->first()['year'] }}</td> --}}
            {{-- </tr> --}}
        </div>
        @endforeach
    </div>
</div>
@endsection
