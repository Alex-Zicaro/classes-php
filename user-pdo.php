<?php
session_start();

class Database
{
// On peut redéclarer les propriétés publics ou protégés, mais pas ceux privés
    // attribut = protected et protected ne peuvent pas être modfier en-dehors de la classe


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
    }


    //Crée l’utilisateur en base de données. Retourne un tableau contenant l’ensemble des informations concernant l’utilisateur créé.
    public  function register($login, $password, $email, $firstname, $lastname): array
    {


        //on crypte le mdp
        $password = password_hash($password, CRYPT_BLOWFISH);
        //requête pour inserer un utilisateur
        $insertmbr = parent::bdd()->prepare("INSERT INTO utilisateurs (login, password, email, firstname, lastname) VALUES(?,?,?,?,?)");
        // Prépare une requête à l'exécution
        $insertmbr->execute(array($login, $password, $email, $firstname, $lastname)); // Exécute une requête préparée PDO
        // si la requête est bien executé
        if ($insertmbr = true) {
            $sqlUser = "SELECT * FROM utilisateurs WHERE login = '$login'";
            $req = parent::bdd()->prepare($sqlUser);
            return $infoUser = $req->fetch();
            // on return les données de l'utilisateur
        } else {
            echo "erreurs de saisie";
        }
    }

    // Connecte l’utilisateur, modifie les attributs présents dans la classe et retourne un tableau contenant l’ensemble de ses informations.
    public  function connect($login, $password): string
    {

        $sqlLog = "SELECT * FROM utilisateurs WHERE login = '$login' ";
        $prepLog = parent::bdd()->prepare($sqlLog);
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
        // header(location : ???.php)
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
    public function isConnected() :Bool {
        if(isset($this->_id) && isset($this->_login) && isset($this->_email) && isset($this->_firstname) && isset($this->_lastname)){
            $boolCo = true;
        } else {
            $boolCo = false;
        }
        return $boolCo;
    }
    //Retourne un tableau contenant l’ensemble des informations de l’utilisateur.
    public function getAllInfo(){
        
        $array = [
            'login' => $this->_login,
            'email' => $this->_email,
            'firstname' => $this->_firstname,
            'lastname' => $this->_lastname
        ];
        return $array;

    }
    //pas finis
}
?>

<!DOCTYPE html>
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
            <form method="POST" action="">
                <table id="">
                    <tr>
                        <td class="text-light" align="right">
                            <label class="" for="login">Login : </label>
                        </td>
                        <td>
                            <input class="" type="text" placeholder="Votre login" name="login" id="login" value="
                            <?php if (isset($login)) {
                                echo $login;
                            } ?>">
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
            <?php echo (@$erreur) ?>
</body>

</html>