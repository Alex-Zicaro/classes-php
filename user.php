<?php
session_start();

class Database
{

    private static $_dbHost = "locahost";
    private static $_dbUser = "root";
    private static $_dbPassword = "";
    private static $_dbName = "classes";
    private static $_bdd = null;

    public function __construct()
    {
        die('la fonction d\'initialisation n\'est pas autorisée');
    }
    public static function bdd()
    {
        if (self::$_bdd == NULL) {


            self::$_bdd = new mysqli("localhost", "root", "", "classes");
            if (self::$_bdd->connect_error) {
                die("Connection failed: " . self::$_bdd->connect_error);
            }
        }
        return self::$_bdd;
    }
    public static function deco_bdd()
    {
        return self::$_bdd = null;
    }
}
class User extends Database
{

    private $_id;
    public $_login;
    public $_email;
    public $_firstname;
    public $_lastname;

    public function __construct($id, $login, $email, $firstname, $lastname)
    {
        $this->_id = $id;
        $this->_login = $login;
        $this->_email = $email;
        $this->_firstname = $firstname;
        $this->_lastname = $lastname;
    } // 
    //Crée l’utilisateur en base de données. Retourne un tableau contenant l’ensemble des informations concernant l’utilisateur créé.
    public function register($login, $password, $email, $firstname, $lastname)
    {
        // on crypte le mdp
        $password = password_hash($password, CRYPT_BLOWFISH);
        // requête pour inserer un utilisateur
        $sqlInsertUser = ("INSERT INTO utilisateurs (login, password , email , firstname , lastname) VALUES ($login,$password,$email,$firstname,$lastname)");
        $insertUser = mysqli_query(parent::bdd(), $sqlInsertUser);
        if ($insertUser == true) {
            $sqlUser = "SELECT * FROM utilisateurs WHERE login = '$this->_login'";
            $req = parent::bdd()->prepare($sqlUser);
            $infoUser = $req->mysqli_fetch_assoc();
            return $infoUser;
            // on retourne les données de l'utilisateur
        } else {
            echo "erreur";
        }
    }
    // Connecte l’utilisateur, modifie les attributs présents dans la classe et retourne un tableau contenant l’ensemble de ses informations.
    public function connect($login, $password)
    {

        $sqlLog = "SELECT * FROM utilisateurs WHERE login = '$this->_login'";
        $prepLog = mysqli_query(parent::bdd(), $sqlLog);
        $infoLog = mysqli_fetch_assoc($prepLog);
        $passVerif = password_verify($password, $infoLog["password"]);
        if ($login == $infoLog["login"] && $passVerif == $infoLog["password"]) {
            $this->_id = $infoLog["id"];
            $this->_login = $infoLog["login"];
            $this->_firstname = $infoLog["firstname"];
            $this->_lastname = $infoLog["lastname"];
            return $infoLog;
        } else {
            echo "erreur";
        }
    }
    //deco l'user
    public function disconnect()
    {
        unset($this->_id);
        unset($this->_login);
        unset($this->_email);
        unset($this->_lastname);
        unset($this->_firstname);
    }
    //supprime l'utilisateur
    public function delete()
    {
        $reqDel = "DELETE * FROM utilisateurs WHERE login = '$this->_login'";
        $prepDel = mysqli_query(parent::bdd(), $reqDel);

        unset($this->_id);
        unset($this->_login);
        unset($this->_email);
        unset($this->_lastname);
        unset($this->_firstname);
        //refresh pour tout unset
        //header(location:??.php);

    }
    // mettre à jour un user
    public function update($login, $password, $email, $firstname, $lastname)
    {
        $repUpd = "UPDATE utilisateurs SET login = $login , password = $password , email = $email , firstname = $firstname , lastname = $lastname WHERE id = '$this->_id'";

        if (parent::bdd()->query($repUpd) === TRUE) {
            
            echo "Updated successfully";
        } else {
            echo "Error updating: " . parent::bdd()->error;
        }

        parent::bdd()->close();
    }
    // retourner un bool pour savoir si l'user est co
    public function isConnected(): bool
    {
        $boolCo = false;
        if (isset($this->_id) && isset($this->_login) && isset($this->_email) && isset($this->_firstname) && isset($this->_lastname)) {
            $boolCo = true;
        } else {
            $boolCo = false;
        }
        return $boolCo;
    }
    // retourne un array contenant l'ensemble des informations de l'utilisateur
    public function getAllinfo()
    {
        if ($this->isConnected() === true) {

            $array = [

                'login' => $this->_login,
                'email' => $this->_email,
                'firstname' => $this->_firstname,
                'lastname' => $this->_lastname,
            ];
            return $array;
        } else {
            return false;
        }
    }
    // retourne le login de l'utilisateur connecté
    public function getLogin()
    {
        if ($this->isConnected() === true) {
            $login = $this->_login;
            return $login;
        } else {
            return false;
        }
    }
    //retourne l'adresse email de l'utilisateur 
    public function getEmail()
    {
        if ($this->isConnected() === true) {
            $email = $this->_email;
            return $email;
        } else {
            return false;
        }
    }
    // retourne le firstname de l'utilisateur
    public function getFirstName()
    {
        if ($this->isConnected() === true) {
            $firstname = $this->_firstname;
            return $firstname;
        } else {
            return false;
        }
    }
    // retourne la lastname de l'utilisateur
    public function getLastName()
    {
        if ($this->isConnected() === true) {
            $lastname = $this->_lastname;
            return $lastname;
        } else {
            return false;
        }
    }

}
