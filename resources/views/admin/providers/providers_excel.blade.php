<table class="table table-busered table-hover mt-3">
    <thead class="thead-dark">
      <tr>
        <th style="color:blue;font-weight: bold">#</th>
            <th>Contratista</th>
            <th>RUC</th>
            <th>Teléfono</th>
            <th>Email oferta</th>
            <th>Email OCompra</th>
            <th>Representante</th>
      </tr>
    </thead>
    <tbody>
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
    </tbody>
  </table>
