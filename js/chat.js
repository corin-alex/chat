function getMessages() {
     $.ajax({
          type:"GET",
          url:"ajax.php",
          dataType:"json",
          data: {"action": "getMessages"},
          success:function(msg){
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
     })
}
