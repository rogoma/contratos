<table class="table table-bordered table-hover mt-3">
    <thead class="thead-dark">
      <tr>
        <th scope="col">Lote</th>
        <th scope="col">Ítem</th>
        <th scope="col">Cód. de Catál</th>
        {{-- <th scope="col">Descripción</th>         --}}
        <th scope="col">Present.</th>
        <th scope="col">U.M.</th>
        <th scope="col">Precio Unitario</th>
        <th scope="col">Cantidad</th>
        <th scope="col">Monto Total Gs.</th>
        <th scope="col">Marca</th>
        <th scope="col">Procedencia</th>
        <th scope="col">Fabricante</th>
        <th scope="col">Empresa</th>

      </tr>
    </thead>
    <tbody>
        @foreach ($orders as $order)
        <tr>
          {{-- <th scope="row">{{ $order->order_id }}</th>         --}}
          <td>{{$order->batch}}</td>
          <td>{{$order->item_number}}</td>
          <td>{{$order->code}}</td>        
          {{-- <td>{{$order->level5_catalog_codes_description }}</td>           --}}
          <td>{{$order->order_presentations_description}}</td>
          <td>{{$order->order_measurement_units_description }}</td>
          
          <td>{{$order->unit_price}}</td>
          
          {{-- VERIFICAMOS SI ES TIPO CONTRATO CERRADO O ABIERTO --}}
          @if ($order->quantity > 0)
            <td>{{$order->quantity}}</td>
          @else
            <td>{{$order->max_quantity}}</td>
          @endif
          
          <td>{{$order->total_amount}}</td>
        </tr>      
        @endforeach
    </tbody>    
  </table>
  <br><br>
  <table class="table table-bordered table-hover mt-3">
    <thead class="thead-dark">
      <tr>        
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>        
        <th scope="col">Empresas participantes</th>        
      </tr>
    </thead>
    
    <tbody>
      @foreach ($providers as $provider)
        <tr>          
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>
          <td></td>          
          <td>{{$provider->provider}}</td>
          {{-- <td>{{$provider->ruc}}</td>           --}}
        </tr>      
      @endforeach
    </tbody>
  </table>      