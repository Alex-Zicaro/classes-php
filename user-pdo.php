<?php
session_start();
// ob_start();

class DatabasePDO
{
    // accesseurs : getter (lire un attribut) getAttribute / setter (modifier un attribut) setAttribute
    // On peut redéclarer les propriétés publics ou protégés, mais pas ceux privés
    // attribut = protected  ne peuvent pas être modfier en-dehors de la classe mere/fille

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
        } // fin du if
        return self::$bdd;
    }
    public static function deco_bdd()
    {
        return self::$bdd = null;
    }
}


class UserPDO extends DatabasePDO
{
    private $_id;
    public $_login;
    public $_email;
    public $_firstname;
    public $_lastname;



    public function __construct($login, $email , $firstname , $lastname)
    {
        
        $this->_login = $login;
        $this->_email = $email;
        $this->_firstname = $firstname;
        $this->_lastname = $lastname;
    }


    //Crée l’utilisateur en base de données. Retourne un tableau contenant l’ensemble des informations concernant l’utilisateur créé.
    public  function register($login, $Postpassword, $email, $firstname, $lastname)
    {

        $login = $_POST["login"];
        $Postpassword = $_POST["password"];
        $email = $_POST["email"];
        $firstname = $_POST["firstname"];
        $lastname = $_POST["lastname"] ;

        //on crypte le mdp https://fr.wikipedia.org/wiki/Blowfish
        $password = password_hash($Postpassword, CRYPT_BLOWFISH);
        //requête pour inserer un utilisateur
        $insertUser = parent::bdd()->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES(?,?,?,?,?)");
        // Prépare une requête à l'exécution
        $insertUser->execute(array($login, $password, $email, $firstname, $lastname)); // Exécute une requête préparée PDO
        // si la requête est bien executé
        if ($insertUser == true) {
            $sqlUser = "SELECT * FROM utilisateurs WHERE login = '$login'";
            $req = parent::bdd()->prepare($sqlUser);
            $req->execute();
            $infoUser = $req->fetch();
            return $infoUser;
            // on return les données de l'utilisateur
        } else {
            echo "erreurs de saisie";
        }
    }

    // connecte l’utilisateur, et donne aux attributs de la classe les valeurs correspondantes
    public  function connect($login, $password)
    {   
        $login = $_POST["login"] ;
        $password = $_POST["password"];
        $sqlLog = "SELECT utilisateurs.login , utilisateurs.password ,utilisateurs.email, utilisateurs.firstname, utilisateurs.lastname FROM utilisateurs WHERE login = '$login' ";
        $prepLog = parent::bdd()->prepare($sqlLog);
        
        $prepLog->execute();
        $infoLog = $prepLog->fetch(PDO::FETCH_ASSOC);
        $passVerif = password_verify($password, $infoLog["password"]);
        if ($login == $infoLog["login"] && $passVerif == $infoLog["password"]) {


            $this->_login = $infoLog["login"];
            $this->_email = $infoLog["email"];
            $this->_lastname = $infoLog["lastname"];
            $this->_firstname = $infoLog["firstname"];
            @$_SESSION["login"] = $infoLog["login"];
            @$_SESSION["firstname"] = $infoLog["firstname"];
            @$_SESSION["lastname"] = $infoLog["lastname"];
            
        } else {
            echo "erreur";
        }
    }
    // déco l'utilisateur 
    public function disconnect()
    {
        session_destroy();
        unset($this->_id);
        unset($this->_login);
        unset($this->_email);
        unset($this->_lastname);
        unset($this->_firstname);

        // referesh pour tout unset
        // header(location : ???.php)
    }
    //supprime l'utilisateur 
    public function delete($email)
    {

        $reqDel = "DELETE * FROM utilisateurs WHERE login = '$email'";
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

        if ($this->isConnected() === true) {

            $array = [
                'login' => $this->_login,
                'email' => $this->_email,
                'firstname' => $this->_firstname,
                'lastname' => $this->_lastname
            ];
            return $array;
        } else {
            $erreur = "pas connecté";
            return $erreur;
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

?>
<!-- <!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    </header>
    <div class="az">
        <main>
            <h2 id="crh2inscription" class="text-light"> Remplissez tout les champs</h2>
            <br /><br />
            <form method="POST" action="user-pdo.php">
                <table id="">
                    <tr>
                        <td class="text-light" align="right">
                            <label class="" for="login">Login : </label>
                        </td>
                        <td>
                            <input class="" type="text" placeholder="Votre login" name="login" id="login" value="
                            <//?php if (isset($login)) {
                                //echo $login;
                            //} ?>">
                        </td>
                    </tr>
                    <tr>
                        <td class="" align="right">
                            <label class="" for="password">Password : </label>
                        </td>
                        <td>
                            <input class="" type="password" placeholder="Votre password" name="password" id="password">
                        </td>
                    </tr>
                    <tr>
                        <td class="" align="right">
                            <label class="" for="password2">Confirmation du password : </label>
                        </td>
                        <td>
                            <input class="" type="password" placeholder="Confirmation password" name="password2" id="password2">
                        </td>
                    </tr>
                    <tr>
                        <td class="" align="right">
                            <label class="" for="email">Email : </label>
                        </td>
                        <td>
                            <input class="" type="email" placeholder="Votre email" name="email" id="email">
                        </td>
                    </tr>
                    <tr>
                        <td class="" align="right">
                            <label class="" for="firstname">Votre nom </label>
                        </td>
                        <td>
                            <input class="" type="text" placeholder="Votre nom" name="firstname" id="firstname">
                        </td>
                    </tr>
                    <tr>
                        <td class="" align="right">
                            <label class="" for="lastname">Votre prenom : </label>
                        </td>
                        <td>
                            <input class="" type="text" placeholder="Votre prenom" name="lastname" id="lastname">
                        </td>
                    </tr>

                </table>
                <br />
                <input id="" class="btn btn-primary" t type="submit" name="form" class="" value="Je m'inscris">

            </form>
            <br> <br> <br>
            <form method="POST" action="user-pdo.php" id="crforconnexion">
                <table>
                    <tr>
                        <td class="crtdco">
                            <label class="text-light" for="login">Login :</label>
                        </td>
                        <td class="crtdco">
                            <input type="text" name="login" placeholder="Votre Login">
                        </td>
                    </tr>
                    <tr>
                        <td class="crtdco">
                            <label class="text-light" for="password">Password :</label>
                        </td>
                        <td class="crtdco">
                            <input type="password" name="password" placeholder="Votre Password">
                        </td>
                    </tr>
                    <tr>
                        <td class="crtdco">
                            <input class="btn btn-primary" type="submit" name="formconnexion" id="crinputco" value="Se connecter !">
                        </td>
                    </tr>
                </table>
            </form>
            <form class="" action="" method="get">
                <input class="btn btn-primary " name="off" type="submit" value="Déconnexion">
            </form>




            <?php
            // déconnexion
            // if (isset($_GET['off'])) {

            //     $user = new UserPDO($login,$email,$firstname,$lastname);
            //     $user->disconnect();

            // }

            // ?>
            // <?php
            // if (isset($_POST["form"])) {
            //     $user = new UserPDO($login,$email,$firstname,$lastname);
            //     $user->register($login, $Postpassword, $email, $firstname, $lastname);
            //     header("location : user-pdo.php");
            //     exit;
            // }
            // else if(isset($_POST["formconnexion"])){
            //     $con = new UserPDO($login,$email,$firstname,$lastname);
            //     $con->connect($login,$password);
            //     header("location : user-pdo.php");
            //     exit;
            // }
            // echo (@$erreur);
            // var_dump(@$_SESSION);
            // ?>
</body>

</html> -->