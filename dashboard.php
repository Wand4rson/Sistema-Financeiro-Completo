<?php  
  require 'header.php';
  require 'class/financeiro.class.php';
  require 'class/usuarios.class.php';
  
  $fin = new Financeiro();

  /* Retorno por Tipo ex: Despesas/Receitas */


  $data_base_vencimento = date('Y-m-d'); //Data Atual
  $lstFinAVencer = $fin->getListaFinanceiroEstatistica($data_base_vencimento, 'Vencer');
  $lstFinVencido = $fin->getListaFinanceiroEstatistica($data_base_vencimento, 'Vencido');
  

  $user = new Usuario();
  $lstUsuario = $user-> getUsuarioID($_SESSION['UserIDLogin']);
  

?>


<!-- Begin page content -->
<main role="main" class="flex-shrink-0">
  
  <div class="container">
  
    <h1>Estatísticas</h1>
   
      <?php foreach($lstUsuario as $usuario) : ?>            
            <br/><small class="text-muted">E-mail : <?php echo $usuario['user_email'] ;?></small>
            <br/><small class="text-muted">Ultimo Acesso : <?php echo date('d/m/Y H:i:s', strtotime($usuario['user_ultimoacesso'])) ;?></small>
            <br/>
            <br/>
      <?php endforeach; ?>

        <!-- Totais Gerais Por Tipo  A Vencer --> 
        <?php if(!empty($lstFinAVencer)) : ?>      
            <?php foreach($lstFinAVencer as $vencer) : ?>            
                <?php  if($vencer['categoria_tipo'] == 'D') :  ;?> 

                     <div class="alert alert-danger" role="alert">
                        Total Despesas a vencer R$ :  <?php echo number_format($vencer['TotalSumValorParcelas'],2,',','.') ;?> 
                     </div>

                <?php  else: ;?> 

                     <div class="alert alert-primary" role="alert">
                           Total Receitas a vencer R$ :  <?php echo number_format($vencer['TotalSumValorParcelas'],2,',','.') ;?>
                     </div> 

                <?php  endif;?>             
            <?php endforeach; ?>       
        <?php endif; ?> <!-- fim lstFinAVencer -->


               <!-- Totais Gerais Por Tipo  Vencido  --> 
               <?php if(!empty($lstFinVencido)) : ?>      
                     <?php foreach($lstFinVencido as $vencido) : ?>            
                        <?php  if($vencido['categoria_tipo'] == 'D') :  ;?> 

                              <div class="alert alert-danger" role="alert">
                                 Total Despesas Vencidas R$ :  <?php echo number_format($vencido['TotalSumValorParcelas'],2,',','.') ;?> 
                                 
                              </div>

                        <?php  else: ;?> 

                              <div class="alert alert-primary" role="alert">
                                    Total Receitas Vencidas  . R$ :  <?php echo number_format($vencido['TotalSumValorParcelas'],2,',','.') ;?>
                              </div> 

                        <?php  endif;?>             
                     <?php endforeach; ?>       
               <?php endif; ?> <!-- fim lstFinAVencer -->
        
      
        <?php if(!empty($lstfin)){ ?>
          <br/><small class="text-muted">Qtde Registros encontrados : <?php echo count($lstfin) ;?></small><br/><br/>       
        <?php } ?>



  </div>
</main>


<?php
  require 'footer.php';
?>