<?php
require 'header-login.php';
require 'class/usuarios.class.php';

    if (isset($_POST['inputEmail']) && (!empty(trim($_POST['inputEmail']))))
    {
        $email = htmlspecialchars(addslashes($_POST['inputEmail']));

        $user = new Usuario();
    
        if($user -> AddTokenRecuperacao($email) !=false)
        {	                                
            $_SESSION['msgInfo']  = "Acesse seu E-mail e clique no link de recuperação de senha. <br/>";              
            header("Location: login.php");            
            die();	    	    
        }
        else
        {
            //$_SESSION['msgErros'] = "Não foi possível enviar email de recuperação de senha. Solicite suporte técnico.<br/>";
            //Retorno vem da Classe
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
            <p>Recuperação de Senha - Informe seu E-mail</p>
        </div>

        <div class="form-label-group">
            <input type="email" name="inputEmail" id="inputEmail" class="form-control" placeholder="E-mail" required autofocus>
            <label for="inputEmail">E-mail</label>
        </div>

        <button class="btn btn-lg btn-primary btn-block" name="btnrecuperar" type="submit">Enviar</button>

        <p>Já Tenho Cadastro <a href="login.php" class="text-info">Entrar</a></p>    
        
        <p>Ainda não sou Usuário <a href="admin-user-adicionar.php" class="alert-link text-success">Criar Conta</a></p>

    </form>

    <?php require 'footer-login.php'; ?>