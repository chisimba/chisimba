var config = {user: "", password: "", track: ["chisimba"]};
var server = require("http").createServer();
var socket = require("socket.io").listen(server);
var twitter = new (require("twitter-node").TwitterNode)(config);

server.listen(8080);

twitter.addListener("tweet", function(tweet) {
    socket.broadcast(JSON.stringify(tweet));
});

twitter.stream();
