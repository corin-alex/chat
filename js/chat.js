/**
 * Fonction pour recuperer les derniers messages
 */
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
                         var name = msg[i].author.name;
                         var picture = msg[i].author.picture;
                         var text = msg[i].text;
                         var time = msg[i].time;
                         
                         result += '<li><div class="msg_avatar">';
                         result += (picture) ? '<img src="' + picture + '">' : '<img src="img/48x48.png">';
                         result += '</div><div class="msg_content"><strong>' + name + "</strong> - <em>" + time + "</em><br>" + text + "<br><br></div></li>";
                    }
                    if(!result) result = "Aucun message";
                    $("#msg_list").html(result);
                    $("#msg_list").animate({ scrollTop:  5000 }, 250);
               }
          }
     });
}

/**
 * Fonction pour envoyer un message
 */
function sendMessage() {
     $.ajax({
          type:"GET",
          url:"ajax.php",
          dataType:"json",
          data: {"action": "sendMessage",
                 "user": $("#userSelect").val(),
                 "msg": $("#msgInput").val().trim()},
          success: function() {
               getMessages();
               $("#msgInput").val("");
          }
     });
}

/**
 * Fonction de connexion utilisateur
 */
function login() {
     $.ajax({
          type:"GET",
          url:"ajax.php",
          dataType:"json",
          data: {"action": "login",
                 "name": $("#loginName").val().trim(),
                 "password": $("#loginPassword").val()},
          success: function(msg) {
               var headerContent = "<span>" + msg.name + "</span>";
               headerContent += (msg.picture) ? '<img src="' + msg.picture + '">' : '<img src="img/48x48.png">';
               
               $("#sidebar header").html(headerContent);
               $("#userSelect").val(msg.id);
               getOnlineUsersList();
               
               $("#login-panel").fadeOut('fast', function() {
                    $("#chatbody").delay(500).fadeIn('slow');
               });
          }
     });
}

/**
 * Fonction pour afficher la liste des utilisateurs
 */
function getOnlineUsersList() {
     $.ajax({
          type:"GET",
          url:"ajax.php",
          dataType:"json",
          data: {"action": "getOnlineUsersList"},
          success: function(msg) {
               if(msg){
                    var result = '<ul class="userlist"><li>Utilisateurs enregistrés</li>';
                    var count = 0;
                    
                    var currentUser = $("#userSelect").val();
                    
                    for (var i = 0; i < msg.length; i++) {
                         // Si utilisateur courrant
                         if (currentUser ==  msg[i].id) {
                              // Si non connecté, on cache le chat
                              if (!msg[i].logged_in) {
                                   $("#chatbody").fadeOut('fast');
                                   $("#login-panel").delay(500).fadeIn('fast');
                              }
                         }
                         
                         if (msg[i].logged_in)  {
                              result += '<li class="user_online"><span class="bull bull_online"></span>';
                              count++;
                         }
                         else {
                              result += '<li><span class="bull"></span>';
                         }

                         result += msg[i].name + "</li>";
                    }
                    result += "</ul>";
                    $("#onlineuser").html(result);
                    $("#sidebar footer").html("Utilisateurs en ligne : " + count);
               }
          }
     });
}
