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

app.get('/:page', function (req, res)
{
    res.send(req.params.page)    
});

app.get('/:page/edit', function(req, res)
{
    res.send("Hey there this is the edit page for "+req.params.page);
});

io.sockets.on('connection', function (socket)
{
    io.sockets.emit('HELLO! :)');

    socket.on('disconnect', function ()
    {
        io.sockets.emit('GOODBYE :(');
    });
});
