<?php  
  require 'header.php';
  require 'class/financeiro.class.php';

      
      if (isset($_POST['anun_descricao']) && (!empty($_POST['anun_descricao']))){
        $anun_descricao = htmlspecialchars(addslashes($_POST['anun_descricao']));
      }

      if (isset($_POST['categoria_codigo']) && (!empty($_POST['categoria_codigo']))){
        $categoria_codigo = htmlspecialchars(addslashes($_POST['categoria_codigo']));
      }

      if (isset($_POST['anun_nrodocumento']) && (!empty($_POST['anun_nrodocumento']))){
        $anun_nrodocumento = htmlspecialchars(addslashes($_POST['anun_nrodocumento']));
      }

      if (isset($_POST['anun_valorparcela']) && (!empty($_POST['anun_valorparcela']))){
        $anun_valorparcela = htmlspecialchars(addslashes($_POST['anun_valorparcela']));
        //Converte Antes de Gravar no Banco
        $anun_valorparcela = str_replace(',','.', str_replace('.','', $anun_valorparcela));
      }

      if (isset($_POST['anun_datavencimento']) && (!empty($_POST['anun_datavencimento']))){
        $anun_datavencimento_primeira_parcela = addslashes($_POST['anun_datavencimento']);
        $anun_datavencimento = htmlspecialchars(addslashes($_POST['anun_datavencimento']));
      }

      /* Recupera Informações de Lançamentos Múltiplos */
        if (isset($_POST['qtde_parcelas_lancar']) && (!empty($_POST['qtde_parcelas_lancar']))){
          $qtde_parcelas_lancar = htmlspecialchars(addslashes($_POST['qtde_parcelas_lancar']));
          //echo "Qtde Parcelas Lançar : ".$qtde_parcelas_lancar;
        }
  
  
        if (isset($_POST['intervalo_dias_entre_as_parcelas']) && (!empty($_POST['intervalo_dias_entre_as_parcelas']))){
          $intervalo_dias_entre_as_parcelas = htmlspecialchars(addslashes($_POST['intervalo_dias_entre_as_parcelas']));
          //echo "Intervalo de Dias Entre as Parcelas : ".$intervalo_dias_entre_as_parcelas;
        }
  
  
        if (isset($_POST['tipo_intervalo_lcto']) && (!empty($_POST['tipo_intervalo_lcto']))){
          $tipo_intervalo_lcto = htmlspecialchars(addslashes($_POST['tipo_intervalo_lcto']));
          //echo "Tipo Lançamento Parcelas : ".$tipo_intervalo_lcto;
        }    
      /* ------- Fim de Recuperar Inf. Multiplos-------*/


      //Se Todos os valores obrigatorios foram preenchidos grava//
      if ((isset($_POST['anun_descricao']) && (!empty(trim($_POST['anun_descricao']))))  &&  
        (isset($_POST['anun_nrodocumento']) && (!empty(trim($_POST['anun_nrodocumento'])))) &&        
        (isset($_POST['anun_valorparcela']) && (!empty($_POST['anun_valorparcela']))) &&        
        (isset($_POST['qtde_parcelas_lancar']) && (!empty($_POST['qtde_parcelas_lancar']))) &&
        (isset($_POST['intervalo_dias_entre_as_parcelas']) && (!empty($_POST['intervalo_dias_entre_as_parcelas']))) &&
        (isset($_POST['anun_datavencimento']) && (!empty($_POST['anun_datavencimento'])))) 
        
          {   

              $fin = new Financeiro();
            
              /*-----------------------------------------------------------------------*/
              /*Formata data digitada para formato gravar banco de dados*/
              $dataexplode = explode("/",$anun_datavencimento);
              $data_vencimento_salvar_db = $dataexplode[2]."-".$dataexplode[1]."-".$dataexplode[0];
              $anun_datavencimento_primeira_parcela_salvar_db =  $dataexplode[2]."-".$dataexplode[1]."-".$dataexplode[0];
              /*-----------------------------------------------------------------------*/
              
                  if($fin -> AddFinanceiroMultiplos(
                            $qtde_parcelas_lancar,
                            $anun_datavencimento_primeira_parcela_salvar_db,
                            $tipo_intervalo_lcto,
                            $intervalo_dias_entre_as_parcelas,
                            $anun_nrodocumento,
                            $anun_descricao,                             
                            $anun_valorparcela, 
                            $anun_valorparcela, 
                            $data_vencimento_salvar_db, 
                            $categoria_codigo)!=false)
                  {	

                      $_SESSION['msgInfo']  = "Registro Inserido com sucesso [Lçtos Múltiplos]. <br/>";	    
                      header("Location:admin-fin-lista.php");
                      die();

                  }else
                  {
                  
                    $_SESSION['msgErros'] = "Erro ao executar método. Campos Obrigatórios não preenchidos !";
                  if (isset($_SESSION['msgErros'])){
                        echo "<div class='alert alert-danger'>".$_SESSION['msgErros']."</div>";                                          
                    }                  
                  }
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

            <div class="text-center mb-4">
                <img class="mb-4" src="img/categories-list.png" alt="Icone Principal">    
                <h5>Lançamento Financeiro - Múltiplas Parcelas</h5>
            </div>
                
                <div class="row">  
                    
                    <div class="col-sm-5">
                          <div class="form-group">
                            <label for="anun_descricao">Descrição Lançamento *</label>
                            <input type="text" class="form-control" id="anun_descricao" name="anun_descricao" placeholder="Descrição Lançamento" required>
                          </div>
                    </div>

                    <div class="col-sm-3">
                        <div class="form-group">
                            <label for="categoria_codigo">Tipo Lançamento *</label>
                            
                            <select name="categoria_codigo" id="categoria_codigo" class="form-control" required>
                              <?php                  

                              require 'class/categorias.class.php';
                              $cat = new Categorias();
                              $listacat = $cat->getCategorias();

                              foreach($listacat as $categoria):
                              ?>
                                <option value="<?php echo $categoria['cat_id']; ?>"><?php echo $categoria['cat_descricao']; ?></option>
                              <?php
                              endforeach;
                              ?>
                            </select>
                          </div>
                      </div>    

                      <div class="col-sm-2">
                          <div class="form-group">
                            <label for="anun_nrodocumento">Número Documento *</label>
                            <input type="text" class="form-control" id="anun_nrodocumento" name="anun_nrodocumento" placeholder="Número Documento" required>
                          </div>
                        </div>
                        
                        <div class="col-sm-2">
                          <div class="form-group">
                            <label for="anun_datavencimento">Data 1º Vencimento *</label>
                            <input type="text" class="form-control" id="anun_datavencimento" name="anun_datavencimento" placeholder="dd/mm/aaaa" data-mask="00/00/0000" required>
                          </div>
                        </div>   
                </div>



                  <div class="card card-body">
                    
                    <!-- Marca se é Multiplos Lançamentos -->        

                          <!-- Inicio Div, Multiplos -->
                          <div class="row">

                                <div class="col-sm-4">
                                  <div class="form-group">
                                    <label for="anun_valorparcela">Valor de Cada Parcela R$ *</label>
                                    <input type="text" class="form-control money" id="anun_valorparcela" name="anun_valorparcela" placeholder="Valor Parcela" required>
                                  </div>
                                </div>
                                
                                <div class="col-sm-4">
                                  <div class="form-group">
                                    <label for="qtde_parcelas_lancar">Qtde Parcelas a Lançar *</label>
                                    <input type="number" class="form-control" id="qtde_parcelas_lancar" min="1" name="qtde_parcelas_lancar" placeholder="Qtde Parcelas" value="1" required>
                                  </div>
                                </div>

                                <div class="col-sm-4">
                                  <div class="form-group">
                                    <label for="intervalo_dias_entre_as_parcelas">Intervalo Entre as Parcelas *</label>
                                    <input type="number" class="form-control" id="intervalo_dias_entre_as_parcelas" min="1" name="intervalo_dias_entre_as_parcelas" placeholder="Qtde dias Entre as parcelas" value="1" required>
                                  </div>
                                </div>

                              <div class="row">
                              
                                    <div class="col-sm-6">
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="radio" name="tipo_intervalo_lcto" id="tipo_intervalo_lcto" value="QtdeDias" checked required>
                                      <label class="form-check-label" for="tipo_intervalo_lcto">Intervalo por Qtde Dias</label>                                      
                                    </div>
                                      <p>
                                        <small class="text-muted">Considera o 1º Vencimento informado e a cada nova parcela soma a Qtde de Dias entre as Parcelas informada no parâmetro.</small>
                                      </p>
                                    </div>

                                    <div class="col-sm-6">
                                      <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="tipo_intervalo_lcto" id="tipo_intervalo_lcto" value="DiaFixo" required>
                                        <label class="form-check-label" for="tipo_intervalo_lcto">Intervalo por Dia Fixo</label>
                                      </div>
                                      
                                      <p>
                                        <small class="text-muted">Considera o 1º Vencimento informado e a cada nova parcela incrementa um Mês, mantendo assim o dia Fixo.</small>
                                      </p>

                                    </div>
                                    
                                  
                              </div> <!-- Fim Row dos Radios -->


                          </div>
                          <!-- Fim Div, Multiplos -->

                  </div> <!-- Fim div card -->


                  <br/><br/>
                  <button class="btn btn-primary btn-block" type="submit">Confirmar</button>
                
          </form>

          <br/>
          <a class="btn btn-link btn-sm" href="admin-fin-lista.php">Voltar -> Lista Financeira</a>      

    </div>
</main>

<?php
  require 'footer.php';
?>


