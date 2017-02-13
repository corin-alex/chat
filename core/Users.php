<?php
final class Users {
     public static function loginOrCreate($name, $password) {
          // On récupère l'objet database
          $db = Database::getInstance();

          $q = $db->prepare("SELECT id FROM users WHERE name = :name");
          $q->bindValue(":name", strtolower($name), PDO::PARAM_STR);
          $q->execute();

          $u = $q->fetch();
          if (empty($u)) {
               $q = $db->prepare("INSERT INTO users (`name`, `password`, `picture`, `last_login`) VALUES (:name, :password, '', :currentTime)");
               $q->bindValue(":name", trim(strtolower($name)), PDO::PARAM_STR);
               $q->bindValue(":password", sha1($password), PDO::PARAM_STR);
               $q->bindValue(":currentTime", time(), PDO::PARAM_INT);
               $q->execute();
          }

          $q = $db->prepare("SELECT id, name, picture, last_login FROM users WHERE name = :name AND password = :password");
          $q->bindValue(":name", strtolower($name), PDO::PARAM_STR);
          $q->bindValue(":password", sha1($password), PDO::PARAM_STR);
          $q->execute();

          $u = $q->fetch();

          if (!empty($u)) {
               $q = $db->prepare("UPDATE users SET last_login=:currentTime WHERE id=:id");
               $q->bindValue(":id", $u->id, PDO::PARAM_INT);
               $q->bindValue(":currentTime", time(), PDO::PARAM_INT);
               $q->execute();

               return array('id' => $u->id, 'name' => ucfirst($u->name), 'picture'=> $u->picture);
          }

          return null;
     }

     public static function getOnlineUsersList() {

          $db = Database::getInstance();

          $q = $db->prepare("SELECT id, name, picture, last_login FROM users");
          $q->execute();
          $u = $q->fetchAll(PDO::FETCH_ASSOC);

          if (!empty($u)) {
               for($i = 0; $i < count($u); $i++) {
                    if ((time() - $u[$i]['last_login']) < 300) {
                         $u[$i]['logged_in'] = true;
                    }
                    else {
                         $u[$i]['logged_in'] = false;
                    }
               }

               return $u;
          }
          return null;
     }
     public static function getOnlineUsersCount(){
          $db = Database::getInstance();

          $q = $db->prepare("SELECT id, last_login FROM users");
          $q->execute();
          $u = $q->fetchAll();

          $count = 0;

          foreach ($u as $user) {
               if ((time() - $user->last_login) < 300) $count++;
          }

          return array('count' => $count);
     }
}
