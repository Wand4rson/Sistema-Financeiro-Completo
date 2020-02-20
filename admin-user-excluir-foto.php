<?php  
  require 'header.php';
  require 'class/usuarios.class.php';


   //Entrou no form, recuperar os dados do usuário selecionado para alteração//
   if (isset($_POST['user_id']) || !empty($_POST['user_id'])){ 


    /* Tem Imagem na Pasta, passar nome para apagar também o Arquivo */
    if (isset($_POST['img_name']) || !empty($_POST['img_name'])){
        $img_deletar = addslashes($_POST['img_name']);
    }else{
        //Não Passou nenhuma Imagem para Remover redireciona para Lista de novo
        $img_deletar="";
        header("Location: admin-user-meus-dados.php");
        die();
    }
    /**********/


    $user = new Usuario();
    
    if ($user -> ApagarImagem($img_deletar) !=false){
        $_SESSION['msgInfo']  = "Imagem removida com sucesso. <br/>";	    
        header("Location: admin-user-meus-dados.php");
        die();
    }    
}

?>


<?php
  require 'footer.php';
?>

