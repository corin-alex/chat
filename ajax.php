<?php
// On definit le type de contenu qu'on va renvoyer et l'encodage
//header('Content-Type: application/json; charset=utf-8');

// Petite fonction qui nous permet d'afficher une erreur
function showError($msg) {
     echo json_encode(array('error' => $msg));
     exit;
}

// Si aucune action demandée
if (empty($_GET['action'])) {
     showError("Aucune action demandée");
}

// Nos inclusions
require_once ("core/Database.php");
require_once ("core/Users.php");
require_once ("core/Sessions.php");
require_once ("core/Chat.php");

// On traite l'action demandée
switch ($_GET['action']) {
     case 'getMessages' :
          echo Chat::getMessages();
          break;
     case 'sendMessage' :
          $user = $_GET['user'];
          $msg = $_GET['msg'];

          if (!empty($user) and !empty($msg))
          {
               Chat::sendMessage($user, $msg);
          }
          echo json_encode(array('message' => "1"));
          exit;
          break;
     case 'login' :
          break;
     case 'logout' :
          break;
     case 'getOnlineUsersCount' :
          break;
     case 'getOnlineUsersList' :
          break;
     default :
          showError("Action invalide");
}
