(function() {
 function addUser(user) {
  var added = false;
  $("#users li").each(function() {
   if (!added) {
    if ($(this).text() > user) {
     $("<li></li>").text(user).insertBefore(this);
     added = true;
    }
   }
  });
  if (!added) {
   $("<li></li>").text(user).appendTo("#users");
  }
 }

 function log(entry) {
  var d = new Date();
  var hour = d.getHours();
  var mins = d.getMinutes();
  if (hour < 10) hour = "0" + hour;
  if (mins < 10) mins = "0" + mins;
  entry.append("<span>"+hour+":"+mins+"</span>").appendTo("#log");
  document.getElementById("log").scrollTop += 999999;
 }

 var script = document.createElement("script");
 script.src = "http://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js";
 script.onload = function() {
  $.getScript("http://" + location.host + "/socket.io/socket.io.js", function() {
   var socket = new io.Socket(location.hostname, {"port": location.port});

   socket.on("connect", function() {
    $("#login").css("display", "none");
    $("#chat").css("display", "block");
    log($("<li>Connected</li>"));
    socket.send($("#login input").val());
    $("#chat input").focus();
   });

   socket.on("disconnect", function() {
    log($("<li>Disconnected</li>"));
    $("#users li").remove();
    $("#chat").css("display", "none");
    $("#login").css("display", "block");
    $("#login input").removeAttr("disabled");
    $("#login input").focus();
   });

   socket.on("message", function(message) {
    message = JSON.parse(message);
    if (message[0] == "users") {
     for (var i = 0; i < message[1].length; i++) {
      addUser(message[1][i]);
     }
    } else if (message[0] == "join") {
     addUser(message[1]);
     log($("<li></li>").text(message[1] + " joined the chat"));
    } else if (message[0] == "left") {
     $("#users li").each(function() {
      if ($(this).text() == message[1]) {
       $(this).remove();
      }
     });
     log($("<li></li>").text(message[1] + " left the chat"));
    } else if (message[0] == "message") {
     log($("<li></li>").text(message[1]));
    }
   });

   $("#chat").submit(function() {
    var message = $("#chat input").val();
    if (message.length) {
     socket.send(message);
     $("#chat input").val("");
     $("#error").text("");
    } else {
     $("#error").text("Please enter a message to send.");
    }
    return false;
   });

   $("#login").submit(function() {
    if ($("#login input").val().length) {
     $("#login input").attr("disabled", "disabled");
     log($("<li>Connecting...</li>"));
     $("#error").text("");
     socket.connect();
    } else {
     $("#error").text("Please enter a nickname.");
    }
    return false;
   });

   $("#login input").removeAttr("disabled").focus();
   log($("<li>Initialised. Enter your nickname.</li>"));
  });
 };
 document.getElementsByTagName("head")[0].appendChild(script);
})();
