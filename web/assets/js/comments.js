'use strict';
// $(document).ready(function(){

$(document).on('click', '.comments', function () {
    let caja_comentarios = $(this).parents('.post').find('.box-comments');
    if (caja_comentarios.is(':hidden')) {
        $(this).parents('.post').css('max-height', 'none');
        caja_comentarios.show();
    } else {
        $(this).parents('.post').css('max-height', '345px');
        caja_comentarios.hide();
    }
});

$(document).on('click', '.button-comments', function () {
    let id_user = $(this).parents('.post').find('input[name=id_user]').val();
    let id_post = $(this).parents('.post').find('input[name=id_post]').val();
    let id_user_session = $(this).parents('.post').find('input[name=id_user_session]').val();
    let caja_comentarios = $(this).parents('.post').find('.box-comments');
    let comment = caja_comentarios.find('.comment-user').val();
    let respuesta = false;
    $.ajax({
        url: '/ajax/ajax_comments',
        type: 'post',
        data: {
            id_user: id_user,
            id_user_session: id_user_session,
            id_post: id_post,
            comment: comment,
            respuesta: respuesta,
        },
        success: function (response) {
            // caja_comentarios.hide();
            if (response['comment']) {
                let path = Routing.generate('perfil', {'username': response['comment-user']});
                caja_comentarios
                    .find('.buttons')
                    .before(`<div class="comment">
                                <div class="enlaces">
                                    <a href="${path}">${response['comment-user']}</a>
                                    <a href="#" class="delete-comment">Borrar</a>
                                </div>
                                <p>${response['comment-content']}</p>
                            </div>`);
                caja_comentarios.find('.comment-user').val('');

            } else {
                $(this).parents('.box-comments').find('#msg_error').show(0).delay(5000).hide(0);
            }
        }
    });
});

$(document).on('click', '.delete-comment', function () {
    let comentario = $(this).parents('.comment');
    let id_comment = comentario.find('input[name=id_comment]').val();
    let id_post = comentario.find('input[name=id_post]').val();
    let id_user_comment = comentario.find('input[name=id_user_comment]').val();
    $.ajax({
        url: '/ajax/ajax_delete_comment',
        type: 'post',
        data: {
            id_comment: id_comment,
            id_post: id_post,
            id_user_comment: id_user_comment,
        },
        success: function (response) {
            if (response['delete']) {
                $(comentario).remove();
            }
        }
    });
});

// });
