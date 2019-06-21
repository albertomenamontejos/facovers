'use strict';
$(document).ready(function(){
    $('.follow-user').click(function(){
        let id_user =  $(this).parents('.post').find('input[name=id_user]').val();
        let id_post = $(this).parents('.post').find('input[name=id_post]').val();
        let id_user_session = $(this).parents('.post').find('input[name=id_user_session]').val();
        $.ajax({
            url: '/ajax/ajax_follow',
            type:'post',
            data:{
                id_user: id_user,
                id_user_session: id_user_session,
            },
            success:function(response){
                // cambiarClases(response[id_user]['follow']);
                if(response[id_user]['follow']){
                    $('.follow_' + id_user)
                        .removeClass('.follow_' + id_user)
                        .removeClass('follow')
                        .addClass('.follow_' + id_user)
                        .addClass('unfollow')
                    ;
                    $('.follow_' + id_user).children('button').text('Dejar de seguir')
                }else{
                    $('.unfollow_' + id_user)
                        .removeClass('.follow_' + id_user)
                        .removeClass('unfollow')
                        .addClass('follow_' + id_user)
                        .addClass('follow');
                    $('.follow_' + id_user).children('button').text('Seguir')
                }
            }
        });
    });
});
//
// function cambiarClases(follow){
//     if(follow){
//         $('.follow-user-perfil')
//             .removeClass('follow-user-perfil')
//             .removeClass('follow-user-perfil')
//             .addClass('follow-user-perfil')
//             .addClass('unfollow-user-perfil')
//         ;
//         $('.unfollow-user-perfil').text('Dejar de seguir')
//     }else{
//         $('.unfollow-user-perfil')
//             .removeClass('follow-user-perfil')
//             .removeClass('unfollow-user-perfil')
//             .addClass('follow-user-perfil')
//             .addClass('follow-user-perfil');
//         $('.follow-user-perfil').text('Seguir')
//     }
// }