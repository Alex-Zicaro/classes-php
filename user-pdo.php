<?php
session_start();

class DatabasePDO
{
    // On peut redéclarer les propriétés publics ou protégés, mais pas ceux privés
    // attribut = protected  ne peuvent pas être modfier en-dehors de la classe
    
    private static $dbName = 'classes';
    private static $dbHost = 'localhost';
    private static $dbUsername = 'root';
    private static $dbUserPassword = '';
    private static $bdd = null;

    public function __construct()
    {
        die('La fonction d\'initialisation n\'est pas autorisée');
    }
    // appel en static ::
    public static function bdd()
    { // public = accessible par d'autres fichiers
        if (self::$bdd == NULL) {
            try {
                self::$bdd = new PDO("mysql:host=" . self::$dbHost . ";" . "dbname=" . self::$dbName, self::$dbUsername, self::$dbUserPassword);
                self::$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "<h1 class='bg-danger text-center'>La connexion à échouée<h1> 
                    <h3 class='bg-warning'>Le message d'erreur : " . $e->getMessage() . "</h3>";
            }
        } // fin de if
        return self::$bdd;
    }
    public static function deco_bdd()
    {
        return self::$bdd = null;
    }
}
// accesseurs : getter (lire un attribut) getAttribute / setter (modifier un attribut) setAttribute

class UserPDO extends DatabasePDO
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
    }


    //Crée l’utilisateur en base de données. Retourne un tableau contenant l’ensemble des informations concernant l’utilisateur créé.
    public  function register($login, $password, $email, $firstname, $lastname): array
    {


        //on crypte le mdp
        $password = password_hash($password, CRYPT_BLOWFISH);
        //requête pour inserer un utilisateur
        $insertUser = parent::bdd()->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES(?,?,?,?,?)");
        // Prépare une requête à l'exécution
        $insertUser->execute(array($login, $password, $email, $firstname, $lastname)); // Exécute une requête préparée PDO
        // si la requête est bien executé
        if ($insertUser == true) {
            $sqlUser = "SELECT * FROM utilisateurs WHERE login = '$this->_login'";
            $req = parent::bdd()->prepare($sqlUser);
            $req->execute();
            $infoUser = $req->fetch();
            return $infoUser;
            // on return les données de l'utilisateur
        } else {
            echo "erreurs de saisie";
        }
    }

    // Connecte l’utilisateur, modifie les attributs présents dans la classe et retourne un tableau contenant l’ensemble de ses informations.
    public  function connect($login, $password)
    {

        $sqlLog = "SELECT * FROM utilisateurs WHERE login = '$this->_login' ";
        $prepLog = parent::bdd()->prepare($sqlLog);
        $prepLog->execute();
        $infoLog = $prepLog->fetch();
        $passVerif = password_verify($password, $infoLog["password"]);
        if ($login == $infoLog["login"] && $passVerif == $infoLog["password"]) {

            $this->_id = $infoLog["id"];
            $this->_login = $infoLog["login"];
            $this->_lastname = $infoLog["lastname"];
            $this->_firstname = $infoLog["firstname"];
            return $infoLog;
        } else {
            echo "erreur";
        }
    }
    // déco l'utilisateur 
    public function disconnect()
    {

        unset($this->_id);
        unset($this->_login);
        unset($this->_email);
        unset($this->_lastname);
        unset($this->_firstname);
        // referesh pour tout unset
        // header(location : ???.php)
    }
    //supprime l'utilisateur 
    public function delete()
    {

        $reqDel = "DELETE * FROM utilisateurs WHERE login = '$this->_login'";
        $prepDel = parent::bdd()->prepare($reqDel);
        $exe = $prepDel->execute();

        unset($this->_id);
        unset($this->_login);
        unset($this->_email);
        unset($this->_lastname);
        unset($this->_firstname);
        // referesh pour tout unset
        // header(location : ???.php);
    }
    // mettre à jour un utilisateur
    public function update($login, $password, $email, $firstname, $lastname)
    {
        $reqUpd = "UPDATE utilisateurs SET login = ? , password = ? , email = ? , firstname = ? , lastname = ? WHERE id = '$this->_id'";
        $prepare = parent::bdd()->prepare($reqUpd); // parent::bdd = connexion à la bdd depuis la classe mère 
        $exe = $prepare->execute(array($login, $password, $email, $firstname, $lastname)); // execute array = déclare les ? de la requête
        //header location pour le refresh
    }
    //retourner un bool pour savoir si l'user est co
    public function isConnected(): Bool
    {
        $boolCo = false;
        if (isset($this->_id) && isset($this->_login) && isset($this->_email) && isset($this->_firstname) && isset($this->_lastname)) {
            $boolCo = true;
        } else {
            $boolCo = false;
        }
        return $boolCo;
    }
    //Retourne un tableau contenant l’ensemble des informations de l’utilisateur.
    public function getAllInfo()
    {

        if ( $this->isConnected() === true) {

            $array = [
                'login' => $this->_login,
                'email' => $this->_email,
                'firstname' => $this->_firstname,
                'lastname' => $this->_lastname
            ];
            return $array;
        } else {
            return false;
        }
    }
    //Retourne le login de l’utilisateur connecté.

    public function getLogin()
    {
        if ($this->isConnected() === true) {
            $user = $this->_login;
            return $user;
        } else {
            return false;
        }
    }
    //Retourne l’adresse email de l’utilisateur connecté.
    public function getEmail()
    {
        if ($this->isConnected() === true) {

            $email = $this->_email;
            return $email;
        } else {
            return false;
        }
    }

    // retourne le first name de l'utilisateur
    public function getFirstName()
    {
        if ($this->isConnected() === true) {
            $firstname = $this->_firstname;
            return $firstname;
        } else {
            return false;
        }
    }
    // retourne le lastname de l'utilisateur
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
