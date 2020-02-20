<?php

/* ----- DECLARAÇÃO USES CLASSE PHPMAILER ----- */
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
/* --- FIM DECLARAÇÃO USES CLASSE PHPMAILER --- */

    class Usuario{


        public function AddUsuario($email, $senha){

            global $conn;


            if(strlen($senha) < 5){
                $_SESSION['msgErros']  = "Senha deve possuir no mínimo 5 caracteres.<br/>";
                return false;
                exit;
            }

            /* Verifica se email já esta cadastrado */
            $sql = "SELECT * FROM tab_usuarios WHERE user_email=:user_email";
            $sql = $conn->prepare($sql);
            $sql->bindValue("user_email",$email);
            $sql->execute();

            if($sql->rowCount() > 0 ){                
                $_SESSION['msgErros']  = "Usuário já existe em nossa base de dados.<br/>";
                return false;
                exit;
            }

            $sql = "INSERT INTO tab_usuarios(              
                    user_email,
                    user_senha,
                    user_datacadastro,
                    user_horacadastro,
                    ip_lancamento)
                VALUES (                
                    :user_email,
                    :user_senha,
                    :user_datacadastro,
                    :user_horacadastro,
                    :ip_lancamento)";


            try{
                $sql = $conn->prepare($sql);
                $sql->bindValue("user_email", $email);
                $sql->bindValue("user_senha",MD5($senha));            
                $sql->bindValue("user_datacadastro", date('Y-m-d'));
                $sql->bindValue("user_horacadastro", date('H:i:s', time()));
                $sql->bindValue("ip_lancamento", $_SERVER['REMOTE_ADDR']);                                
                $sql->execute();
             
                /*-------------------------------------------------------------------------*/
                /* Recupera o ID do Usuário Adicionado e Cria duas Categorias de Lançamentos
                Uma Despesa e Outra Receita*/                
                $lastId = $conn->lastInsertId();
                
                require 'categorias.class.php';
                $cat = new Categorias();
                $cat->AddCategoriaDefault($lastId,'Receitas Diversas','R');
                $cat->AddCategoriaDefault($lastId,'Despesas Diversas','D');

                /*-------------------------------------------------------------------------*/

                return true;

            }catch(PDOException $e){
                $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                return false;
            }

        }

        public function EditUsuario($senha, $imgselecionada, $img_antiga_apagar){

            global $conn;

                if(strlen($senha) < 5){
                    $_SESSION['msgErros']  = "Nova Senha deve possuir no mínimo 5 caracteres.<br/>";
                    return false;
                    exit;
                }
                
                /* Verifica se enviaram imagem */                    
                if (!empty($imgselecionada['name'])){  
                    if(isset($imgselecionada['tmp_name']) && (!empty($imgselecionada['tmp_name']))){

                        // 1º - Pega o tipo da imagem selecionada e só insere caso seja os tipos definidos
                        $tipo_imagem_selecionada = $imgselecionada['type'];	

                        if(in_array($tipo_imagem_selecionada, array('image/jpeg', 'image/png', 'image/jpg'))) {

                                switch ($tipo_imagem_selecionada){
                                    case "image/jpeg":
                                            $tipo_imagem_salvar = ".jpeg";
                                            break;
                                    case "image/jpg":
                                            $tipo_imagem_salvar=".jpg";
                                            break;
                                    case "image/png":
                                            $tipo_imagem_salvar=".png";
                                            break;
                                }
                                
                        }else{
                            $_SESSION['msgErros']  = "Tipo de Imagem Inválido.<br/>";      
                            return false;                      
                            exit;
                        }

                        //2º - Permite Gravar
                        $nome_arquivo_imagem = md5(time().rand(0,999)) .$tipo_imagem_salvar;
                        
                    //Conseguiu Atualizar a foto de perfil, apaga a antiga//
                    if(move_uploaded_file($imgselecionada['tmp_name'],'img/logo/'.$nome_arquivo_imagem))
                    {
                        //Apaga a antiga Imagem da Pasta//                            
                        if (!empty($img_antiga_apagar)){
                            $path_imagem = "img/logo/$img_antiga_apagar";
                            unlink($path_imagem);
                        }                                
                    }
                }//fim teste tmp_name
            }//Fim da verificação se teve imagem selecionada


        /* Verifica se enviaram alguma imagem */
        if (!empty($imgselecionada['name'])){  
            if (!empty($senha)) {
                $sql = $conn->prepare("UPDATE tab_usuarios SET user_senha=:user_senha, user_imagemperfil=:user_imagemperfil WHERE user_id=:user_id");                
            }else{                
                $sql = $conn->prepare("UPDATE tab_usuarios SET user_imagemperfil=:user_imagemperfil WHERE user_id=:user_id");
            }
        }else
        {
            //Não enviaram imagem, update normal da senha
             if (!empty($senha)) {
                $sql = $conn->prepare("UPDATE tab_usuarios SET user_senha=:user_senha WHERE user_id=:user_id");                                             
             }
        }

        //Enviaram Senha Cria  BindSenha
        if (!empty($senha)) {
            $sql->bindValue("user_senha",MD5($senha)); 
        }

         /* Verifica se enviaram alguma imagem */
         if (!empty($imgselecionada['name'])){            
             //nome da imagem a salvar no db
            $sql->bindValue(":user_imagemperfil",$nome_arquivo_imagem); 
        }

        $sql->bindValue("user_id", $_SESSION['UserIDLogin']); 

        try{
            return $sql->execute();
        }catch(PDOException $e){
            $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
            return false;
        }
 
  }

  
        //Deletar imagem do usuario, somente dar um update vazio na foto
        public function ApagarImagem($nome_imagem_apagar){
            
            global $conn;
            $sql = $conn->prepare("UPDATE tab_usuarios SET user_imagemperfil='' WHERE user_id=:user_id");        
            $sql->bindValue(":user_id", $_SESSION['UserIDLogin']);
            $sql->execute();

            if ($sql->rowCount() > 0){   
                
                //Apaga a Imagem da Pasta//
                $path_imagem = "img/logo/$nome_imagem_apagar";
                unlink($path_imagem);
                /*******************/

                return true;            
            }else{            
                return false;
            } 
        }


        public function getUsuarioID($user_id){
            global $conn;

            $result = array();

            $sql="SELECT * FROM tab_usuarios WHERE user_id=:user_id";
            $sql = $conn->prepare($sql);
            $sql->bindValue("user_id",$user_id);
            $sql->execute();

            if ($sql->rowCount() > 0 ){              
                $result = $sql->fetchAll(PDO::FETCH_ASSOC); 
            }

            return $result;

        }


        
        /*Qtde de Usuarios Registrados no Site */
        public function getCountUsuariosRegistrados(){
            global $conn;

            $sql ="SELECT COUNT(*) as qtde FROM tab_usuarios WHERE user_ativo='sim'";
            $sql = $conn->prepare($sql);
            $sql->execute();

            $qtde_lancamentos = $sql->fetch();
            return $qtde_lancamentos['qtde'];
        }


        /* Quando Solicita a recuperação de Senha, Alimenta esta Tabela com o Token 
        e quando o usuário acessar o link enviado, ele abre o formulario para alterar a senha
        atual */

        public function AddTokenRecuperacao($email){

            global $conn;

            if(empty($email)){
                $_SESSION['msgErros']  = "E-mail para recuperação não informado.<br/>";
                return false;
                exit;
            }

            /* Verifica se email já esta cadastrado */
            $sql = "SELECT * FROM tab_usuarios WHERE user_email=:user_email";
            $sql = $conn->prepare($sql);
            $sql->bindValue("user_email",$email);
            $sql->execute();

            if($sql->rowCount() > 0 )
            {     

                /* Recupera o ID do Usuário Vinculado ao E-mail Informado */
                $sql = $sql->fetch();
                $id_usuario = $sql['user_id'];

                /* Gera um Token */
                $token_hash = md5(uniqid());
                /*--------------*/

                /*Insere na Tabela Token*/
                $sql="INSERT INTO tab_usuarios_token(             
                    usuario_id,
                    token_hash,             
                    token_expira_em,
                    ip_lancamento)
                VALUES (
                    :usuario_id,
                    :token_hash,             
                    :token_expira_em,
                    :ip_lancamento)";
    
    
                try{
                    $sql = $conn->prepare($sql);
                    $sql->bindValue("usuario_id", $id_usuario);
                    $sql->bindValue("token_hash", $token_hash);            
                    $sql->bindValue("token_expira_em", date('Y-m-d',strtotime("+2 days")));                    
                    $sql->bindValue("ip_lancamento", $_SERVER['REMOTE_ADDR']);    
                    $sql->execute();
                 

                    
                    /* -------------- MONTA MENSAGEM DE RECUPERAÇÃO DE SENHA --------------*/

                    $link_recuperacao = "";
                    $mensagem="";
                    $assunto="";

                    $assunto="Sistema Financeiro - Solicitação Redefinição de Senha";
                    //Talvez seja necessário mudar as URLs de reconfiguração de senha para as do seu site
                    $link_recuperacao = "https://www.seusite.com.br/painel/login-recuperar-senha-redefinir.php?token_recuperacao=".$token_hash;
                    $mensagem = "Clique no Link de Recuperação ou copie e cole no seu navegador para redefinir sua senha : ".$link_recuperacao;                    
                    
                    /* -------------- Envia Email PHPMailer --------------*/                    
                  
                    require 'PHPMailer/Exception.php';
                    require 'PHPMailer/PHPMailer.php';
                    require 'PHPMailer/SMTP.php';
                        
                    /*********************************** ROTINA DE ENVIO EMAIL ************************************/ 
                    // Inicia a classe PHPMailer
                    $mail = new PHPMailer();
                    
                    // DEFINIÇÃO DOS DADOS DE AUTENTICAÇÃO - Você deve auterar conforme o seu domínio!
                    $mail->IsSMTP(); // Define que a mensagem será SMTP
                    $mail->Host = "seusite.com.br"; // Seu endereço de host SMTP
                    $mail->SMTPAuth = true; // Define que será utilizada a autenticação -  Mantenha o valor "true"
                    $mail->Port = 587; // Porta de comunicação SMTP - Mantenha o valor "587"
                    $mail->SMTPSecure = false; // Define se é utilizado SSL/TLS - Mantenha o valor "false"
                    $mail->SMTPAutoTLS = false; // Define se, por padrão, será utilizado TLS - Mantenha o valor "false"
                    $mail->Username = 'suporte@seusite.com.br'; // Conta de email existente e ativa em seu domínio
                    $mail->Password = 'suasenha.'; // Senha da sua conta de email
                    
                    // DADOS DO REMETENTE
                    $mail->Sender = "suporte@seusite.com.br"; // Conta de email existente e ativa em seu domínio
                    $mail->From = "suporte@seusite.com.br";   // Sua conta de email que será remetente da mensagem
                    $mail->FromName = "Redefinir Senha";             // Nome da conta de email
                    
                    // DADOS DO DESTINATÁRIO                    
                    $mail->AddAddress($email, ''); // Define qual conta de email receberá a mensagem
                    
                    // Definição de HTML/codificação
                    $mail->IsHTML(true);      // Define que o e-mail será enviado como HTML
                    $mail->CharSet = 'utf-8'; // Charset da mensagem (opcional)
                    
                    // DEFINIÇÃO DA MENSAGEM OU CONCATENAR MENSAGEM
                    $mail->Subject  = $assunto;     // Assunto da mensagem
                    $mail->Body .= nl2br($mensagem); // Texto da mensagem
                    
                                       // ENVIO DO EMAIL
                    $enviado = $mail->Send();

                    // Limpa os destinatários e os anexos
                    $mail->ClearAllRecipients();
                    
                    // Exibe uma mensagem de resultado do envio (sucesso/erro)
                    if ($enviado) 
                    {                           
                        $mensagemRetorno = "E-mail enviado com sucesso!";
                        return true;
                    } 
                    else 
                    {                            
                        $mensagemRetorno = "Não foi possível enviar o e-mail. Detalhes do Erro. Informações :  <br/> ".$mail->ErrorInfo;
                        $_SESSION['msgErros'] = $mensagemRetorno;
                        return false;
                        exit;
                    }
                    
                    /* ----------- FIM do Envio de email PHPMailer ----------- */
                    
                }catch(PDOException $e){
                    $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                    return false;
                }
            }
            else
            {
                $_SESSION['msgErros']  = "E-mail informado não existe em nossa base de dados.<br/>";
                return false;
                exit;
            }
            
        }

        /*Marca Token de Recuperação como Usado */
        public function UpdateTokenRecuperacaoUsado($token_hash){

            global $conn;

            if(empty($token_hash)){
                $_SESSION['msgErros']  = "Token de recuperação não informado para marcação de Usado.<br/>";
                return false;
                exit;
            }

            $sql="UPDATE tab_usuarios_token SET token_usado='sim' WHERE token_hash=:token_hash";                    
            
            try{
                $sql = $conn->prepare($sql);                    
                $sql->bindValue("token_hash", $token_hash);                                                    
                return $sql->execute();
            }catch(PDOException $e){
                $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                return false;
            }            
        }

        /*Alterar Somente a Senha do Usuario a partir do ID, metodo auxiliar a recuperação de senha*/
        public function UpdateUsuarioSenha($senha, $user_id){

            global $conn;

            if(empty($user_id)){
                $_SESSION['msgErros']  = "Usuário para alteração de Senha não informado.<br/>";
                return false;
                exit;
            }

            if(strlen($senha) < 5){
                $_SESSION['msgErros']  = "Senha deve possuir no mínimo 5 caracteres.<br/>";
                return false;
                exit;
            }
            
            if(empty($senha)){
                $_SESSION['msgErros']  = "Nova Senha não informado.<br/>";
                return false;
                exit;
            }

            $sql = $conn->prepare("UPDATE tab_usuarios SET user_senha=:user_senha WHERE user_id=:user_id");

        try{            
            
            $sql->bindValue("user_senha",MD5($senha)); 
            $sql->bindValue("user_id", $user_id); 
            return $sql->execute();

        }catch(PDOException $e){
            $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
            return false;
        }
  }



        public function VerificaLogin($email, $senha){
            
            global $conn;

            if(empty($email)){
                $_SESSION['msgErros']  = "E-mail não informado.<br/>";
                return false;
                exit;
            }

            if(empty($senha)){
                $_SESSION['msgErros']  = "Senha não informado.<br/>";
                return false;
                exit;
            }


            /* Verifica se email Existe */
            $sql = "SELECT * FROM tab_usuarios WHERE user_email=:user_email";
            $sql = $conn->prepare($sql);
            $sql->bindValue("user_email",$email);
            $sql->execute();

            if($sql->rowCount() == 0 ){                
                $_SESSION['msgErros']  = "Usuário não existe em nossa base de dados.<br/>";
                return false;
                exit;
            }

            
            /* Verifica se Usuário esta Ativo */
            $sql = "SELECT * FROM tab_usuarios WHERE user_email=:user_email AND user_ativo='nao'";
            $sql = $conn->prepare($sql);
            $sql->bindValue("user_email",$email);
            $sql->execute();

            if($sql->rowCount() > 0 ){                
                $_SESSION['msgErros']  = "Usuário Inativo em nossa base de dados. Verifique com Equipe de Suporte.<br/>";
                return false;
                exit;
            }

            /* Passou pela Verificação, Verifica senha */

            $sql = "SELECT * FROM tab_usuarios WHERE user_email=:user_email AND user_senha=:user_senha";
            $sql = $conn->prepare($sql);
            $sql->bindValue("user_email",$email);
            $sql->bindValue("user_senha",MD5($senha));
            $sql->execute();

            if ($sql->rowCount() > 0 ){
                $sql = $sql->fetch(); //Ao Local de fetchAll que precisa de array
                
                $_SESSION['UserIDLogin'] = $sql['user_id'];
                $_SESSION['UserEmailLogin'] = $sql['user_email'];



                    /* Grava Data/Hora do Ultimo Acesso */
                    $sql = "UPDATE tab_usuarios SET user_ultimoacesso=:user_ultimoacesso WHERE user_id=:user_id";
                    $sql = $conn->prepare($sql);
                    $sql->bindValue("user_id", $_SESSION['UserIDLogin']);
                    $sql->bindValue("user_ultimoacesso", date('Y-m-d H:i:s'));
                    $sql->execute();
                    /*-----------------------------------*/
                
                return true;                
            }else{
                $_SESSION['msgErros']  = "Usuário ou Senha Inválido.<br/>";
                return false;                
                exit;                
            }

        }





    }

?>