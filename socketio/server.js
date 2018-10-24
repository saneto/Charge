var app = require('http').createServer();
var io = require('socket.io')(app);

app.listen(8081);

// on attent les connexions
io.sockets.on('connection', function (socket) {
    console.log('socket', "connection");

    // on rejoint le salon "room" quand une socket le demande
    socket.on('room', function (room) {
        socket.join(room);
        console.log('socket join', room);
    });

    // on quitte un salon
    socket.on('room.leave', function (room) {
        socket.leave(room, function () {
            console.log('socket leave', room);
        });
    });

    io.sockets.in("gestionnaires").emit('message', "un vendeur se connecte aux gestionnaires");
});
