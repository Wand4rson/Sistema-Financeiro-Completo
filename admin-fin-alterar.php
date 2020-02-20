<?php  
  require 'header.php';
  require 'class/financeiro.class.php';


     /* Entrou no form, recuperar os dados do documento selecionado 
     e preencher os campos para alterar */     
     if (isset($_POST['fin_id']) || (!empty($_POST['fin_id']))){

      $fin = new Financeiro();
      $fin_id = addslashes($_POST['fin_id']);
  
      //Busca dados a Alterar
      $result = $fin->getListaFinanceiroID($fin_id);	

     }

   //Clicou no Botão Alterar no form executa Ações //
   if (isset($_POST['btnalterar']))
   {   


    

      if (isset($_POST['inputcodigoalterar']) && (!empty($_POST['inputcodigoalterar']))){
        $fin_id_alterar = htmlspecialchars(addslashes($_POST['inputcodigoalterar']));
      }

      if (isset($_POST['anun_descricao']) && (!empty($_POST['anun_descricao']))){
        $anun_descricao = htmlspecialchars(addslashes($_POST['anun_descricao']));
      }

      if (isset($_POST['categoria_codigo']) && (!empty($_POST['categoria_codigo']))){
        $categoria_codigo = htmlspecialchars(addslashes($_POST['categoria_codigo']));
      }

      if (isset($_POST['anun_nrodocumento']) && (!empty($_POST['anun_nrodocumento']))){
        $anun_nrodocumento = htmlspecialchars(addslashes($_POST['anun_nrodocumento']));
      }

      if (isset($_POST['anun_parcela']) && (!empty($_POST['anun_parcela']))){
        $anun_parcela = htmlspecialchars(addslashes($_POST['anun_parcela']));
      }

      if (isset($_POST['anun_valorparcela']) && (!empty($_POST['anun_valorparcela']))){
        $anun_valorparcela = htmlspecialchars(addslashes($_POST['anun_valorparcela']));
        
        //Converte Antes de Gravar no Banco
        $anun_valorparcela = str_replace(',','.', str_replace('.','', $anun_valorparcela));
      }

      if (isset($_POST['anun_datavencimento']) && (!empty($_POST['anun_datavencimento']))){
        $anun_datavencimento = htmlspecialchars(addslashes($_POST['anun_datavencimento']));
      }

      //Se Todos os valores obrigatorios foram preenchidos grava//
      if ((isset($_POST['anun_descricao']) && (!empty(trim($_POST['anun_descricao']))))  &&  
        (isset($_POST['anun_nrodocumento']) && (!empty(trim($_POST['anun_nrodocumento'])))) &&
        (isset($_POST['anun_parcela']) && (!empty($_POST['anun_parcela']))) &&
        (isset($_POST['anun_valorparcela']) && (!empty($_POST['anun_valorparcela']))) &&
        (isset($_POST['anun_datavencimento']) && (!empty($_POST['anun_datavencimento'])))) {   

            $fin = new Financeiro();

            /*-----------------------------------------------------------------------*/
            /*Formata data digitada para formato gravar banco de dados*/
            $dataexplode = explode("/",$anun_datavencimento);
            $data_vencimento_salvar_db = $dataexplode[2]."-".$dataexplode[1]."-".$dataexplode[0];
            /*-----------------------------------------------------------------------*/
                        
            if($fin -> AlterarFinanceiro(
                      $fin_id_alterar,                      
                      $anun_nrodocumento,
                      $anun_descricao, 
                      $anun_parcela, 
                      $anun_valorparcela, 
                      $anun_valorparcela, 
                      $data_vencimento_salvar_db, 
                      $categoria_codigo)!=false){	

                $_SESSION['msgInfo']  = "Registro Alterado com sucesso. <br/>";	    
                header("Location:admin-fin-lista.php");                        
                die();

            }else
            {
             
              $_SESSION['msgErros'] = "Campos Obrigatórios não preenchidos !";
             if (isset($_SESSION['msgErros'])){
                  echo "<div class='alert alert-danger'>".$_SESSION['msgErros']."</div>";        
              }
            
            }
      } //fim clicou botao alterar
    
    }

?>

<!-- Begin page content -->
<main role="main" class="flex-shrink-0">
  
  <div class="container">  
    

        <form class="form-signin" method="POST" action="">
              
            <?php
                if (isset($_SESSION['msgErros'])){
                    echo "<div class='alert alert-danger'>".$_SESSION['msgErros']."</div>";
                    unset($_SESSION['msgErros']);               
                }    
            ?>

            <?php
                foreach($result as $fin):
            ?>

            <div class="text-center mb-4">
                <img class="mb-4" src="img/categories-list.png" alt="Icone Principal">    
                <h3>Alterar Lançamento Financeiro</h3>
            </div>

            <input class="form-control" type="hidden" name="inputcodigoalterar" value="<?php echo $fin['lanc_id']; ?>">

            <div class="row">  
                
                <div class="col-sm-8">
                      <div class="form-group">
                        <label for="anun_descricao">Descrição Lançamento *</label>
                        <input type="text" class="form-control" id="anun_descricao" name="anun_descricao" placeholder="Descrição Lançamento" value="<?php echo $fin['lanc_descricao']; ?>" required>
                      </div>
                </div>


                <div class="col-sm-4">
                    <div class="form-group">
                        <label for="categoria_codigo">Tipo Lançamento *</label>
                        
                        <select name="categoria_codigo" id="categoria_codigo" class="form-control" required>
                          <?php                  

                          require 'class/categorias.class.php';
                          $cat = new Categorias();
                          $listacat = $cat->getCategorias();

                          foreach($listacat as $categoria):
                          ?>                            
                            <option value="<?php echo $categoria['cat_id']; ?>"<?php echo ($categoria['cat_id']==$fin['categoria_id'])?'selected="selected"':''; ?>> <?php echo $categoria['cat_descricao']; ?></option>
                          <?php
                          endforeach;
                          ?>
                        </select>
                      </div>
                  </div>             

            </div>

            <div class="row">            
                  <div class="col-sm-3">
                      <div class="form-group">
                        <label for="anun_nrodocumento">Número Documento *</label>
                        <input type="text" class="form-control" id="anun_nrodocumento" name="anun_nrodocumento" placeholder="Número Documento" value="<?php echo $fin['lanc_documento']; ?>" required>
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="anun_parcela">Parcela *</label>
                        <input type="number" class="form-control" id="anun_parcela" name="anun_parcela" min="1" placeholder="Parcela" value="<?php echo $fin['lanc_parcela']; ?>" required>
                      </div>
                    </div>
                    
                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="anun_valorparcela">Valor Parcela R$ *</label>
                        <input type="text" class="form-control money" id="anun_valorparcela" name="anun_valorparcela" placeholder="Valor Parcela" value="<?php echo number_format($fin['lanc_valor_porparcela'],2); ?>" required>
                      </div>
                    </div>

                    <div class="col-sm-3">
                      <div class="form-group">
                        <label for="anun_datavencimento">Data Vencimento *</label>                        
                        <input type="text" class="form-control" id="anun_datavencimento" name="anun_datavencimento" placeholder="dd/mm/aaaa" data-mask="00/00/0000" value="<?php echo date('d/m/Y', strtotime($fin['lanc_datavencimento'])); ?>" required>
                      </div>
                    </div>              
            </div>

            <?php
                endforeach;
            ?>

            <br/><br/>
            <button class="btn btn-primary btn-block" name="btnalterar" type="submit">Confirmar</button>
            

          </form>
          <br/>
          <a class="btn btn-link btn-sm" href="admin-fin-lista.php">Voltar -> Lista Financeira</a>      

    </div>
</main>

<?php
  require 'footer.php';
?>


