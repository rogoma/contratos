@extends('layouts.app')

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-sitemap bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Departmentos Geográficos</h5>
                        <span>EditarDepartmento Geográfico</span>
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
                            <a href="{{ route('departments.index') }}">Departmentos Geográficos</a>
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
                                    <h5>Editar Departmento Geográfico</h5>
                                </div>
                                <div class="card-block">
                                    <form method="POST" action="{{ route('departments.update', $department->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="form-group row @error('coddpto') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Código de Dpto.</label>
                                            <div class="col-sm-10">                                                
                                                <input type="text" id="coddpto" name="coddpto" value="{{ old('coddpto', $department->coddpto) }}" class="form-control @error('coddpto') form-control-danger @enderror" value="{{ old('coddpto', $department->coddpto) }}" readonly>
                                                @error('coddpto')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="form-group row @error('nomdpto') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Descripción</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="nomdpto" name="nomdpto" value="{{ old('nomdpto', $department->nomdpto) }}" class="form-control @error('nomdpto') form-control-danger @enderror" value="{{ old('nomdpto', $department->nomdpto) }}">
                                                @error('nomdpto')
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