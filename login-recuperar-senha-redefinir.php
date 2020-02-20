<?php
require 'header-login.php';
require 'class/usuarios.class.php';

    
    if(!empty($_GET['token_recuperacao'])) {
        
        $token = htmlspecialchars(addslashes($_GET['token_recuperacao']));

        /*Recupera o ID do Usuario a que se refere o Token*/
        $sql = "SELECT * FROM tab_usuarios_token WHERE token_hash=:token_hash AND token_usado='nao' AND token_expira_em > NOW()";
        $sql = $conn->prepare($sql);
        $sql->bindValue(":token_hash", $token);
        $sql->execute();

        if($sql->rowCount() > 0) {

            /*Recupera o ID do Usuario referente ao Token*/
            $sql = $sql->fetch();
            $user_id = $sql['usuario_id'];
            /*************************/


            if (isset($_POST['inputPassword']) && (!empty($_POST['inputPassword'])))
            {
                $senha = addslashes($_POST['inputPassword']);

                $user = new Usuario();
            

                /* Marca Token como Utilizado */
                if($user -> UpdateTokenRecuperacaoUsado($token) !=false)
                {	                                
                    /* Altera para Nova Senha somente após Marcar Token como Usado */
                    if($user -> UpdateUsuarioSenha($senha,$user_id) !=false)
                    {	                                
                        $_SESSION['msgInfo']  = "Senha alterada com sucesso. <br/>";
                        header("Location: login.php");            
                        die();	    
                    }                    
                }
            }   

        //
        
        } else {
            $_SESSION['msgErros'] = "Token Inválido ou já utilizado.<br/>"; 
            //exit;
        }
    }

        
?>

    <form class="form-signin" method="POST">
        <br/>
        <?php
            if (isset($_SESSION['msgErros'])){
                echo "<div class='alert alert-danger'>".$_SESSION['msgErros']."</div>";
                $_SESSION['msgErros'] = "";
                unset($_SESSION['msgErros']);               
            }    
        ?>

        <?php
            if (isset($_SESSION['msgInfo'])){      
                echo "<div class='alert alert-info text-center'>".$_SESSION['msgInfo']."</div>";   
                $_SESSION['msgInfo'] = "";
                unset($_SESSION['msgInfo']);         
            }
        ?>

        <div class="text-center mb-4">
            <img class="mb-4" src="img/edit-user.png" alt="Icone Principal">    

            <div class="alert alert-primary" role="alert">
                Recuperação de Senha <strong>Informe sua nova Senha</strong>
            </div>
        </div>

        <div class="form-label-group">
            <input type="password" name="inputPassword" id="inputPassword" class="form-control" placeholder="Senha" required autofocus>
            <label for="inputPassword">Nova Senha</label>
        </div>

        <button class="btn btn-lg btn-primary btn-block"  type="submit">Alterar Senha</button>

    </form>


<?php require 'footer-login.php'; ?>