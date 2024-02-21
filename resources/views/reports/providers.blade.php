<!DOCTYPE html>
<html>
<head>
    <title>Reporte de Contratistas</title>
    {{-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"> --}}
    <link rel="stylesheet" href="/css/bootstrap.min.css" />
</head>
<style type="text/css">
    table{
        font-family: arial,sans-serif;
        border-collapse:collapse;
        width:100%;
        font-size:8px;
    }
    td, th{
        border:1px solid #dddddd;
        text-align: center;
        padding:4px;
        font-size:10px;
    }
    thead tr{
        background-color:#dddddd;
        padding:2px;
        font-size:8px;
    }

    h2{
        text-align: center;
        font-size:14px;
        margin-bottom:5px;
    }
    h3{
        text-align: left;
        font-size:12px;
        margin-bottom:15px;
    }
    body{
        /* background:#f2f2f2; */
    }
    .section{
        margin-top:30px;
        padding:50px;
        background:#fff;
        font-size:8px;
    }
    .pdf-btn{
        margin-top:30px;
    }
    .columna1 { width: 1%; text-align: center;}
    .columna2 { width: 14%; text-align: left;}
    .columna3 { width: 4%; text-align: left;}
    .columna4 { width: 3%; text-align: left;} */
    .columna5 { width: 3%; text-align: left;}
    .columna6 { width: 3%; text-align: center;}
    .columna7 { width: 7%; text-align: center;}
    /* .columna6 { width: 8%; text-align: left;}     */
</style>
<body>
<img src="img/logoVI.png">
    <h2>Listado de Contratistas</h2>
        <table>
            <tr>
                {{-- PARA TITULOS EN COLORES --}}
                <th style="color:blue;font-weight: bold">#</th>
                <th>Contratista</th>
                <th>RUC</th>
                <th>Teléfono</th>
                <th>Email oferta</th>
                <th>Email OCompra</th>
                <th>Representante</th>
            </tr>
            @foreach($providers AS $f)
            <tr>
                {{-- <td class="columna1"> {{ number_format($f->ci,'0', ',','.') }}</td> --}}
                <td class="columna1">{{ $loop->index + 1 }}</td>
                <td class="columna2"> {{ $f->description }}</td>
                <td class="columna3"> {{ $f->ruc }}</td>
                <td class="columna4"> {{ $f->telefono }}</td>
                <td class="columna5"> {{ $f->email_oferta}}</td>
                <td class="columna6"> {{ $f->email_ocompra}}</td>
                <td class="columna7"> {{ $f->representante}}</td>
            </tr>
            @endforeach
        </table>
    </div>
</body>
</html>
