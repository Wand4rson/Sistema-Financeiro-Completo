<?php
        
    $user ='root';
    $pass ='123';
    $db='appfinanceiro';
    $servidor='localhost';
    

    try{
        $conn = new PDO("mysql:dbname=$db;host=$servidor;charset=utf8", $user, $pass,
            array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));

        //echo "Conectado";
    }catch(PDOException $e){
        echo "Erro ao conectar no banco de dados .:".$e->getMessage();
    }


?>