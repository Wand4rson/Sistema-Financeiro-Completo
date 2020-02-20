<?php  
  require 'header.php';
  require 'class/usuarios.class.php';

  $user = new Usuario();
  $lstuser = $user->getUsuarioID($_SESSION['UserIDLogin']);
?>

<!-- Begin page content -->
<main role="main" class="flex-shrink-0">
  
  <div class="container">  
   <h1>Meus Dados</h1>


  <?php    
    foreach($lstuser as $user):
  ?>

      <div class="card border-primary">  

      <?php if(!empty($user['user_imagemperfil'])): ?>
            <div class="text-center">                
                
                <br/>                  
                <?php if (file_exists("img/logo/".$user['user_imagemperfil'])) : ?>                
                  <!-- Exite Mostra -->
                  <img src="img/logo/<?php echo $user['user_imagemperfil']; ?>" class="rounded" alt="LogoTipo"  width="201" height="251" >
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


        <div class="card-body text-primary">
          <h5 class="card-title"><?php echo "Usuário E-mail : " .$user['user_email'];?></h5>
            <p class="card-text">
                <?php echo "Data de Cadastro : " .date('d/m/Y', strtotime($user['user_datacadastro']));?> 
            </p>
            <p class="card-text">
                <?php echo "Hora de Cadastro : ". date('H:i:s', strtotime($user['user_horacadastro']));?>
            </p>

            <div class="row">
            <div class="col text-center">
            <form action="admin-user-alterar.php" method="post">
              <?php //Envia via Post para Outro Arquivo o Codigo que vai ser removido, na outra tela iremos recuperar o campo codigo ?> 
                <input type="hidden" name="user_id" value="<?=$user['user_id']?>"> 
                <button type="submit" name="editar" class="btn btn-outline-primary btn-sm">Editar <i class="fas fa-user-edit"></i></button>              
						</form>
            </div>

            <div class="col text-center">
            <form action="admin-user-excluir-foto.php" method="post">
              <?php //Envia via Post para Outro Arquivo o Codigo que vai ser removido, na outra tela iremos recuperar o campo codigo ?> 
                <input type="hidden" name="user_id" value="<?=$user['user_id']?>"> 
                <input type="hidden" name="img_name" value="<?=$user['user_imagemperfil']?>"> 
                
                <?php if(!empty($user['user_imagemperfil'])): ?>
                    <button type="submit" name="editar" class="btn btn-outline-danger btn-sm">Imagem <i class="fas fa-trash"></i></button>              
                <?php else: ?>
                  <button type="submit" name="editar" class="btn btn-outline-danger btn-sm" disabled>Imagem <i class="fas fa-trash"></i></button>              
                <?php endif; ?>

						</form>
            </div>
            </div>




            
        </div>
      </div>

    <?php
      endforeach
    ?>

  </div>
</main>


<?php
  require 'footer.php';
?>