<?php
require 'header-login.php';
require 'class/usuarios.class.php';

        /* Inicia Módulo já criando a Sessão Captcha */
        if(!isset($_SESSION['captcha_number'])) {
            $numero_aleatorio = rand(1000, 9999);
            $_SESSION['captcha_number'] = $numero_aleatorio;
        }
            
        if (isset($_POST['inputEmail']) && (!empty($_POST['inputEmail']))){
            $email = htmlspecialchars(addslashes($_POST['inputEmail']));
        }

        if (isset($_POST['inputPassword']) && (!empty($_POST['inputPassword']))){
            $senha = addslashes($_POST['inputPassword']);
        }


        /* ---------------------------------------------------------------------- */
        //Recupera Captcha Digitado pelo Usuário e Compara com o Gerado Aleatorio//
        /* ---------------------------------------------------------------------- */

        if ((isset($_POST['inputCaptcha'])) && (!empty($_POST['inputCaptcha']))) 
        {
            $codigo_captcha_informado = addslashes($_POST['inputCaptcha']);
            

                if($codigo_captcha_informado == $_SESSION['captcha_number']) 
                {                        
                        /* Código de Segurança Valido e todos os campos preenchidos executa metodos BD*/                        
                        if ((isset($_POST['inputEmail']) && (!empty(trim($_POST['inputEmail']))))
                        &&  (isset($_POST['inputPassword']) && (!empty(trim($_POST['inputPassword']))))) 
                        {                       
                                $senha = addslashes($_POST['inputPassword']);
                                $email = htmlspecialchars(addslashes($_POST['inputEmail']));

                                $user = new Usuario();
                                
                                if($user -> AddUsuario($email,$senha) !=false){



                                    $_SESSION['msgInfo']  = "Usuário Inserido com sucesso. <br/>";
                                    header("Location:login.php");     
                                    die();
                                }
                                else
                                {
                                    //Erro no Login
                                    //Novo Código Captcha
                                    $numero_aleatorio = rand(1000, 9999);
                                    $_SESSION['captcha_number'] = $numero_aleatorio;
                                }
                        }
                        else
                        {
                            //Faltou preencher algum Input no Form
                            $_SESSION['msgErros'] = "Campos obrigatórios não preenchidos. <br/>";                    

                            //Novo Código Captcha
                            $numero_aleatorio = rand(1000, 9999);
                            $_SESSION['captcha_number'] = $numero_aleatorio;

                        }

                //Captcha Inválido//        
                } else 
                {            
                    $_SESSION['msgErros'] = "Código de segurança Inválido. <br/>";                    
                    // Novo Código Captcha 
                    $numero_aleatorio = rand(1000, 9999);
                    $_SESSION['captcha_number'] = $numero_aleatorio;
                }            
        }
        else
        {
            //$_SESSION['msgErros'] = "Código de segurança não Informado. <br/>";                    
            // Novo Código Captcha
            $numero_aleatorio = rand(1000, 9999);
            $_SESSION['captcha_number'] = $numero_aleatorio;
        }

        /* ---------------------------------------------------------------------- */


?>

	
    

    <form class="form-signin" action="" method="POST">
	        <br/><br/>
            <?php
                if (isset($_SESSION['msgErros'])){
                    echo "<div class='alert alert-danger'>".$_SESSION['msgErros']."</div>";
                    $_SESSION['msgErros'] ="";
                    unset($_SESSION['msgErros']);               
                }    
            ?>
            
                <div class="text-center mb-4">
                    <img class="mb-4" src="img/new-user.png" alt="Icone Principal">    
                    <p>Crie sua Conta no Sistema Financeiro</p>
						<?php 
							$user = new Usuario();
							$qtde_user_registrados = $user->getCountUsuariosRegistrados();        
							//echo "<div class='container'>";
							echo "<small class='text-center text-info'>Faça como <strong>".$qtde_user_registrados." pessoas</strong> cadastre-se e utilize o Sistema Financeiro.</small>";      
							//echo "</div>";
						?>
                </div>
                
                <div class="form-group">
                    <label for="inputEmail">E-mail</label>
                    <input type="email" class="form-control" id="inputEmail" name="inputEmail" placeholder="E-mail" required autofocus>                    
                </div>

                <div class="form-group">
                    <label for="inputPassword">Senha</label>
                    <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Senha" required>
                </div>
                
                <div class="row">
                    <div class="col-sm-6">
                        <div class="mb-4">
                            <img src="img-captcha.php" class="mb-4" width="180" height="50" alt="Captcha"/>                                                
                        </div>
                    </div>

                    <div class="col-sm-6">
                        <div class="form-label-group">                            
                            <input type="text" name="inputCaptcha" id="inputCaptcha" class="form-control" placeholder="Código de Segurança" required>
							<label for="inputCaptcha">Código de Segurança</label>
                        </div>
                    </div>
                </div>

                <p>
                    <small class="text-muted">* Confirmando o cadastro você está aceitando o nosso Termo de Uso. <a href="login-termos-de-uso.php">Saiba mais</a></small>
                </p>

                <button class="btn btn-primary btn-block" type="submit">Confirmar</button>

                <br/>
                <p>Já Tenho Cadastro <a href="login.php" class="text-info">Entrar</a></p>
                
    </form>


<?php require 'footer-login.php'; ?>