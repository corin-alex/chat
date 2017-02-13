function getMessages() {
     $.ajax({
          type:"GET",
          url:"ajax.php",
          dataType:"json",
          data: {"action": "getMessages"},
          success: function(msg) {
               if(msg){
                    var result = "";
                    for (var i = 0; i < msg.length; i++) {
                         var name = msg[i]['author']['name'];
                         var text = msg[i]['text'];
                         var time = msg[i]['time'];

                         result += '<li class="list-group-item">[' + time + '] - <strong>' + name + " :</strong> " + text + "</li>";
                    }
                    if(!result) result = "Aucun message";
                    $("#msg_list").html(result);
                    $("#msg_list").animate({ scrollTop:  5000 }, 250);
               }
          }
     });
}

function sendMessage() {
     var uid = $("#userSelect").val();
     var msg = $("#msgInput").val().trim();

     $.ajax({
          type:"GET",
          url:"ajax.php",
          dataType:"json",
          data: {"action": "sendMessage", "user": uid, "msg": msg},
          success: function(msg) {
               getMessages();
               $("#msgInput").val("");
          }
     });
}

function login() {
     var name = $("#loginName").val().trim();
     var pwd  = $("#loginPassword").val();

     $.ajax({
          type:"GET",
          url:"ajax.php",
          dataType:"json",
          data: {"action": "login", "name": name, "password": pwd},
          success: function(msg) {
               $("#sidebar header").html(msg['name'] + '<img src="' + msg['picture'] + '">');
               $("#userSelect").val(msg['id']);
               $(".login-panel").fadeOut('fast');
               $("main, #sidebar").fadeIn('fast');
               getOnlineUsersList();
               getOnlineUsersCount();
          }
     });
}
function getOnlineUsersList() {
     $.ajax({
          type:"GET",
          url:"ajax.php",
          dataType:"json",
          data: {"action": "getOnlineUsersList"},
          success: function(msg) {
               if(msg){
                    var result = '<ul class="userlist">';
                    for (var i = 0; i < msg.length; i++) {
                         if (msg[i]['logged_in'])  {
                              result += '<li class="logged_in">';
                         }
                         else {
                              result += '<li>';
                         }

                         result += msg[i]['name'];

                         result += "</li>";
                    }
                    result += "</ul>";
                    $("#onlineuser").html(result);
               }
          }
     });
}

function getOnlineUsersCount() {
     $.ajax({
          type:"GET",
          url:"ajax.php",
          dataType:"json",
          data: {"action": "getOnlineUsersCount"},
          success: function(msg) {
               if(msg){
                    $("#sidebar footer").html("Utilisateurs en ligne : " + msg['count']);
               }
          }
     });
}
