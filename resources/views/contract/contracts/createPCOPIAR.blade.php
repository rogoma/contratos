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
                                </div>
                                <div class="card-block">
                                    <h3 class="text-center">Datos para cargar Llamados</h3>
                                    <form class="rows" method="POST" action="{{ route('contracts.store') }}">
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

                                        {{-- #1 --}}
                                        <div class="col-sm-4">
                                            <div class="form-group @error('description') has-danger @enderror">
                                                <label class="col-form-label">Descripción</label>
                                                <textarea rows="2" id="description" name="description" class="form-control">{{ old('description') }}</textarea>
                                                @error('description')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- #2 --}}
                                        <div class="col-sm-2">
                                            <div class="form-group @error('dncp_pac_id') has-danger @enderror">
                                                <label class="col-form-label">PAC ID</label>
                                                <input type="number" id="dncp_pac_id" name="dncp_pac_id" value="{{ old('dncp_pac_id') }}" class="form-control">
                                                @error('dncp_pac_id')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- #3 --}}
                                        <div class="col-sm-6">
                                            <div class="form-group @error('linkdncp') has-danger @enderror">
                                                <label class="col-form-label">Link DNCP</label>
                                                <textarea rows="2" id="linkdncp" name="linkdncp" class="form-control">{{ old('linkdncp') }}</textarea>
                                                @error('linkdncp')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- #4 --}}
                                        <div class="col-sm-2">
                                            <div class="form-group @error('number_year') has-danger @enderror">
                                                <label class="col-form-label">N° Contrato/Año</label>
                                                <input type="text" id="number_year" name="number_year" class="form-control">{{ old('number_year') }}</textarea>
                                                @error('number_year')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- #5 --}}
                                        <div class="col-sm-2">
                                            <div class="form-group @error('year') has-danger @enderror">
                                                <label class="col-form-label">AÑO </label>
                                                <input type="number" id="year" name="year" value="{{ old('year') }}" class="form-control">
                                                @error('year')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- #6 --}}
                                        <div class="col-sm-2">
                                            <label class="col-form-label @error('sign_date') has-danger @enderror">Fecha Firma Contr.</label>
                                            <div class="input-group @error('sign_date') has-danger @enderror">
                                                <input type="text" id="sign_date" name="sign_date" value="{{ old('sign_date') }}" class="form-control" autocomplete="off">
                                                <span class="input-group-append" id="basic-addon">
                                                    <label class="input-group-text" onclick="show('sign_date');"><i class="fa fa-calendar"></i></label>
                                                </span>
                                            </div>
                                            @error('sign_date')
                                            <div class="has-danger">
                                                <div class="col-form-label">{{ $message }}</div>
                                            </div>
                                            @enderror
                                        </div>
                                        {{-- #7 --}}
                                        <div class="col-sm-4">
                                            <div class="form-group @error('provider') has-danger @enderror">
                                                <label class="col-form-label">Contratista </label>
                                                <select id="provider" name="provider" class="form-control">
                                                    <option value="">Seleccionar</option>
                                                @foreach ($providers as $provider)
                                                    {{-- <option value="{{ $provider->id }}" @if ($provider->id == old('provider')) selected @endif>{{ $provider->description .' ('.$provider->code.')' }}</option> --}}
                                                    <option value="{{ $provider->id }}" @if ($provider->id == old('provider')) selected @endif>{{ $provider->description }}</option>
                                                @endforeach
                                                </select>
                                                @error('provider')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- #8 --}}
                                        <div class="col-sm-2">
                                            <div class="form-group @error('actual_state') has-danger @enderror">
                                                <label class="col-form-label">Estado <br></label>
                                                <select id="actual_state" name="actual_state" class="form-control">
                                                    <option value="">Seleccionar</option>
                                                @foreach ($contr_states as $state)
                                                    <option value="{{ $state->id }}" @if ($state->id == old('actual_state')) selected @endif>{{ $state->description }}</option>
                                                @endforeach
                                                </select>
                                                @error('actual_state')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- #9 --}}
                                        <div class="col-sm-3">
                                            <div class="form-group @error('modality') has-danger @enderror">
                                                <label class="col-form-label">Modalidad</label>
                                                <select id="modality" name="modality" class="form-control">
                                                    <option value="">Seleccionar</option>
                                                @foreach ($modalities as $modality)
                                                    <option value="{{ $modality->id }}" @if ($modality->id == old('modality')) selected @endif>{{ $modality->description .' ('.$modality->code.')' }}</option>
                                                @endforeach
                                                </select>
                                                @error('modality')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- #10 --}}
                                        <div class="col-sm-3">
                                            <div class="form-group @error('financial_organism') has-danger @enderror">
                                                <label class="col-form-label">Organismo Financiador</label>
                                                <select id="financial_organism" name="financial_organism" class="form-control">
                                                    <option value="">Seleccionar</option>
                                                @foreach ($financial_organisms as $financial_organism)
                                                    <option value="{{ $financial_organism->id }}" @if ($financial_organism->id == old('financial_organism')) selected @endif>{{ $financial_organism->code.' - '.$financial_organism->description }}</option>
                                                @endforeach
                                                </select>
                                                @error('financial_organism')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- #11 --}}
                                        <div class="col-sm-3">
                                            <div class="form-group @error('contract_type') has-danger @enderror">
                                                <label class="col-form-label">Tipo de Contrato</label>
                                                <select id="contract_type" name="contract_type" class="form-control">
                                                    <option value="">Seleccionar</option>
                                                @foreach ($contract_types as $contract_type)
                                                    <option value="{{ $contract_type->id }}" @if ($contract_type->id == old('contract_type')) selected @endif>{{$contract_type->description }}</option>
                                                @endforeach
                                                </select>
                                                @error('contract_type')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- #12 --}}
                                        <div class="col-sm-3" style="padding-bottom: 10px;">
                                            <div class="form-group @error('total_amount') has-danger @enderror">
                                                <label class="col-form-label">Monto Total</label>
                                                {{-- <input type="number" id="total_amount" name="total_amount" value="{{ old('total_amount') }}" class="form-control"> --}}
                                                <input type="text" id="total_amount" name="total_amount" value="{{ old('total_amount') }}" class="form-control total_amount autonumber" data-a-sep="." data-a-dec=",">
                                                @error('total_amount')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Agrega estos enlaces en la sección head de tu archivo de diseño o la vista directamente -->
                                        {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/css/bootstrap-datepicker.min.css">
                                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                                        <script src="https://cdn.jsdelivr.net/npm/bootstrap-datepicker@1.9.0/dist/js/bootstrap-datepicker.min.js"></script> --}}

                                        <div class="container">
                                            <h3 style="text-align: center;">Opciones de Pólizas</h3>
                                            {{-- <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label for="date1">Fecha 1:</label>
                                                        <input type="text" class="form-control datepicker" id="date1" name="date1" autocomplete="off">
                                                    </div>
                                            </div> --}}

                                            <div class="row">
                                                {{-- #13 ANTICIPO--}}
                                                <div class="col-sm-2">
                                                        <label class="col-form-label @error('advance_validity_from') has-danger @enderror">Anticipo <br><small>(Vigencia Desde)</small></label>
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
                                                <div class="col-sm-2">
                                                        <label class="col-form-label @error('advance_validity_to') has-danger @enderror">Anticipo <br><small>(Vigencia Hasta)</small></label>
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

                                                <div class="col-sm-2">
                                                    <div class="form-group @error('control_1') has-danger @enderror">
                                                        <label class="col-form-label">Control<br><small>(Control)</small></label>
                                                        <input type="text" id="control_1" disabled="disabled" name="control_1" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row">
                                                {{-- #15 FIEL CUMPL.--}}
                                                <div class="col-sm-2">
                                                    <label class="col-form-label @error('fidelity_validity_from') has-danger @enderror">Fiel Cumplimiento<br><small>(Vigencia Desde)</small></label>
                                                    <div class="input-group @error('fidelity_validity_from') has-danger @enderror">
                                                        <input type="text" id="fidelity_validity_from" name="fidelity_validity_from" value="{{ old('fidelity_validity_from') }}" class="form-control" autocomplete="off">
                                                        <span class="input-group-append" id="basic-addon">
                                                            <label class="input-group-text" onclick="show('fidelity_validity_from');"><i class="fa fa-calendar"></i></label>
                                                        </span>
                                                    </div>
                                                    @error('fidelity_validity_from')
                                                    <div class="has-danger">
                                                        <div class="col-form-label">{{ $message }}</div>
                                                    </div>
                                                    @enderror
                                                </div>
                                                {{-- #16 FIEL CUMPL.--}}
                                                <div class="col-sm-2">
                                                    <label class="col-form-label @error('fidelity_validity_to') has-danger @enderror">Fiel Cumplimiento <br><small>(Vigencia Hasta)</small></label>
                                                    <div class="input-group @error('fidelity_validity_to') has-danger @enderror">
                                                        <input type="text" id="fidelity_validity_to" name="fidelity_validity_to" value="{{ old('fidelity_validity_to') }}" class="form-control" autocomplete="off">
                                                        <span class="input-group-append" id="basic-addon">
                                                            <label class="input-group-text" onclick="show('fidelity_validity_to');"><i class="fa fa-calendar"></i></label>
                                                        </span>
                                                    </div>
                                                    @error('fidelity_validity_to')
                                                    <div class="has-danger">
                                                        <div class="col-form-label">{{ $message }}</div>
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group @error('control_2') has-danger @enderror">
                                                        <label class="col-form-label">Control<br><small>(Control)</small></label>
                                                        <input type="text" id="control_2" disabled="disabled" name="control_2" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row">
                                                {{-- #17 ACCIDENTES--}}
                                                <div class="col-sm-2">
                                                    <label class="col-form-label @error('accidents_validity_from') has-danger @enderror">Accidentes Personales<br><small>(Vigencia Desde)</small></label>
                                                    <div class="input-group @error('accidents_validity_from') has-danger @enderror">
                                                        <input type="text" id="accidents_validity_from" name="accidents_validity_from" value="{{ old('accidents_validity_from') }}" class="form-control" autocomplete="off">
                                                        <span class="input-group-append" id="basic-addon">
                                                            <label class="input-group-text" onclick="show('accidents_validity_from');"><i class="fa fa-calendar"></i></label>
                                                        </span>
                                                    </div>
                                                    @error('accidents_validity_from')
                                                    <div class="has-danger">
                                                        <div class="col-form-label">{{ $message }}</div>
                                                    </div>
                                                    @enderror
                                                </div>
                                                {{-- #18 ACCIDENTES--}}
                                                <div class="col-sm-2">
                                                    <label class="col-form-label @error('accidents_validity_to') has-danger @enderror">Accidentes Personales <br><small>(Vigencia Hasta)</small></label>
                                                    <div class="input-group @error('accidents_validity_to') has-danger @enderror">
                                                        <input type="text" id="accidents_validity_to" name="accidents_validity_to" value="{{ old('accidents_validity_to') }}" class="form-control" autocomplete="off">
                                                        <span class="input-group-append" id="basic-addon">
                                                            <label class="input-group-text" onclick="show('accidents_validity_to');"><i class="fa fa-calendar"></i></label>
                                                        </span>
                                                    </div>
                                                    @error('accidents_validity_to')
                                                    <div class="has-danger">
                                                        <div class="col-form-label">{{ $message }}</div>
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group @error('control_3') has-danger @enderror">
                                                        <label class="col-form-label">Control<br><small>(Control)</small></label>
                                                        <input type="text" id="control_3" disabled="disabled" name="control_3" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row">
                                                {{-- #19 TODO RIESGO--}}
                                                <div class="col-sm-2">
                                                    <label class="col-form-label @error('risks_validity_from') has-danger @enderror">Todo Riesgo<br><small>(Vigencia Desde)</small></label>
                                                    <div class="input-group @error('risks_validity_from') has-danger @enderror">
                                                        <input type="text" id="risks_validity_from" name="risks_validity_from" value="{{ old('risks_validity_from') }}" class="form-control" autocomplete="off">
                                                        <span class="input-group-append" id="basic-addon">
                                                            <label class="input-group-text" onclick="show('risks_validity_from');"><i class="fa fa-calendar"></i></label>
                                                        </span>
                                                    </div>
                                                    @error('risks_validity_from')
                                                    <div class="has-danger">
                                                        <div class="col-form-label">{{ $message }}</div>
                                                    </div>
                                                    @enderror
                                                </div>
                                                {{-- #20 TODO RIESGO--}}
                                                <div class="col-sm-2">
                                                    <label class="col-form-label @error('risks_validity_to') has-danger @enderror">Todo Riesgo <br><small>(Vigencia Hasta)</small></label>
                                                    <div class="input-group @error('risks_validity_to') has-danger @enderror">
                                                        <input type="text" id="risks_validity_to" name="risks_validity_to" value="{{ old('risks_validity_to') }}" class="form-control" autocomplete="off">
                                                        <span class="input-group-append" id="basic-addon">
                                                            <label class="input-group-text" onclick="show('risks_validity_to');"><i class="fa fa-calendar"></i></label>
                                                        </span>
                                                    </div>
                                                    @error('risks_validity_to')
                                                    <div class="has-danger">
                                                        <div class="col-form-label">{{ $message }}</div>
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group @error('control_4') has-danger @enderror">
                                                        <label class="col-form-label">Control<br><small>(Control)</small></label>
                                                        <input type="text" id="control_4" disabled="disabled" name="control_4" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row">
                                                {{-- #21 RESP. CIVIL--}}
                                                <div class="col-sm-2">
                                                    <label class="col-form-label @error('civil_resp_validity_from') has-danger @enderror">Responsabilidad Civil<br><small>(Vigencia Desde)</small></label>
                                                    <div class="input-group @error('civil_resp_validity_from') has-danger @enderror">
                                                        <input type="text" id="civil_resp_validity_from" name="civil_resp_validity_from" value="{{ old('civil_resp_validity_from') }}" class="form-control" autocomplete="off">
                                                        <span class="input-group-append" id="basic-addon">
                                                            <label class="input-group-text" onclick="show('civil_resp_validity_from');"><i class="fa fa-calendar"></i></label>
                                                        </span>
                                                    </div>
                                                    @error('civil_resp_validity_from')
                                                    <div class="has-danger">
                                                        <div class="col-form-label">{{ $message }}</div>
                                                    </div>
                                                    @enderror
                                                </div>
                                                {{-- #22 RESP. CIVIL--}}
                                                <div class="col-sm-2">
                                                    <label class="col-form-label @error('civil_resp_validity_to') has-danger @enderror">Responsabilidad Civil <br><small>(Vigencia Hasta)</small></label>
                                                    <div class="input-group @error('civil_resp_validity_to') has-danger @enderror">
                                                        <input type="text" id="civil_resp_validity_to" name="civil_resp_validity_to" value="{{ old('civil_resp_validity_to') }}" class="form-control" autocomplete="off">
                                                        <span class="input-group-append" id="basic-addon">
                                                            <label class="input-group-text" onclick="show('civil_resp_validity_to');"><i class="fa fa-calendar"></i></label>
                                                        </span>
                                                    </div>
                                                    @error('risks_validity_to')
                                                    <div class="has-danger">
                                                        <div class="col-form-label">{{ $message }}</div>
                                                    </div>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-2">
                                                    <div class="form-group @error('control_5') has-danger @enderror">
                                                        <label class="col-form-label">Control<br><small>(Control)</small></label>
                                                        <input type="text" id="control_5" disabled="disabled" name="control_5" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                    <br>
                                    <div class="col-sm-12">
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-primary m-b-0 f-12">Guardar</button>
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
</div>
@endsection

@push('scripts')
<script type="text/javascript">
$(document).ready(function(){

    $('#dependency').select2();
    $('#provider').select2();
    $('#modality').select2();
    $('#actual_state').select2();
    $('#contract_type').select2();
    $('#funding_source').select2();
    $('#financial_organism').select2();
    // $('#expenditure_object').select2();

    $('#sign_date').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });
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

    $('#fidelity_validity_from').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });
    $('#fidelity_validity_to').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });

    $('#accidents_validity_from').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });
    $('#accidents_validity_to').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });

    $('#risks_validity_from').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });
    $('#risks_validity_to').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });
    $('#civil_resp_validity_from').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });
    $('#civil_resp_validity_to').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy',
        autoclose: true
    });




    $('#dncp_resolution_date').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    });

    show = function(id){
        $('#'+id).datepicker('show');
    }

    $('#plurianualidad').change(function(){
        if($('#plurianualidad').val() == 1){
            $('#multi_years').removeClass('d-none');
        }else{
            $('#multi_years').addClass('d-none');
        }
    });

    $('#addRow').click(function(){
        new_row = $('#multi_year_template').clone();
        new_row.removeClass('d-none');
        new_row.find('#multi_year_year').attr('name', 'multi_year_year[]');
        new_row.find('#multi_year_amount').attr('name', 'multi_year_amount[]');
        $('#multi_years').append(new_row);
    });
    delRow = function(element){
        element.closest('#multi_year_template').remove();
    }

});
</script>
@endpush
