import {postAjaxIndex} from './app';

$(document).ready(function () {
    let pagina = $('.posts').find('input[name="page"]').val();
    if (pagina) {
        $(window).scroll(function () {
            if ($(window).scrollTop() == $(document).height() - $(window).height()) {
                let url;
                let offset = $('.posts').find('input[name="offset"]').val();
                let data;
                if(pagina == 'index_app'){
                    url = '/ajax/ajax_post_index';
                    data =  {
                        offset: offset,
                    };
                }else if(pagina == 'perfil_app'){
                    url = '/ajax/ajax_post_perfil';
                    data =  {
                        offset: offset,
                        user_id: $('.mi-perfil').find('input[name="user_id"]').val(),
                    };
                }
                // console.log(offset);
                if (offset != 'null') {
                    $.ajax({
                        type: 'post',
                        url: url,
                        data: data,
                        beforeSend: function () {
                            $('#loader').show();
                        },
                        success: function (response) {
                            $('.posts').append(response.html);
                            if (response.max < 9) {
                                offset = 'null';
                            } else {
                                offset = (+$('.posts').find('input[name="offset"]').val()) + (+response.max);
                            }
                            $('.posts').find('input[name="offset"]').val(offset);
                        },
                        complete: function () {
                            $('#loader').hide();
                        }
                    });
                }
            }
        });
    }
});