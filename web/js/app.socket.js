var socket = io.connect('http://localhost:8081');

socket.on('disconnect', function () {
    socket.emit('room.leave', "gestionnaires");
});

socket.on('message', function (data) {
    console.log('socket message', data);
});

/**
 * Le serveur envoi une notification.
 */
socket.on('toasts.broadcast', function (data) {
    console.log('socket.io', data);
    console.log($('#toasts'));

    // création de la notification
    var $notification = $('<div/>').hide().text(data.text);
    $notification.addClass('alert alert-' + data.type).slideDown(150, function () {
        var $self = $(this);
        setTimeout(function () {
            $self.slideUp(150, function () { $(this).remove() });
        }, 3000);
    });
    // ajout de la notification dans la liste
    $('#toasts').append($notification);
    // si on a plus de 3 notifs, on supprime la plus ancienne (la première)
    if($('#toasts').children().length > 3) {
        $('#toasts div:not(".close")').first().addClass('close').slideUp(150, function () { $(this).remove() });
    }
});