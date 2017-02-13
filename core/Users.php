<?php
final class Users {
     /**
      * Méthode de connexion utilisateur
      * Si l'utilisateur n'existe pas, il est automatiquement créé
      */
     public static function loginOrCreate($name, $password) {
          // On récupère l'objet database
          $db = Database::getInstance();

          // On cherche si le nom d'utilisateur existe
          $q = $db->prepare("SELECT id FROM users WHERE name = :name");
          $q->bindValue(":name", strtolower($name), PDO::PARAM_STR);
          $q->execute();
          $u = $q->fetch();
          
          // Si il n'existe pas, on le crée
          if (empty($u)) {
               $q = $db->prepare("INSERT INTO users (`name`, `password`, `picture`, `last_login`) VALUES (:name, :password, '', :currentTime)");
               $q->bindValue(":name", trim(strtolower($name)), PDO::PARAM_STR);
               $q->bindValue(":password", sha1($password), PDO::PARAM_STR);
               $q->bindValue(":currentTime", time(), PDO::PARAM_INT);
               $q->execute();
          }

          // Dans tous les cas on check le mdp
          $q = $db->prepare("SELECT id, name, picture, last_login FROM users WHERE name = :name AND password = :password");
          $q->bindValue(":name", strtolower($name), PDO::PARAM_STR);
          $q->bindValue(":password", sha1($password), PDO::PARAM_STR);
          $q->execute();
          $u = $q->fetch();

          // Si l'user et le mdp correspondent, on met à jour le temps de dernière connexion
          // et on renvoi les infos concernant l'utilisateur
          if (!empty($u)) {
               self::updateLastLogin($u->id);
               return array('id' => $u->id, 'name' => ucfirst($u->name), 'picture'=> $u->picture);
          }

          // Dans les autres cas on renvoi null
          return null;
     }
     
     public static function updateLastLogin($uid) {
          $db = Database::getInstance();
          
          $q = $db->prepare("UPDATE users SET last_login=:currentTime WHERE id=:id");
          $q->bindValue(":id", $uid, PDO::PARAM_INT);
          $q->bindValue(":currentTime", time(), PDO::PARAM_INT);
          $q->execute();
     }

     /**
      * Méthode pour recuperer la liste des utilisateurs
      */
     public static function getOnlineUsersList() {
          // On récupère l'objet database
          $db = Database::getInstance();

          // On récupère la liste de tous les utilisateurs
          $q = $db->prepare("SELECT id, name, picture, last_login FROM users");
          $q->execute();
          $userList = $q->fetchAll(PDO::FETCH_ASSOC);

          // Si notre liste n'est pas vide
          if (!empty($userList)) {
               // Pour chaque utilisateur, si le temps de derninère connexion est inférieur
               // à 5 minutes (300 secondes) on considère qu'il est connecté
               for($i = 0; $i < count($userList); $i++) {
                    if ((time() - $userList[$i]['last_login']) < 300) {
                         $userList[$i]['logged_in'] = true;
                    }
                    else {
                         $userList[$i]['logged_in'] = false;
                    }
                    
                    // On met la premiere lettre du nom on majuscule
                    $userList[$i]['name'] = ucfirst($userList[$i]['name']);
               }

               // On retourne la liste des utilisateurs
               return $userList;
          }
          
          // Sinon un tableau vide
          return array();
     }
}
