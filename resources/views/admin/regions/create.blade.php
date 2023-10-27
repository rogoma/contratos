@extends('layouts.app')

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-sitemap bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Regiones</h5>
                        <span>Agregar Región</span>
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
                            <a href="{{ route('regions.index') }}">Regiones</a>
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
                                    <h5>Agregar Región</h5>
                                </div>
                                <div class="card-block">
                                    <form method="POST" action="{{ route('regions.store') }}">
                                        @csrf

                                        <div class="form-group row @error('codreg') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Código Región</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="codreg" name="codreg" value="{{ old('codreg') }}" class="form-control @error('codreg') form-control-danger @enderror" value="{{ old('codreg') }}">
                                                @error('codreg')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('subcreg') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Sub Código</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="subcreg" name="subcreg" value="{{ old('subcreg') }}" class="form-control @error('subcreg') form-control-danger @enderror" value="{{ old('subcreg') }}">
                                                @error('subcreg')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('nomreg') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Nombre Región</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="nomreg" name="nomreg" value="{{ old('nomreg') }}" class="form-control @error('nomreg') form-control-danger @enderror" value="{{ old('nomreg') }}">
                                                @error('nomreg')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-sm-2"></label>
                                            <div class="col-sm-10">
                                                <button type="submit" class="btn btn-primary m-b-0">Guardar</button>
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