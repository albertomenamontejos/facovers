import {reloadVideos} from './app';

$(document).on('click','.navdot',function(){
    if($(this).siblings('.menu-navdot').is(":visible")){
        $(this).siblings('.menu-navdot').hide();
    }else{
        $(this).siblings('.menu-navdot').show();
    }
});

$(document).on('click','#borrar_video',function(){
    let id_post = $(this).parents('.post').find('input[name=id_post]').val();
    let id_user = $(this).parents('.post').find('input[name=id_user]').val();
    $.ajax({
        url: '/ajax/delete_video',
        type: 'post',
        data: {
            id_post: id_post,
            id_user:id_user,
        },
        success: function (response) {
            if (response['delete']) {
                $('#post_'+response['id_post']).remove();
            }
        }
    });
});