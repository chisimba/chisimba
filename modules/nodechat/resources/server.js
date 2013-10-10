var paperboy = require("paperboy");
var path = require("path");
var sys = require("sys");

var server = require("http").createServer(function(req, res) {
 var ip = req.connection.remoteAddress;
 paperboy
  .deliver(path.dirname(__filename), req, res)
  .addHeader('Expires', 300)
  .addHeader('X-PaperRoute', 'Node')
  .before(function() {
   sys.log('Received Request');
  })
  .after(function(statCode) {
   sys.log(statCode, req.url, ip);
  })
  .error(function(statCode, msg) {
   res.writeHead(statCode, {'Content-Type': 'text/plain'});
   res.end("Error " + statCode);
   sys.log(statCode, req.url, ip, msg);
  })
  .otherwise(function(err) {
   res.writeHead(404, {'Content-Type': 'text/plain'});
   res.end("Error 404: File not found");
   sys.log(404, req.url, ip, err);
  });
});

var socket = require("socket.io").listen(server);
socket.on("connection", function(client) {
 client.on("message", function(message) {
  if (client.nick) {
   socket.broadcast(JSON.stringify(["message", client.nick + ": " + message]));
  } else {
   var users = Array();
   var keys = Object.keys(socket.clients);
   for (var i = 0; i < keys.length; i++) {
    if (socket.clients[keys[i]].nick) {
     users.push(socket.clients[keys[i]].nick);
    }
   }
   client.send(JSON.stringify(["users", users]));
   client.nick = message;
   socket.broadcast(JSON.stringify(["join", message]));
  }
 });
 client.on("disconnect", function() {
  socket.broadcast(JSON.stringify(["left", client.nick]));
 });
});

server.listen(8080);
