<?php

    session_start(); //sem esta linha ele não destroia a sessao
    unset($_SESSION['msgErros']); 
    unset($_SESSION['UserIDLogin']); 
    unset($_SESSION['UserEmailLogin']);     
    header("location: login.php");
    die();
?>