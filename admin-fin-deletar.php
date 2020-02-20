<?php  
  require 'header.php';
  require 'class/financeiro.class.php';

    //Entrou no form, recuperar os dados do usuário selecionado para alteração//
    if (isset($_POST['fin_id']) || !empty($_POST['fin_id']))
    {      
        
        $fin = new Financeiro();
        $fin_id = addslashes($_POST['fin_id']);

        if($fin -> DeletarFinanceiroID($fin_id)!=false){	
            $_SESSION['msgInfo']  = "Registro removido com sucesso. <br/>";	    
            header("Location: admin-fin-lista.php");            
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