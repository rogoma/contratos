@extends('layouts.app')

@section('content')
<div class="pcoded-content">
    <div class="page-header card">
        <div class="row align-items-end">
            <div class="col-lg-8">
                <div class="page-header-title">
                    <i class="fa fa-sitemap bg-c-blue"></i>
                    <div class="d-inline">
                        <h5>Distritos</h5>
                        <span>Agregar Distrito</span>
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
                            <a href="{{ route('districts.index') }}">Distritos</a>
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
                                    <h5>Agregar Distrito</h5>
                                </div>
                                <div class="card-block">
                                    <form method="POST" action="{{ route('districts.store') }}">
                                        @csrf

                                        <div class="form-group row @error('coddist') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Código Distrito</label>
                                            <div class="col-sm-10">
                                                <input type="text" id="coddist" name="coddist" value="{{ old('coddist') }}" class="form-control @error('coddist') form-control-danger @enderror" value="{{ old('coddist') }}">
                                                @error('coddist')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('coddpto') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Código Dpto. Geográfico</label>
                                            <div class="col-sm-10">                                                
                                                <input type="text" id="coddpto" name="coddpto" value="{{ old('coddpto') }}" class="form-control @error('coddpto') form-control-danger @enderror" value="{{ old('coddpto') }}">
                                                @error('coddpto')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

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
                                            <label class="col-sm-2 col-form-label">Sub Código Región</label>
                                            <div class="col-sm-10">                                                
                                                <input type="text" id="subcreg" name="subcreg" value="{{ old('subcreg') }}" class="form-control @error('subcreg') form-control-danger @enderror" value="{{ old('subcreg') }}">
                                                @error('subcreg')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-group row @error('nomdist') has-danger @enderror">
                                            <label class="col-sm-2 col-form-label">Nombre Distrito</label>
                                            <div class="col-sm-10">                                                
                                                <input type="text" id="nomdist" name="nomdist" value="{{ old('nomdist') }}" class="form-control @error('nomdist') form-control-danger @enderror" value="{{ old('nomdist') }}">
                                                @error('nomdist')
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