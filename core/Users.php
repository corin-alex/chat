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

          $q = $db->prepare("SELECT id, name FROM users WHERE name = :name AND password = :password");
          $q->bindValue(":name", strtolower($name), PDO::PARAM_STR);
          $q->bindValue(":password", sha1($password), PDO::PARAM_STR);
          $q->execute();

          $u = $q->fetch();

          if (!empty($u)) {
               return array('id' => $u->id, 'name' => ucfirst($u->name));
          }

          return null;
     }
}
