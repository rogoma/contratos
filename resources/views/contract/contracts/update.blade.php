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
                        <span>Editar Llamados {{$contract->id }}</span>
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
                                {{-- <div class="card-header">
                                    <h5>Editar Pedido</h5>
                                </div> --}}
                                <div class="card-block">
                                    <h6 class="text-center">Datos de cabecera para los formularios</h6>
                                    <form class="row" method="POST" action="{{ route('contracts.update', $contract->id) }}">
                                        @csrf
                                        @method('PUT')

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
                                        
                                        <div class="col-sm-4">
                                            <div class="form-group @error('modality') has-danger @enderror">
                                                <label class="col-form-label">Modalidad <br><small>(Modalidad del llamado)</small></label>
                                                <select id="modality" name="modality" class="form-control"">
                                                    <option value="">Seleccionar</option>
                                                @foreach ($modalities as $modality)
                                                    @if ($contract->modality->id == 28)
                                                        <option value="{{ $modality->id }}" @if ($modality->id == old('modality', $contract->modality_id)) selected @endif>{{ $modality->description }}</option>
                                                    @else
                                                        <option value="{{ $modality->id }}" @if ($modality->id == old('modality', $contract->modality_id)) selected @endif>{{ $modality->description }}</option>
                                                    @endif
                                                @endforeach
                                                </select>
                                                @error('modality')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group @error('dncp_pac_id') has-danger @enderror">
                                                <label class="col-form-label">PAC ID <br><small>(ID del PAC)</small></label>
                                                <input type="number" id="dncp_pac_id" name="dncp_pac_id" value="{{ old('dncp_pac_id', $contract->dncp_pac_id) }}" class="form-control">
                                                @error('dncp_pac_id')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group @error('year') has-danger @enderror">
                                                <label class="col-form-label">AÑO <br><small>(AÑO)</small></label>
                                                <input type="number" id="year" name="year" value="{{ old('year', $contract->year) }}" class="form-control">
                                                @error('year')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>                                        

                                        <div class="col-sm-3">
                                            <div class="form-group @error('financial_organism') has-danger @enderror">
                                                <label class="col-form-label">O.F.  <br><small>(Organismo Financiador)</small></label>
                                                <select id="financial_organism" name="financial_organism" class="form-control">
                                                    <option value="">Seleccionar</option>
                                                @foreach ($financial_organisms as $financial_organism)
                                                    <option value="{{ $financial_organism->id }}" @if ($financial_organism->id == old('financial_organism', $contract->financial_organism_id)) selected @endif>{{ $financial_organism->code.' - '.$financial_organism->description }}</option>
                                                @endforeach
                                                </select>
                                                @error('financial_organism')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>                                        

                                        <div class="col-sm-2">
                                            <div class="form-group @error('total_amount') has-danger @enderror">
                                                <label class="col-form-label">Monto Total <br><small>(Monto total)</small></label>
                                                <input type="text" id="total_amount" name="total_amount" value="{{ old('total_amount', $contract->total_amount) }}" class="form-control total_amount autonumber" data-a-sep="." data-a-dec="," readonly>
                                                {{-- <input type="text  " id="total_amount" name="total_amount" value="{{ $contract->total_amount}}" class="form-control total_amount autonumber" data-a-sep="." data-a-dec="," readonly> --}}
                                                @error('total_amount')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-sm-4">
                                            <div class="form-group @error('description') has-danger @enderror">
                                                <label class="col-form-label">Descripción <br><small>(Descripción del Pedido)</small></label>
                                                <textarea rows="2" id="description" name="description" class="form-control">{{ old('description', $contract->description) }}</textarea>
                                                @error('description')
                                                    <div class="col-form-label">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>                                        

                                        <div class="col-sm-12">
                                            <div class="form-group text-center">
                                                <button type="submit" class="btn btn-warning m-b-0 f-12">Modificar</button>
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

@push('scripts')
<script type="text/javascript">
$(document).ready(function(){

    $('#dependency').select2();
    $('#modality').select2();
    $('#sub_program').select2();
    $('#funding_source').select2();
    $('#financial_organism').select2();
    $('#expenditure_object_id').select2();
    $('#expenditure_object2_id').select2();
    $('#expenditure_object3_id').select2();
    $('#expenditure_object4_id').select2();
    $('#expenditure_object5_id').select2();
    $('#expenditure_object6_id').select2();


    $('#begin_date').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
    });
    $('#form4_date').datepicker({
        language: 'es',
        format: 'dd/mm/yyyy'
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
