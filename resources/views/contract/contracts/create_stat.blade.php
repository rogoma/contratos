@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Selecciona fechas</h2>
        {{-- <form action="" method="post"> --}}
        <form method="POST" action="{{ route('contracts.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date1">Fecha 1:</label>
                        <input type="text" class="form-control datepicker" id="date1" name="date1" autocomplete="off">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date2">Fecha 2:</label>
                        <input type="text" class="form-control datepicker" id="date2" name="date2" autocomplete="off">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date3">Fecha 3:</label>
                        <input type="text" class="form-control datepicker" id="date3" name="date3" autocomplete="off">
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date4">Fecha 4:</label>
                        <input type="text" class="form-control datepicker" id="date4" name="date4" autocomplete="off">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date5">Fecha 5:</label>
                        <input type="text" class="form-control datepicker" id="date5" name="date5" autocomplete="off">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label for="date6">Fecha 6:</label>
                        <input type="text" class="form-control datepicker" id="date6" name="date6" autocomplete="off">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Guardar</button>
        </form>
    </div>

    <script>
        // Inicializa el datepicker para cada campo
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true
        });
    </script>
@endsection
