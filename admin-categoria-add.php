<?php  
  require 'header.php';
  require 'class/categorias.class.php';

    if (isset($_POST['inputdescricao']) && (!empty($_POST['inputdescricao']))){
        $cat_descricao = htmlspecialchars(addslashes($_POST['inputdescricao']));
    }

    if (isset($_POST['inputtipolancamento']) && (!empty($_POST['inputtipolancamento']))){
        $cat_tipolancamento = htmlspecialchars(addslashes($_POST['inputtipolancamento']));
    }

    if (isset($_POST['inputstatus']) && (!empty($_POST['inputstatus']))){
        $cat_status = htmlspecialchars(addslashes($_POST['inputstatus']));
    }

    //Se Todos os valores obrigatorios foram preenchidos grava//
    if ((isset($_POST['inputdescricao']) && (!empty(trim($_POST['inputdescricao']))))
    &&  (isset($_POST['inputtipolancamento']) && (!empty($_POST['inputtipolancamento'])))) {   


    $cat = new Categorias();
    
    if($cat -> AddCategoria($cat_descricao, $cat_status, $cat_tipolancamento)!=false){	
        $_SESSION['msgInfo']  = "Registro Inserido com sucesso. <br/>";        	    
        header("Location:admin-categoria-lista.php");
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

<!-- Begin page content -->
<main role="main" class="flex-shrink-0">
  
  <div class="container">  
    <!-- <h1>Cadastro de Categorias</h1> -->

            <form class="form-signin" action="" method="POST">
            
                    <?php
                        if (isset($_SESSION['msgErros'])){
                            echo "<div class='alert alert-danger'>".$_SESSION['msgErros']."</div>";
                            unset($_SESSION['msgErros']);               
                        }    
                    ?>

                        <div class="text-center mb-4">
                            <img class="mb-4" src="img/categories-list.png" alt="Icone Principal">    
                            <h3>Cadastro de Categorias</h3>
                        </div>
                        
                        <div class="form-group">
                            <label for="inputdescricao">Descrição *</label>
                            <input type="text" class="form-control" id="inputdescricao" name="inputdescricao" placeholder="Descrição" required>                    
                        </div>

                        <div class="row">  
                        
                                <div class="col-sm-8">
                                    <div class="form-group">
                                    <label for="inputtipolancamento">Tipo Lançamento *</label>
                                        <select name="inputtipolancamento" class="form-control" required>
                                            <option value="" selected>Escolha...</option>
                                            <option value="D">Despesa</option>
                                            <option value="R">Receita</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-sm-4">
                                    <div class="form-group">
                                    <label for="inputstatus">Ativo *</label>
                                        <select name="inputstatus" class="form-control" required>
                                            <option value="" selected>Escolha...</option>
                                            <option value="sim">Sim</option>
                                            <option value="nao">Não</option>
                                        </select>
                                    </div>
                                </div>
                                
                        </div>
                        
                        <br/>
                        <button class="btn btn-primary btn-block" type="submit">Confirmar</button>
            </form>
            <br/>
            <a class="btn btn-link btn-sm" href="admin-categoria-lista.php">Voltar -> Lista de Categorias</a>                
    


    </div>
</main>

<?php
  require 'footer.php';
?>

