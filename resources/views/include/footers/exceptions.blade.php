@push('scripts')
<script type="text/javascript">
$(document).ready(function(){
    
    getNotifications = function(){
        // obtenemos primeramente el token csrf y luego realizamos 
        // la solicitud de notificaciones
        $.ajax({
            url : '/token/',
            method : 'GET',
            success: function(response){
                try{
                    if(response.status == "success"){
                        // INICIO obtener las notificaciones
                        $.ajax({
                            url : '/exceptions/getNotifications/',
                            method : 'GET',
                            data: { _token: response.token },
                            success: function(data){
                                try{
                                    if(data.status == "success"){
                                        notificaciones = '<li><h6>Alertas</h6></li>';
                                        parent = document.getElementById('alertas-notificaciones');
                                        alertas = 0;

                                        // notificaciones tope recepcion consultas
                                        if(data.alerta_consultas.length > 0){
                                            data.alerta_consultas.forEach(element => {
                                                alertas += 1;
                                                var limite = '';
                                                if(element.dias == 0){ 
                                                    limite = 'Fecha límite hoy.'
                                                }else if(element.dias == 1){
                                                    limite = 'Fecha límite dentro de 1 día.'
                                                }else{
                                                    limite = 'Fecha límite dentro de '+element.dias+' días.'
                                                }
                                                notificaciones += '<li><div class="media"><div class="media-body">'+
                                                    '<h5 class="notification-user">Fecha Tope de Consultas</h5>'+
                                                    '<p class="notification-msg">PAC_ID: '+element.pac_id+'</p>'+
                                                    '<p style="font-size: 14px;color:red" class="notification-msg">FECHA FINAL: '+ (element.fecha_fin)+'</p>'+
                                                    // '<p class="notification-msg">Llamado Nº '+element.llamado+'</p>'+
                                                    '<span style="font-size: 14px;color:red" class="notification-time f-w-600">'+limite+'</span>'+
                                                    '</div></div></li>';3
                                            });
                                        }
                                        // notificaciones tope aclaracion consultas
                                        if(data.alerta_aclaraciones.length > 0){
                                            data.alerta_aclaraciones.forEach(element => {
                                                alertas += 1;
                                                var limite = '';
                                                if(element.dias == 0){ 
                                                    limite = 'Fecha límite hoy.'
                                                }else if(element.dias == 1){
                                                    limite = 'Fecha límite dentro de 1 día.'
                                                }else{
                                                    limite = 'Fecha límite dentro de '+element.dias+' días.'
                                                }
                                                var consultas = 0;
                                                if(element.consultas_pendientes == 1){
                                                    consultas = '1 consulta pendiente de respuesta.'
                                                }else{
                                                    consultas = element.consultas_pendientes +' consultas pendientes de respuesta.'
                                                }
                                                notificaciones += '<li><div class="media"><div class="media-body">'+
                                                    '<h5 class="notification-user">Pendiente de respuesta</h5>'+
                                                    '<p class="notification-msg">Llamado Nº '+element.llamado+', '+consultas+'</p>'+
                                                    '<span style="font-size: 14px;color:red" class="notification-time f-w-600">'+limite+'</span>'+
                                                    '</div></div></li>';3   
                                            });
                                        }

                                        if(data.alerta_consultas.length > 0 || data.alerta_aclaraciones.length > 0){
                                            $('#numero-notificaciones').text(alertas);
                                            parent.innerHTML = notificaciones;
                                        }
                                    }else{
                                        console.log(data.message);
                                    }
                                }catch(error2){ 
                                    console.log(error2);                                    
                                }
                            },
                            error: function(error2){
                                console.log(error2);
                            }
                        });
                        // FIN obtener las notificaciones
                    }
                }catch(error){ 
                    console.log(error); 
                }
            }
        });
    }

    getNotifications();

    // intervalo cada 10 minutos
    setInterval(getNotifications, 10*60*1000);
});
</script>
@endpush