$(document).ready(function(){
    $(document).on('click','#ver_usuarios',function () {
        $('#conversaciones_activas').hide();
        $('#lista_seguidores').show();
    });

    $(document).on('click','#volver_chat',function () {
        $('#lista_seguidores').hide();
        $('#conversaciones_activas').show();
    });

    //Hacer click a usuario
    $(document).on('click','.seguidor_inactivo',function(){
        let id_user = $(this).find('input[name=id_user]').val();
        $('#conversaciones_activas').append(this);
        $('#lista_seguidores').hide();
        $('#conversaciones_activas').show();
        $.ajax({
            type:'post',
            url:'/ajax/chat',
            data:{
                id_user:id_user,
            },
            success: function (response){
                $('.formulario').show();
                $('#cabecera').remove();
                $('.conversacion ').prepend(response['html_cabecera']);
                $('#mensajes').remove();
                $('#conversacion ').append(response['html_mensajes']);
                $('#conversacion').scrollTop(999999999999999999);

                llamadaInterval(id_user);
            }
        });
    });

    $(document).on('click','.seguidor',function(){
        let id_user = $(this).find('input[name=id_user]').val();
        $.ajax({
            type:'post',
            url:'/ajax/chat',
            data:{
                id_user:id_user,
            },
            success: function (response){
                $('.formulario').show();
                $('#cabecera').remove();
                $('.conversacion ').prepend(response['html_cabecera']);
                $('#mensajes').remove();
                $('#conversacion ').append(response['html_mensajes']);
                // $('#conversacion').animate({ scrollTop: 999999999999999999 },100);
                $('#conversacion').scrollTop(999999999999999999);

                llamadaInterval(id_user);
            }
        });
    });

    //Enviar mensaje
    $(document).on('click','#enviar_mensaje',function(){
        let mensaje = $(this).siblings("textarea").val();
        if(mensaje != '' && mensaje != null ){
            let id_user = $(this).parents('.conversacion ').find('input[name=id_user]').val();
            $.ajax({
                type:'post',
                url:'/ajax/enviar_mensaje',
                data:{
                    id_user:id_user,
                    mensaje:mensaje,
                },
                success: function (response){
                    $('#mensajes').remove();
                    $('#conversacion ').append(response['html_mensajes']);
                    $('#textarea').val('');
                    // $('#conversacion').animate({ scrollTop: 999999999999999999 },100);
                    $('#conversacion').scrollTop(999999999999999999);

                }
            });
        }
    });
});


function llamadaInterval(id_user){
    if(typeof interval !== 'undefined'){
        clearInterval(interval);
    }
     interval = setInterval(function(){
        $.ajax({
            type:'post',
            url:'/ajax/reload_mensajes',
            data:{
                id_user:id_user,
            },
            success: function (response){
                $('#mensajes').remove();
                $('#conversacion ').append(response['html_mensajes']);
                // $('#conversacion').animate({ scrollTop:999999999999999999},100);
                $('#conversacion').scrollTop(999999999999999999);
            }
        });
    }, 2000);
}