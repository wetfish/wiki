var express = require('express'),
    app     = require('express')(),
    server  = require('http').createServer(app),
    io      = require('socket.io').listen(server),
    count   = 0;

server.listen(1337);
app.use(express.static(__dirname + '/static'));

app.get('/', function (req, res)
{
  res.sendfile(__dirname + '/index.html');
});

io.sockets.on('connection', function (socket)
{
    io.sockets.emit('HELLO! :)');

    socket.on('disconnect', function ()
    {
        io.sockets.emit('GOODBYE :(');
    });
});
