// assets/js/app.js
const routes = require('../../js/fos_js_routes.json');
// const Rounting =  require('../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js');
const Routing = require('./Components/Routing');
Routing.setRoutingData(routes);
require('@fortawesome/fontawesome-free/css/all.min.css');
require('@fortawesome/fontawesome-free/js/all.js');
require('../scss/main.scss');
require('../js/likes.js');
require('../js/follow.js');
require('../js/comments.js');
require('../js/nav_post.js');
require('../js/index.js');
require('../js/perfil.js');
require('../js/search.js');
require('../js/explorar.js');
require('../js/config.js');
require('../js/event.js');
require('../js/chat/chat.js');
// require('../js/chat/user.js');

//MEnsaje si ha subido un video,por ejemplo.
$('.mensaje-confirmacion-ajax').fadeIn(800).delay(3000).fadeOut(800);

export function reloadAside(){
    $('#lista_seguidores').empty();
    $.ajax({
        url:'/ajax/user_aside',
        success:function(response){
            if(response['aside'].html){
                $('#aside_lista_seguidores').html(response['aside'].html)
            }
        }
    });
}

export function reloadStadistics(user_id){
    $('.est-seguidores .result').empty();
    $.ajax({
        url:'/ajax/reload_stadistics',
        data:{
            user_id:user_id
        },
        success:function(response){
            if(response['estadisticas'].num_followers){
                $('.est-seguidores .result').html(response['estadisticas'].num_followers);
            }
        }
    });
}

export function reloadVideos(user_id){
    $('.est-videos .result').empty();
    $.ajax({
        url:'/ajax/reload_videos',
        data:{
            user_id:user_id
        },
        success:function(response){
            if(response['estadisticas'].num_videos){
                $('.est-videos .result').html(response['estadisticas'].num_videos);
            }
        }
    });
}

export function reloadFollowers(user_id){
    $('#lista_seguidores').empty();
    $.ajax({
        url:'/ajax/reload_followers',
        type:'post',
        data:{
          user_id:user_id
        },
        success:function(response){
            let html='';
            let photo = "";
            if(response['users']){
                $.each(response['users'],function(key,value){
                    if(value.photo == null){
                        photo = 'https://ui-avatars.com/api/?size=180&name='+ value.username + '&color=6c6c6c&background=f2f2f2';
                    }
                    let path = Routing.generate('perfil',{'username':value.username});
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
                });
            }
            $('#lista_seguidores').html(html);
        }
    });
}
export function reloadEvents(user_id){
    let evento = $('.caja_eventos');
    $.ajax({
        url:'/ajax/reload_events',
        type:'post',
        data:{
          user_id:user_id
        },
        success:function(response){
            evento.empty();
            evento.append(response['html']);
        }
    });
}

export function cambiarClases(id_user,follow){
    if(follow){
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

$(document).on('click','#cerrar_popup',function(){
    $('#popup1').hide();
});

//PAGINACION CON SCROLL
// $(window).scroll(function() {
//     if($(window).scrollTop() == $(document).height() - $(window).height()) {
//       $('#loader').show();
//     }
// });