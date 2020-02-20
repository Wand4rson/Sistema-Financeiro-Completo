<?php  
  require 'header.php';
  require 'class/financeiro.class.php';

    //Entrou no form, recuperar os dados do usuário selecionado para alteração//
    if (isset($_POST['fin_id']) || !empty($_POST['fin_id']))
    {      
        
        $fin = new Financeiro();
        $fin_id = addslashes($_POST['fin_id']);
        $valor_pagamento = addslashes($_POST['fin_valor_pagamento']); //Valor Pago Informado pelo Usuário

        if($fin -> setLancamentoPago($fin_id, $valor_pagamento)!=false){	
            $_SESSION['msgInfo']  = "Baixa realizada com sucesso. <br/>";	    
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