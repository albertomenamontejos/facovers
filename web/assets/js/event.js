import {reloadEvents} from './app';
$(document).ready(function(){
    $(document).on('click','#inscripcion',function(){
        let evento = $(this).parents('.evento');
        let id_event = evento.find('input[name=id_event]').val();
        $.ajax({
            url: '/ajax/event_inscrip',
            type: 'post',
            data: {
                id_event: id_event,
            },
            success: function (response) {
                if (response['ok']) {
                    let asistentes = evento.find('#asistentes').text();
                    evento.find('#asistentes').text((+asistentes) + 1);
                    evento.find('#inscripcion').remove();
                    let button = document.createElement('button');
                    button.innerHTML = 'Borrar inscripción';
                    button.setAttribute('id', 'remove_inscripcion');
                    evento.find('.cabecera_der').append(button);
                    reloadEvents(response.id_user);

                }
            }
        });
    });

    $(document).on('click','#remove_inscripcion',function(){
        let evento = $(this).parents('.evento');
        let id_event = evento.find('input[name=id_event]').val();
        $.ajax({
            url: '/ajax/event_remove_inscrip',
            type: 'post',
            data: {
                id_event: id_event,
            },
            success: function (response) {
                if (response['ok']) {
                    let asistentes = evento.find('#asistentes').text();
                    evento.find('#asistentes').text((+asistentes) - 1);
                    evento.find('#remove_inscripcion').remove();
                    let button = document.createElement('button');
                    button.innerHTML = 'Inscribirse';
                    button.setAttribute('id', 'inscripcion');
                    evento.find('.cabecera_der').append(button);
                    reloadEvents(response.id_user);
                }
            }
        });
    });

    $(document).on('click','#borrar_evento',function(){
        let evento = $(this).parents('.evento');
        let id_event = evento.find('input[name=id_event]').val();
        $.ajax({
            url: '/ajax/borrar_evento',
            type: 'post',
            data: {
                id_event: id_event,
            },
            success: function (response) {
                if (response['ok']) {
                    reloadEvents(response.id_user);
                    evento.remove();
                }
            }
        });
    });
});