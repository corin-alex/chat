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
               $("#username").html(msg['name']);
               $("#userSelect").val(msg['id']);

               $(".login-panel").fadeOut('fast');
               $(".chat-panel").fadeIn('fast');
          }
     });
}
