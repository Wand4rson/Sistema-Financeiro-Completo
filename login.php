<?php
    require 'header-login.php';    
    require 'class/usuarios.class.php';



    /* CASO SESSÃO DE LOGIN EXISTA, SIGNIFICA QUE USUARIO LOGOU E NÃO SAIU NO BOTAO LOGOUT
    COM ISSO CASO USUARIO CLIQUE NOVAMENTE NO BOTAO FAZER LOGIN, JÁ REDIRECIONAR 
    DIRETAMENTE PARA TELA DO DASHBOARD*/
    
    if (isset($_SESSION['UserIDLogin'])){
        header("Location:dashboard.php");
        exit;
    }      
    /* ---------------------------------------------------------------------------------- */



    /* Inicia Módulo já criando a Sessão Captcha */
    if(!isset($_SESSION['captcha_number'])) {
        $numero_aleatorio = rand(1000, 9999);
        $_SESSION['captcha_number'] = $numero_aleatorio;
    }

    /* Inicia Módulo já Criando a Sessão de Qtde de Tentativas Erros */
    if(!isset($_SESSION['qtde_tentativas_login'])) {
        $_SESSION['qtde_tentativas_login'] = 0;
    }


    /* ---------------------------------------------------------------------------------- */
    /* Se tem Sessão Qtde Tentativas Criada Inicia Processo */
    if(isset($_SESSION['qtde_tentativas_login'])) {

        $codigo_captcha_informado = "";

        //Tem Três tentativas ou mais de erro, Obriga recuperar captcha Digitado//
        if ($_SESSION['qtde_tentativas_login'] >=3 )
        {

            //Recupera Captcha Digitado pelo Usuário//
            if ((isset($_POST['inputCaptcha'])) && (!empty($_POST['inputCaptcha']))) 
            {
                $codigo_captcha_informado = addslashes($_POST['inputCaptcha']);                
            }
            else
            {                
                $codigo_captcha_informado = "";
                $_SESSION['msgErros'] = "Não foi Informado o Código de Segurança. Tente Novamente";

                /* Novo Código Captcha */
                $numero_aleatorio = rand(1000, 9999);
                $_SESSION['captcha_number'] = $numero_aleatorio;
            }
        }
        else
        {            
            /*Não deu três tentativas Ainda, Código Segurança Vazio*/
            $codigo_captcha_informado = "";            
        }
    /* ---------------------------------------------------------------------------------- */
            
    
    /* ---------------------------------------------------------------------------------- */
    /* Recupera Campo Informados no Formulário */
    /* ---------------------------------------------------------------------------------- */    

    
    if (isset($_POST['inputEmail']) && (!empty($_POST['inputEmail']))){
        $email = htmlspecialchars(addslashes($_POST['inputEmail']));
    }

    if (isset($_POST['inputPassword']) && (!empty($_POST['inputPassword']))){
        $senha = $_POST['inputPassword'];
    }


    /* ---------------------------------------------------------------------------------- */
    /* Houve três contagens de erro, e não foi informado nada no InputCaptcha
    não deixa usuário continuar, sem preencher campo Código de Segurança*/
    /* ---------------------------------------------------------------------------------- */
    if ($_SESSION['qtde_tentativas_login'] >=3 )
    {        
        if ((isset($_POST['inputCaptcha'])) && (empty($_POST['inputCaptcha']))) 
        {
            $_SESSION['msgErros'] = "Não foi Informado o Código de Segurança. Tente Novamente";
            $_POST['inputPassword'] = ""; //Forço Limpar o campo senha para dar erro no teste do BD
        }
    }
    /* ---------------------------------------------------------------------------------- */




    /* ---------------------------------------------------------------------------------- */
    /* Antes de Verificar no Banco de dados, Caso tenha Dado três Tentativas de Erro, Valida 
    Captcha digitado pelo Usuario com captcha gerado aleatoriamente
    Caso Esteja OK, Continua e Conecta no BD */

    //Teve Mais de Tres tentativas de Login errado, Valida o Token Informado pelo Usuario//
    if ($_SESSION['qtde_tentativas_login'] >=3 )
    {    
        if($codigo_captcha_informado == $_SESSION['captcha_number']) {                        
 
            $codigo_captcha_informado = "";          //LIMPA VARIAVEL CAPTCHA INFORMADO PELO USUARIO            
        } else 
        {            
            //echo "Captcha Informado Erro com Captcha Aleatorio. INFORMADO ".$codigo_captcha_informado." - ALEATORIO : ". $_SESSION['captcha_number']."";

            $_SESSION['msgErros'] = "Código de segurança Inválido.";
            $_SESSION['qtde_tentativas_login'] ++; //Captcha digitado Errado Força Geração Captcha
            
            /* Novo Código Captcha */
            $numero_aleatorio = rand(1000, 9999);
            $_SESSION['captcha_number'] = $numero_aleatorio;
        }
    }
    /* ---------------------------------------------------------------------------------- */



    /* ---------------------------------------------------------------------------------- */
    //TODOS OS VALORES OBRIGATÓRIOS FORAM PREENCHIDOS E VALIDADO O CAPTCHA,
    //CONTINUA E VALIDA NO BANCO DE DADOS A SENHA E EMAIL
    /* ---------------------------------------------------------------------------------- */
            
        //Se Todos os valores obrigatorios foram preenchidos grava//
        if ((isset($_POST['inputEmail']) && (!empty(trim($_POST['inputEmail']))))
        &&  (isset($_POST['inputPassword']) && (!empty(trim($_POST['inputPassword']))))
        && (empty($codigo_captcha_informado)))
        {               
            
        $senha = addslashes($_POST['inputPassword']);
        $email = htmlspecialchars(addslashes($_POST['inputEmail']));

        $user = new Usuario();
        
            if($user -> VerificaLogin($email,$senha) !=false){	
                /*Login com SUCESSO ZERA QtdeTentativas */
                $_SESSION['qtde_tentativas_login'] = 0;                                 
                header("Location:dashboard.php");                
            }else
            {                
                /*Login com ERRO CONTA ++ QtdeTentativas */  
                $_SESSION['qtde_tentativas_login'] ++;      
                                
                /* Novo Código Captcha */
                $numero_aleatorio = rand(1000, 9999);
                $_SESSION['captcha_number'] = $numero_aleatorio;
            }
        }
        
        //-------------------------------------------------------------------

    } /* FIM do Se tem Sessão Qtde Tentativas Criada Inicia Processo */

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
        <img class="mb-4" src="img/cash-icon.png" alt="Icone Principal">    
        <p>Controle suas Despesas com Sistema Financeiro</p>
    </div>

    
    <div class="form-label-group">
        <input type="email" name="inputEmail" id="inputEmail" class="form-control" placeholder="E-mail" required autofocus>
        <label for="inputEmail">E-mail</label>
    </div>

    <div class="form-label-group">
        <input type="password" name="inputPassword" id="inputPassword" class="form-control" placeholder="Senha" required>
        <label for="inputPassword">Senha</label>
    </div>

    <?php 
        //Se Sessão Existe e Contou 3 ou Mais Tentativas Ativa Captcha Obrigatório//
        if(isset($_SESSION['qtde_tentativas_login'])  && $_SESSION['qtde_tentativas_login'] >=3 ):
    ?>     
            <div class="row">
                <div class="col-sm-6">
                    <div class="mb-4">
                        <img src="img-captcha.php" class="mb-4" width="180" height="50" alt="Captcha"/>                                                
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-label-group">
                        <input type="text" name="inputCaptcha" id="inputCaptcha" class="form-control" placeholder="Código de Segurança" >
                        <label for="inputCaptcha">Código de Segurança</label>
                    </div>
                </div>
            </div>

    <?php 
        endif 
    ;?>    

    
    <button class="btn btn-lg btn-primary btn-block" type="submit">Entrar</button>

	<?php 	
		if ($_SESSION['qtde_tentativas_login'] > 0) {
            echo "<br/>";
            echo "<div class='alert alert-danger'> Qtde Tentativas Login Erro : " .$_SESSION['qtde_tentativas_login']."</div>";				
		 }
	?>

        <p>Não Lembro minha senha <a href="login-recuperar-senha.php" class="alert-link text-danger">Recuperar Senha</a><br/>
        Ainda não sou Usuário <a href="admin-user-adicionar.php" class="alert-link text-success">Criar Conta</a></p>

    </form>

    <?php require 'footer-login.php'; ?>