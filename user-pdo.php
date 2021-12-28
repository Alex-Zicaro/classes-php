<?php

require_once("bdd.php");
class User extends Connect{
    private $_id;
    public $_login;
    public $_email;
    public $_firstname;
    public $_lastname ;

    public function __construct($id,$login,$email,$firstname,$lastname)
    {
        $this->_id = $id;
        $this->_login = $login;
        $this->_email = $email;
        $this->_firstname = $firstname;
        $this->_lastname = $lastname;
    }


    public function register($login,$password,$email,$firstname,$lastname){
        
        $sql = 
        'INSERT INTO utilisateurs( login, password, email, firstname, lastname) 
        VALUE(
        '  . $_POST['login']  . $_POST["password"] . $_POST["email"] . $_POST["firstname"] . $_POST["lastname"] .  ') ';

        $req = connect::$bdd->prepare($sql);

        $formLogin = htmlspecialchars(strip_tags($_POST["formLogin"]));
        $formPassword = $_POST["password"];
        if(!empty($formLogin) && !empty($formPassword) && require("bdd.php")) {
            $reqBddLogin = $bdd->prepare("SELECT * FROM utilisateurs WHERE login = :login");
        }



    }

}

