<?php  
  require 'header.php';
  require 'class/usuarios.class.php';


   //Entrou no form, recuperar os dados do usuário selecionado para alteração//
   if (isset($_POST['user_id']) || !empty($_POST['user_id'])){
    
        $user = new Usuario();
        $user_id = addslashes(addslashes($_POST['user_id']));
        //$user_id = addslashes($_SESSION['UserIDLogin']);

        //Busca dados a Alterar
        $result = $user->getUsuarioID($user_id);		
    }


   //Clicou no Botão Alterar no form executa Ações //
   if (isset($_POST['btnalterar']))
   {      
                
            if (isset($_POST['inputcodigo']) && (!empty($_POST['inputcodigo']))) {
                $usuario_id_alterar = addslashes($_POST['inputcodigo']);
            }

            if (isset($_POST['inputPassword']) && (!empty($_POST['inputPassword']))){
                $usuario_senha = addslashes($_POST['inputPassword']);
            }
            
            if (isset($_FILES['inputFileImg'])){
                $usuario_foto = $_FILES['inputFileImg'];
            }

            /* Tratamento de Imagem para Apagar/Alterar */
            if (isset($_FILES['inputFileImg'])){
                $imgselecionada = $_FILES['inputFileImg'];

                //Tem Imagem Antiga para Apagar//
                if (isset($_POST['img_name_apagar']) && (!empty($_POST['img_name_apagar']))) {
                    $nome_img_antiga_apagar = addslashes($_POST['img_name_apagar']);
                }else{
                    $nome_img_antiga_apagar= "";
                }
            } else {
                $imgselecionada = array();
                $nome_img_antiga_apagar= "";
            }
            /* FIM -Tratamento de Imagem para Apagar/Alterar */

            //Se Todos os valores obrigatorios foram preenchidos grava//
            if ((isset($_POST['inputcodigo']) && (!empty($_POST['inputcodigo'])))             
            &&  (isset($_POST['inputPassword']) && (!empty(trim($_POST['inputPassword']))))) {   
            
            $user = new Usuario();
            if($user -> EditUsuario($usuario_senha, $usuario_foto, $nome_img_antiga_apagar)!=false){                
                $_SESSION['msgInfo']  = "Registro Alterado com sucesso. <br/>";	    
                header("Location:admin-user-meus-dados.php");
                die();
            }else
            {
            
                //if (isset($_SESSION['msgErros'])){
                //    echo "<div class='alert alert-danger'>".$_SESSION['msgErros']."</div>";        
                //}        

            }
        }

}//Fim do Botão Alterar

?>

<!-- Begin page content -->
<main role="main" class="flex-shrink-0">
  
  <div class="container">  
    
  <form class="form-signin" action="" method="POST" enctype="multipart/form-data">
                    
            <?php
                if (isset($_SESSION['msgErros'])){
                    echo "<div class='alert alert-danger'>".$_SESSION['msgErros']."</div>";
                    unset($_SESSION['msgErros']);               
                }    
            ?>


            <?php 
                foreach($result as $usuario):
            ?>

            <div class="text-center mb-4">
                 <!-- Mostra Image -->            
                <?php if(!empty($usuario['user_imagemperfil'])): ?>              
                    <div class="text-center">  
                    
                        <br/>        
                        <?php if (file_exists("img/logo/".$usuario['user_imagemperfil'])) : ?>                
                            <!-- Exite Mostra -->
                            <img src="img/logo/<?php echo $usuario['user_imagemperfil']; ?>" class="rounded" alt="LogoTipo"  width="201" height="251" >
                        <?php else: ?>
                            <!-- Não Exite Mostra Default -->
                            <img src="img/logo/sem_foto.png" class="rounded" alt="LogoTipo">
                        <?php endif ?>                        
                    </div>
                <?php else: ?>
                    <div class="text-center">
                        <br/>
                        <img src="img/logo/sem_foto.png" class="rounded" alt="LogoTipo">
                    </div>
                <?php endif; ?>

                <p>Alterar Cadastro de Usuário</p>
            </div>


            <input class="form-control" type="hidden" name="inputcodigo" value="<?php echo $usuario['user_id']; ?>">
            
            <div class="row">  
                    
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="inputPassword">Nova Senha</label>                
                        <input type="password" class="form-control" id="inputPassword" name="inputPassword" placeholder="Senha" required>
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="inputFileImg">Nova Logo/Foto</label>
                        <input type="file" class="form-control-file" name="inputFileImg">
                    </div>
                </div>

            </div>

             <!-- Envia como parametro o nome da Imagem Antiga, caso esteja alterando a imagem para poder apagar da pasta -->
			<input type="hidden" name="img_name_apagar" value="<?=$usuario['user_imagemperfil']?>">
			<!-- Fim Imagem -->


            <?php 
                endforeach;
            ?>
            <br/>
            <button class="btn btn-primary btn-block" name="btnalterar" type="submit">Alterar</button>
    
    </form>

            <br/>
            <a class="btn btn-link btn-sm" href="admin-user-meus-dados.php">Voltar -> Meus Dados</a>                
    
    </div>
</main>

<?php
  require 'footer.php';
?>

