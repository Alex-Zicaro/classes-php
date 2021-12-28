<?php

// accesseurs : getter (lire un attribut) getAttribute / setter (modifier un attribut) setAttribute

class User 
{
    private $_id;
    public $_login;
    public $_email;
    public $_firstname;
    public $_lastname;
    protected $dns = "mysql:host=localhost;";
    protected $dbName = "dbname=classes;";
    protected $dbCo = "charset=utf8', 'root', ''";
    protected $bdd = new PDO($dns,$dbName,$dbCo);
    public function __construct($id, $login, $email, $firstname, $lastname,$dns,$dbName,$dbCo)
    {
        $this->_id = $id;
        $this->_login = $login;
        $this->_email = $email;
        $this->_firstname = $firstname;
        $this->_lastname = $lastname;
        $this->dns = $dns;
        $this->dbName = $dbName;
        $this->dbCo = $dbCo;
        $bdd = new PDO($dns,$dbName,$dbCo);
    }


    public  function register($login, $password, $email, $firstname, $lastname)
    {

        $sql =
            'INSERT INTO utilisateurs( login, password, email, firstname, lastname) 
        VALUE(
        '  . $_POST['login']  . $_POST["password"] . $_POST["email"] . $_POST["firstname"] . $_POST["lastname"] .  ') ';

        $req = $this->bdd->prepare($sql);

        $formLogin = htmlspecialchars(strip_tags($_POST["formLogin"]));
        $formPassword = $_POST["password"];
        if (!empty($formLogin) && !empty($formPassword)) {
            $reqBddLogin = $this->bdd->prepare("SELECT * FROM utilisateurs WHERE login = :login");
        }
    }
}

// $test = new User();
