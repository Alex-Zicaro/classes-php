<?php

    class Connect {
    public function bdd(){
    try {
        $bdd = new PDO('mysql:host=localhost;dbname=classes;charset=utf8', 'root', '');
        $bdd ->setAttribute(PDO::ATTR_ERRMODE ,PDO::ERRMODE_EXCEPTION);
        
        } 
    catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
}
}
}