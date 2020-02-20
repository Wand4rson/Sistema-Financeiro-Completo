<?php

session_start(); //Sem esta linha não gera o token
header("Content-type: image/jpeg");

$n = $_SESSION['captcha_number'];

$imagem = imagecreate(100, 50);
imagecolorallocate($imagem, 200, 200, 200);

$fontcolor = imagecolorallocate($imagem, 20, 20, 20);


//Para funcionar Local Tire o Comentario Local
imagettftext($imagem, 40, 0, 21, 35, $fontcolor,'C:\xampp\htdocs\proj.financeiro\fonts\Ginga.otf', $n);

//Para funcionar na WEB tire o comentário abaixo
//imagettftext($imagem, 40, 0, 21, 35, $fontcolor,'fonts/Ginga.otf', $n);	

imagejpeg($imagem, null, 100);
?>
