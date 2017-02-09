<?php
/**
 * Gestion de la base de données
 *
 * @package Chat Beweb
 * @author Corin Alexandru
 * @copyright 2014-2017 Corin Alexandru
 * @license MIT
 * @link --
 */

final class Database
{
     private $_connexion;
     private $_requestsNumber;
     private static $_instance;
     /**
      * Constuctor
      *
      * @method __constuct
      * @return void
      */
     private function __constuct()
     {
          $this->_connexion = NULL;
          $this->_requestsNumber = 0;
     }
     /**
      * Singleton
      *
      * @method getInstance
      * @return object _instance
      */
     public static function getInstance()
     {
          if (!(self::$_instance instanceof self))
          {
               self::$_instance = new self();
          }
          return self::$_instance;
     }
     /**
      * Connexion au serveur
      * Si la connexion existe, retourne la connexion existante
      *
      * @method connect
      * @return object _connexion
      */
     private function connect()
     {
          require_once(__DIR__ . '/../config.php'); // Configuration SQL
          if (!$this->_connexion)
          {
               $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PWD, array(PDO::ATTR_PERSISTENT => true));
               $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
               $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
               $names = $db->query("SET NAMES 'utf8'"); // Indique au serveur que nous travaillons en UTF8
               $names->closeCursor();
               $this->_connexion = $db; // On recupère la connexion
          }

          return $this->_connexion;
     }
     /**
      * Se connecte puis prépare la requete
      *
      * @method prepare
      * @param  string  $req Query
      * @return object
      */
     public function prepare($req)
     {
          $this->_requestsNumber ++;
          return $this->connect()->prepare($req);
     }
     /**
      * Requete rapide (une ligne)
      *
      * @method quickQuery
      * @param  string     $q Query
      * @return object        Result
      */
     public function quickQuery($q)
     {
          $sql = $this->prepare($q);
          $sql->execute();
          $result = $sql->fetchAll();
          $sql->closeCursor();
          return $result;
     }
     /**
      * Ecriture rapide
      * Insert / Update
      *
      * @method quickWrite
      * @param  string     $q Query
      * @param  string     $b Bind
      * @param  var        $v Variable
      * @param  int        $t PDO::type
      * @return bool
      */
     public function quickWrite($q, $b = ':none', $v = null, $t = PDO::PARAM_INT)
     {
          $sql = $this->prepare($q);
          $sql->bindValue($b, $v, $t);
          return $sql->execute();
     }
     /**
      * Renvoie le nombre total de requetes effectuées
      *
      * @method getReqNumber
      * @return int      Request number
      */
     public function getReqNumber()
     {
          return (int) $this->_requestsNumber;
     }
}
