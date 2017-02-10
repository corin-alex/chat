<?php
final class Chat {
     /**
      * Methode qui récupère les derniers messages
      *
      * @method quickQuery
      * @param  int           $limit Limite de messages
      * @return string        json Result
      */
     public static function getMessages($limit = 10) {
          // On récupère l'objet database
          $db = Database::getInstance();

          // Requête pour récuperer les dernièrs messages
          $messages = $db->quickQuery("SELECT * FROM messages ORDER BY `time` ASC LIMIT " . intval($limit));

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

               // On ajoute les infos dans le param author du message courrant
               $messages[$i]->author = $author;

               $messages[$i]->time = date("d/m H:i", $messages[$i]->time);

          }

          // On retourne le resultat au format json
          return json_encode($messages);
     }
}
