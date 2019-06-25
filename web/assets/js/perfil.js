'use strict';
import {reloadAside} from './app';
import {reloadStadistics} from './app';
import {reloadFollowers} from './app';
import {reloadEvents} from './app';
import {cambiarClases} from './app';

$(document).ready(function () {
    //VIDEOS
    $('#all,#est-videos').click(function () {

        let lista = $(this).parents('.mi-perfil');
        let user_id = lista.find('input[name=user_id]').val();
        let offset = 0;
        $.ajax({
            type: 'post',
            url: '/ajax/ajax_post_perfil',
            data: {
                user_id: user_id,
                offset: offset,
            },
            dataType: 'text',
            success: function (response) {
                let respuesta = JSON.parse(response);
                $('#list').show();
                $('.eventos_usuario').show();
                $('.post').remove();
                $('#caja_seguidores').hide();
                $('#caja_seguidos').hide();
                $('#subir_video').hide();
                $('#crear_evento').hide();

                $('.posts').find('input[name=offset]').val(9);
                $('.posts').append(respuesta.html);
            }
        });

    });

    //SEGUIDORES
    $('#est-seguidores').click(function () {

        let lista = $(this).parents('.mi-perfil');
        let user_id = lista.find('input[name=user_id]').val();
        $('.posts').find('input[name="offset"]').val(0);

        $.ajax({
            type: 'post',
            url: '/ajax/show_followed',
            data: {
                'user_id': user_id,
                'followed': false,
            },
            dataType: 'text',
            success: function (msg) {
                let result = JSON.parse(msg);
                let photo = "";
                let html = "";
                $('#list').hide();
                $('.post').hide();
                $('#caja_seguidores').hide();
                $('#caja_seguidos').hide();
                $('#subir_video').hide();
                $('#crear_evento').hide();
                $('.eventos_usuario').hide();

                $.each(result, function (key, arr) {
                    $.each(arr, function (id, value) {
                        if (key === 'users') {
                            if (id !== 'error') {
                                if (value.photo == null) {
                                    photo = 'https://ui-avatars.com/api/?size=180&name=' + value.username + '&color=6c6c6c&background=f2f2f2';
                                }else{
                                    photo = value.photo;
                                }
                                let path = Routing.generate('perfil', {'username': value.username});
                                html += `
                                       <a href="${path}" class="usuario-seguidor">
                                            <div class="photo">
                                                <img src="${photo}" alt="">
                                            </div>
                                            <div class="name">
                                                <p class="username">${value.username}</p>
                                            </div>
                                        </a>
                                `;
                                $('#lista_seguidores').html(html);
                                $('#caja_seguidores').show();
                            } else {
                                //Sacar mensaje No se han encontrado resultados
                                $('#perfil_posts').html(value.html)
                            }
                        }
                    });
                });
            }
        });

    });

    //SEGUIDOS
    $('#est-seguidos').click(function () {

        let lista = $(this).parents('.mi-perfil');
        let user_id = lista.find('input[name=user_id]').val();
        $('.posts').find('input[name="offset"]').val(0);
        $.ajax({
            type: 'post',
            url: '/ajax/show_followed',
            data: {
                'user_id': user_id,
                'followed': true,
            },
            dataType: 'text',
            success: function (msg) {
                let result = JSON.parse(msg);
                let photo = "";
                let html = "";
                $('#list').hide();
                $('.post').hide();
                $('#caja_seguidores').hide();
                $('#caja_seguidos').hide();
                $('#subir_video').hide();
                $('#crear_evento').hide();
                $('.eventos_usuario').hide();

                $.each(result, function (key, arr) {
                    $.each(arr, function (id, value) {
                        if (key === 'users') {
                            if (id !== 'error') {
                                if (value.photo == null) {
                                    photo = 'https://ui-avatars.com/api/?size=180&name=' + value.username + '&color=6c6c6c&background=f2f2f2';
                                }else{
                                    photo = value.photo;
                                }
                                let path = Routing.generate('perfil', {'username': value.username});
                                html += `
                                       <a href="${path}" class="usuario-seguido">
                                            <div class="photo">
                                                <img src="${photo}" alt="">
                                            </div>
                                            <div class="name">
                                                <p class="username">${value.username}</p>
                                            </div>
                                        </a>
                                `;

                                $('#lista_seguidos').html(html);
                                $('#caja_seguidos').show();
                            } else {
                                //Sacar mensaje No se han encontrado resultados
                                $('#perfil_posts').html(value.html)
                            }
                        }
                    });
                });
            }
        });
        $('#caja_seguidos').show();
    });

    //SUBIR VIDEO
    $('#btn_subir_video').click(function () {
        $('#list').hide();
        $('.post').hide();
        $('#caja_seguidores').hide();
        $('#caja_seguidos').hide();
        $('#crear_evento').hide();
        $('.eventos_usuario').hide();
        $('#subir_video').show();
    });

    //CREAR EVENTO
    $('#btn_crear_evento').click(function () {
        $('#subir_video').hide();
        $('#list').hide();
        $('.post').hide();
        $('#caja_seguidores').hide();
        $('#caja_seguidos').hide();
        $('.eventos_usuario').hide();
        $('#crear_evento').show();
    });

    //Boton seleccionar video
    $("[type=file]").on("change", function () {
        // Name of file and placeholder
        let file = this.files[0].name;
        $('.label-file').text(file);

    });

    //Seguir usuario o dejar de seguir usuario.
    $('.follow-user-perfil, .unfollow-user-perfil').click(function () {
        let id_user = $(this).parents('.mi-perfil').find('input[name=user_id]').val();
        let id_user_session = $(this).parents('.mi-perfil').find('input[name=id_user_sesion]').val();
            if(id_user_session) {
                $.ajax({
                    url: '/ajax/ajax_follow',
                    type: 'post',
                    data: {
                        id_user: id_user,
                        id_user_session: id_user_session,
                    },
                    success: function (response) {
                        cambiarClases(id_user, response[id_user]['follow']);
                        reloadAside();
                        reloadStadistics(id_user);
                        reloadFollowers(id_user);
                        reloadEvents(id_user);
                        if (response[id_user]['follow']) {
                            $('.follow-user-perfil')
                                .removeClass('follow-user-perfil')
                                .removeClass('follow-user-perfil')
                                .addClass('follow-user-perfil')
                                .addClass('unfollow-user-perfil')
                            ;
                            $('.unfollow-user-perfil').text('Dejar de seguir');
                        } else {
                            $('.unfollow-user-perfil')
                                .removeClass('follow-user-perfil')
                                .removeClass('unfollow-user-perfil')
                                .addClass('follow-user-perfil')
                                .addClass('follow-user-perfil');
                            $('.follow-user-perfil').text('Seguir');
                        }
                    }
                });
            } else{
                $('#popup1').show();
            }
    });

    //Mensajes privados
    $(document).on('click','.privados',function(){
        let id_user_session = $(this).parents('.mi-perfil').find('input[name=id_user_sesion]').val();
        if(id_user_session) {
            let id_user = $(this).parents('.mi-perfil').find('input[name=user_id]').val();
            $.ajax({
                url: '/ajax/chat',
                type: 'post',
                data: {
                    id_user: id_user,
                },
                success:function(){
                    window.location.href = '/mensajes/';
                }
            });
        }else{
            $('#popup1').show();
        }
    });

});

