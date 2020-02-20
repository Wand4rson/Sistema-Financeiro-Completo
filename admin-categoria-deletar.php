<?php  
  require 'header.php';
  require 'class/categorias.class.php';


    //Entrou no form, recuperar os dados do usuário selecionado para alteração//
    if (isset($_POST['cat_id']) || !empty($_POST['cat_id']))
    {      
        
        $cat = new Categorias();        
        $cat_id = addslashes($_POST['cat_id']);

        if($cat -> RemoveCategoriaID($cat_id)!=false){	
            $_SESSION['msgInfo']  = "Registro removido com sucesso. <br/>";	    
            header("Location: admin-categoria-lista.php");            
            die();
        }else
        {
        /* if (isset($_SESSION['msgErros'])){
                echo "<div class='alert alert-danger'>".$_SESSION['msgErros']."</div>";        
            }
        */    
        }
    }

?>

<?php
  require 'footer.php';
?>