'use strict';

$(document).ready(function () {
    $('#btn_search').click(function () {
        let searchRequest = null;
        let minlength = 1;
        let value = $(this).siblings('#search').val();
        let uri = $(document).find('input[name=uri]').val();
        searchAjax(searchRequest, minlength, value,uri);
        $('.page-explorar .seccion').hide();
        $('.post-error').remove();
    });

    $('#search').on('keyup',function(e) {
        let value = $(this).val();
        let searchRequest = null;
        let minlength = 1;
        if (e.which == 13 && value) {
            let uri = $(document).find('input[name=uri]').val();
            searchAjax(searchRequest, minlength, value,uri);
            $('.page-explorar .seccion').hide();
            $('.post-error').remove();
        }else if(!value){
            $('.post-error').hide();
            $('.page-explorar .seccion').show();
            $('.page-explorar .post').show();
            $('.page-explorar .resultado_busqueda').hide();
            $('.post').show();
        }
    });

});

function searchAjax(searchRequest,minlength,value,uri) {
    let  id_user_session = $(document).find('input[name=id_user_session]').val();

    searchRequest = $.ajax({
        type: 'post',
        url: '/ajax/ajax_search',
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
                        if(uri == '/explorar/' || !id_user_session){
                            $('.post-error').remove();
                            $('.page-explorar .seccion').hide();
                            $('.resultado_busqueda').hide();
                            let posts = document.createElement('div');
                            posts.setAttribute('class','resultado_busqueda posts')
                            // posts.append(json.html);
                            $('.page-explorar').append(posts);
                            $('.resultado_busqueda').append(json.html);
                        }else{
                            let posts = document.createElement('div');
                            posts.setAttribute('class','resultado_busqueda posts');
                            // posts.append(json.html);
                            $('.posts').hide();
                            $('#loader').before(posts);
                            $('.post-error').remove();
                            $('.resultado_busqueda').append(json.html)
                        }
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