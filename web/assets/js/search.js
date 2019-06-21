'use strict';

$(document).ready(function () {
    $('#btn_search').click(function () {
        let searchRequest = null;
        let minlength = 1;
        let value = $(this).siblings('#search').val();
         searchAjax(searchRequest,minlength,value);
    });

    $('#search').on('keyup',function(e) {
        let value = $(this).val();
        let searchRequest = null;
        let minlength = 1;
        if (e.which == 13 ) {
            searchAjax(searchRequest, minlength, value);
        }else
        if(!value){
            $('.post-error').hide();
            $('.post').show();
        }
    });

});

function searchAjax(searchRequest,minlength,value) {

    searchRequest = $.ajax({
        type: 'post',
        url: 'ajax/ajax_search',
        data: {
            'q': value
        },
        dataType: 'text',
        success: function (msg) {
                let result = JSON.parse(msg);
            $('.post').hide();
            if(result.posts.error){
                    $('#posts').append(result.posts.error.html)
                }else{
                    //Ajax para mostrar los videos buscados
                    $.ajax({
                        type: 'post',
                        url: '/ajax/ajax_searchShowPost',
                        data: {
                            'posts': result
                        },
                        dataType: 'text',
                        success: function (respuesta) {
                            let json = JSON.parse(respuesta);
                            $('.post-error').remove();
                            $('#posts').append(json.html)
                        }
                    });
                }

                // $.each(result, function (key, arr) {
                //     $.each(arr, function (id, value) {
                //         if (key === 'posts') {
                //             if (id !== 'error') {
                //
                //             } else {
                //                 $('.posts').hide();
                //                 //Sacar mensaje No se han encontrado resultados
                //
                //             }
                //         }
                //     });
                // });
            }
    });
}