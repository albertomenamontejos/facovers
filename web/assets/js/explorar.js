$(document).ready(function(){

    $(document).on('click','#ver_mas_comentados',function(){
        let offset = $('#offset_comentados').val();
        if(offset != 'null') {

            $.ajax({
                url: '/ajax/ajax_explorar',
                type: 'post',
                data: {
                    offset: offset,
                    seccion: 'comentados',
                },
                success: function (response) {
                    if (response.max < 3) {
                        offset = 'null';
                    }else{
                        $('#posts_comentados').append(response.html);
                        offset = (+offset) + (+response.offset);
                        $('#ver_mas_comentados').hide();
                    }

                    $('#offset_comentados').val(offset);
                }
            });
        }
    });


    $(document).on('click','#ver_mas_likes',function(){
        let offset = $('#offset_likes').val();
        if(offset != 'null') {
            $.ajax({
                url: '/ajax/ajax_explorar',
                type: 'post',
                data: {
                    offset: offset,
                    seccion: 'likes',
                },
                success: function (response) {
                    if (response.max < 3) {
                        offset = 'null';
                    }else{
                        $('#posts_likes').append(response.html);
                        offset = (+offset) + (+response.offset);
                        $('#ver_mas_likes').hide();
                    }
                    $('#offset_likes').val(offset);
                }
            });
        }
    });
});

