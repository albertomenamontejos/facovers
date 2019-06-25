'use strict';
$(document).ready(function(){
    $(document).on('click','.icon-like',function(e){
        let  id_user_session = $(this).parents('.post').find('input[name=id_user_session]').val();
        let  id_post = $(this).parents('.post').find('input[name=id_post]').val();
        let likes = $(this).parents('.post').find('.num_likes');
        let num_likes = likes.text();
        if(id_user_session){
            $.ajax({
                url: '/ajax/ajax_like',
                type:'post',
                data:{
                    id_user: id_user_session,
                    id_post: id_post,
                },
                success:function(response){
                    if(response[id_post]['like']){
                        $('#icon_'+id_post)
                            .removeClass('icon-cor')
                            .addClass('icon-cor-rojo');
                       likes.text((+num_likes)+1);
                    }else{
                        $('#icon_'+id_post)
                            .removeClass('icon-cor-rojo')
                            .addClass('icon-cor');
                      likes.text((+num_likes)-1);

                    }
                }

            });
        }else{
            $('#popup1').show();
        }

    });

});
