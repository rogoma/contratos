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
                        <span>Reparos</span>
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
                            <a href="{{ route('contracts.show', $objection->order_id) }}">Pedido Nº {{ $objection->order_id }}</a>
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
                                    <h5>Modificar Reparo</h5>
                                </div>
                                <div class="card-block">
                                    <form method="POST" action="{{ route('contracts.objections.update', [$objection->order_id, $objection->id]) }}">
                                    @csrf
                                    @method('PUT')
                                        
                                        <div class="form-group @error('objection') has-danger @enderror">
                                            <label class="col-form-label">Descripción</label>
                                            <textarea id="objection" name="objection" class="form-control">{{ old('objection', $objection->objection) }}</textarea>
                                            @error('objection')
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