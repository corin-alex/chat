<?php
final class Chat {
     /**
      * Methode qui récupère les derniers messages
      *
      * @method quickQuery
      * @param  int           $limit Limite de messages
      * @return string        json Result
      */
     public static function getMessages($limit = 13) {
          // On récupère l'objet database
          $db = Database::getInstance();


          $count = $db->quickQuery("SELECT count(id) as c FROM messages")[0]->c;
          $l = (($count - $limit) > 0) ?  $count - $limit : 0;
          $limitQ = "LIMIT " .$l . ", " . $limit;

          // Requête pour récuperer les dernièrs messages
          $messages = $db->quickQuery("SELECT * FROM messages ORDER BY `time` ASC " . $limitQ);

          // Pour chaque message, on refait une requête afin de récuperer l'auteur
          // TODO : Formater la date
          for ($i = 0; $i < count($messages); $i++) {
               // On demande seulement le nom et l'avatar pour l'id de l'auteur
               $q = $db->prepare("SELECT name, picture FROM users WHERE id = :id");
               // On bind la valeur id et on lui force le format int
               $q->bindValue(":id", $messages[$i]->author, PDO::PARAM_INT);
               // On execute la requête
               $q->execute();
               // On récupère le premier resultat
               // (normalement il devrait y en avoir qu'un seul)
               $author = $q->fetch();
               $author->name = ucfirst($author->name);
               // On ajoute les infos dans le param author du message courrant
               $messages[$i]->author = $author;

               $messages[$i]->time = date("d/m H:i", $messages[$i]->time);


               // Emoticones
               $messages[$i]->text = self::emoticons($messages[$i]->text);
          }

          // On retourne le resultat au format json
          return json_encode($messages);
     }

     public static function emoticons($str) {
          $db = Database::getInstance();
          $q = $db->prepare("SELECT txt, img FROM emoticons");
          $q->execute();
          $icons = $q->fetchAll();

          foreach ($icons as $icon) {
               $str = str_ireplace($icon->txt, '<img class="emoticon" src="' . $icon->img . '">', $str);
          }

          return $str;
     }

     public static function sendMessage($userId, $msg) {
          // On récupère l'objet database
          $db = Database::getInstance();

          $q = $db->prepare("INSERT INTO messages (`author`, `text`, `time`) VALUES (:id, :msg, :currentTime)");
          $q->bindValue(":id", $userId, PDO::PARAM_INT);
          $q->bindValue(":msg", trim(strip_tags($msg)), PDO::PARAM_STR);
          $q->bindValue(":currentTime", time(), PDO::PARAM_INT);
          $q->execute();

          Users::updateLastLogin($userId);
     }
}
