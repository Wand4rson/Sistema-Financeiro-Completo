<?php  
  require 'header.php';
  require 'class/financeiro.class.php';

  $fin = new Financeiro();
  
  //Foi Inicializado para evitar erro ao carregar campos preenchidos
  $filtros = array(
      'data_inicial' => '',
      'data_final' => '',
      'categoria_codigo' => ''    
  );

  if(isset($_GET['filtros'])) {
    $filtros = $_GET['filtros'];     
  }

  
  //Filtro Vazio, Preenche com Data Inicio e Fim do Mês Corrente//
  //para não ficar com erro o campo de date
  if(empty($filtros['data_inicial'])) {
    $filtros['data_inicial'] = date('Y-m')."-01";
    //date('Y-m-d') = Data Atual corrente;
  }

  if(empty($filtros['data_final'])) {
    $filtros['data_final'] = date("Y-m-t");
    //date('Y-m-d')  = Data Atual
    //date("t/m/Y")  = Ultimo dia do Mes Corrente
  }

  

  /* ADICIONA A VARIAVEL FILTROS PARA UMA SESSÃO AFIM DE UTILIZAR NOS RELATORIOS, POIS NÃO FOI POSSIVEL PASSAR UM ARRAY POR GET
  UMA OUTRA SAIDA SERIA CRIAR UM FORM POST */
  if (isset($_GET['filtros'])) 
  {
    //Recupera Filtros do GET
    $_SESSION['filtros_param_impressao'] = $_GET['filtros'];       
  }
  else
  {
    //Primeira Vez, Pega Variavel filtros criada em tempo de execução
    $_SESSION['filtros_param_impressao'] = $filtros;
  }
  


  $lstfinTotais = $fin->getListaFinanceiroAllTotais($filtros);
  $lstfinTotaisPorTipo = $fin->getListaFinanceiroAllTotaisPorTipo($filtros);

    /* ------ INICIO CONTROLE DE PAGINACAO  ------ */

		//Numero de itens a serem exibidos na pagina
		$qtde_itens_por_pagina=50; 

    /*Recupera o GET do páginador clicado e inicia testes para controle de páginas com base
    na página Atual do paginador, em qual pagina está e usa o Limit*/

    if (isset($_GET['pagina']))
      {
          //Tem Página informada no isset recupera página e utiliza para definir limites
          $pagina_atual = addslashes(intval($_GET['pagina']));  
          
          /*Pega o Limit inicial - qtde por página e usa ex : 
          página =1 => 1/15
          página =2 => 15/30
          ...
          */

          $qtde_inicio_limit=($qtde_itens_por_pagina*$pagina_atual)-$qtde_itens_por_pagina; 
          
          //Recupera os dados com base nos Limits Informados
          $lstfin = $fin->getListaFinanceiroAll($qtde_inicio_limit,$qtde_itens_por_pagina,$filtros);    					
      }
    else
      {
        //Não tem página no isset utiliza inicializa na primeira página
        $pagina_atual = 1;
  
        $qtde_inicio_limit=($qtde_itens_por_pagina*$pagina_atual)-$qtde_itens_por_pagina;		
        
        //Recupera os dados com base nos Limits Informados
        $lstfin = $fin->getListaFinanceiroAll($qtde_inicio_limit,$qtde_itens_por_pagina,$filtros);    					
		}


		//Recupera Qtde de Lançamento existentes pelo Usuário Logado
		$qtde_anuncios_total_db = $fin -> getCountLancamentosPorUsuario($filtros);
		
		//Calcula Quantas páginas serão necessárias criar - utilizando dados
		$qtde_paginas_paginador = ceil($qtde_anuncios_total_db / $qtde_itens_por_pagina);

    /* ------ FIM CONTROLE DE PAGINACAO PAGINAÇÃO ------ */    

?>

<!-- Begin page content -->
<main role="main" class="flex-shrink-0">
  
  <div class="container">  
    
    <!-- Mensagem Novidades -->
    <br/> 
    <div class="alert alert-warning" role="alert">      
      <br/><small class="text-muted">* Faça uma busca com os filtros que deseja e gere a impressão, somente será impresso os resultados montados na tabela.</small>
    </div>
    
    <hr class="my-1">
  
    <form class="form" method="GET">
      
      <div class="row">
        <div class="col">
          <div class="form-group">      
            <label for="data_inicial">Vencimento Inicial</label> <!-- Data no formato value=yyyy-mm-dd -->  
            <input type="date" name="filtros[data_inicial]" id="data_inicial" class="form-control form-control-sm" placeholder="dd/mm/yyyy" value="<?php echo date('Y-m-d', strtotime($filtros['data_inicial'])) ;?>">          
          </div>
        </div>
        
        <div class="col">
          <div class="form-group">      
            <label for="data_final">Vencimento Final</label>
            <input type="date" name="filtros[data_final]" id="data_final" class="form-control form-control-sm" placeholder="dd/mm/yyyy" value="<?php echo date('Y-m-d', strtotime($filtros['data_final'])) ;?>">
          </div>
        </div>
      
      
          <div class="col">
          <div class="form-group">
            <label for="categoria_codigo">Tipo Lançamento</label>
            
            <select name="filtros[categoria_codigo]" id="categoria_codigo" class="form-control form-control-sm">
            <option value="">Todos</option>

              <?php                  

              require 'class/categorias.class.php';
              $cat = new Categorias();
              $listacat = $cat->getCategorias();
                        
              foreach($listacat as $categoria):
            
              ?>                          
                <option value="<?php echo $categoria['cat_id']; ?>" <?php echo ($categoria['cat_id'] == $filtros['categoria_codigo'])?'selected="selected"':''; ?>><?php echo $categoria['cat_descricao']; ?></option>
              <?php

              endforeach;
              ?>
            </select>
          </div>
        </div><!-- Fim div col -->
        
        <div class="col">
          <div class="form-group">
            <label class="invisible" for="">Botão</label>
            <button type="submit" class="btn btn-primary form-control form-control-sm"><i class="fas fa-search"></i></button>
          </div>
        </div>

     
      <div class="col">
          <div class="form-group">
            <label class="invisible" for="">Botão</label>
            <a href="relatorios/relatorio-lista-financeira.php" class="btn btn-warning form-control form-control-sm"><i class="fas fa-print"></i></a>                      
          </div>
        </div>
      
      <?php 
        //endif;
      ?>

      </div> <!-- Fim div Row -->    
    </form>

    <br/>
    <a class="btn btn-outline-primary btn-sm" href="admin-fin-add.php"><i class="fas fa-plus-circle"></i> Novo Lçto Individual</a>
    <a class="btn btn-outline-primary btn-sm" href="admin-fin-add-multiplos.php"><i class="fas fa-plus-circle"></i> Novo Lçto Múltiplos</a>
    
 
    <br/><br/>
  
   <?php
    if (isset($_SESSION['msgInfo'])){      
        echo "<div class='alert alert-info text-center'>".$_SESSION['msgInfo']."</div>";   
        unset($_SESSION['msgInfo']);         
    }
  ?>

  <!-- Lista com Documentos -->
  <div class="table-responsive-xs">
  <table class="table table-hover table-sm">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Descrição</th>
      <th scope="col">Tipo</th>      
      <th scope="col">Valor</th>            
      <th scope="col">Ações</th>      

    </tr>
  </thead>


        <?php foreach($lstfin as $lcto): ?>

         <!-- Formata Cor -->
        <?php 
          if($lcto['categoria_tipo'] == 'D')
          {
            $class_cor = "class='text-danger'";
            $class_cor_tr = "class=''";//table-danger
          }
          else
          {
            $class_cor = "class='text-success'";
            $class_cor_tr = "class=''"; //table-success
          }
        ?>
        <!-- Fim Formata Cor -->


        <tbody>
          <!-- Caso Queira Personalizar cor da Linha da Table -->
          <tr <?php echo $class_cor_tr;?>>
            
            <td>
              <?php echo "<small>". $lcto['lanc_id']."</small>";?>
            </td>

            <td>
                  <?php echo $lcto['lanc_descricao'] ;?>                  
                  
                  <!-- Detalhes -->
                  <div class="form-group">
                    <?php echo "<small>Vencimento : ". date('d/m/Y', strtotime($lcto['lanc_datavencimento']))."</small>" ; ?>                    
			              <?php echo "<small>Docto : ". $lcto['lanc_documento']. " P: ".$lcto['lanc_parcela']."</small>"; ?>



                    <?php 
                      //Se Parcela Já Está Vencida e não Quitada, Mostra Identificação de Parcela Vencida.
                      if ($lcto['lanc_pago'] == 'nao'){                            
                          
                          //Objetos do Tipo DateTime podem Ser Comparados com DateDiff//
                          $data_hoje = new DateTime(date('Y-m-d'));                      
                          $data_vencimento = new DateTime(date('Y-m-d',strtotime($lcto['lanc_datavencimento'])));
                          
                            //Nesta Condição se > 0 Já Vencido... Se < 0 Não venceu Ainda.
                            $qtde_dias_vencido = $data_vencimento->diff($data_hoje);
                          
                            if (($data_vencimento < $data_hoje )){                                                            
                              echo "<p class='text-danger'><i class='fas fa-exclamation-triangle'></i><small> Vencido há : " .$qtde_dias_vencido->format('%r%a dias')."</small></p>";                                                            
                            }
                      
                      }
                      //Fim Verificação Parcela Vencida
                     ?>                    

                        <!-- Pago -->
                        <?php 
                          if ($lcto['lanc_pago'] == 'sim'){  
                                echo "<br/><small class='text-info'>";                      
                                echo "Quitado: ".$lcto['lanc_pago']." - "; 
                                echo "Vlr Pg : ".number_format($lcto['lanc_valor_pagamento'],2,',','.')." - ";
                                echo "Data   : ". date('d/m/Y', strtotime($lcto['lanc_datapagamento']))."";                      
                                echo "</small>";
                            }                  
                        ?>
                        <!-- Fim Pago -->

                  </div>
                  <!-- Fim Detalhes -->
            </td>

            <td>
                <?php echo "<small $class_cor><strong>".$lcto['categoria_nome']."</strong></small>"; ?>
            </td>               
            <td>
                  <?php echo number_format($lcto['lanc_valor_porparcela'],2,',','.') ; ?>              
            </td>


					<td>
              <div class="row">              
                  <form class="form-inline" action="admin-fin-alterar.php" method="post">
                    <?php //Envia via Post para Outro Arquivo o Codigo que vai ser removido, na outra tela iremos recuperar o campo codigo ?> 
                    <input type="hidden" name="fin_id" value="<?=$lcto['lanc_id']?>">               
                    <button type="submit" name="editar" class="btn btn-link btn-sm text-success alert-link" <?php echo($lcto['lanc_pago'] == 'sim')? 'disabled="disabled"':'';?>><i class="fas fa-edit"></i> Editar</button>              
                  </form>

                  <form class="form-inline" action="admin-fin-quitar.php" method="post">
                    <?php //Envia via Post para Outro Arquivo o Codigo que vai ser removido, na outra tela iremos recuperar o campo codigo ?> 
                    <input type="hidden" name="fin_id" value="<?=$lcto['lanc_id']?>"> 
                    <input type="hidden" name="fin_valor_pagamento" value="<?=$lcto['lanc_valor_porparcela']?>">
                    <button type="submit" name="quitar" class="btn btn-link btn-sm text-primary alert-link" <?php echo($lcto['lanc_pago'] == 'sim')? 'disabled="disabled"':'';?>><i class="fas fa-money-bill-alt"></i> Quitar</button>              
                  </form>

                  <form class="form-inline" action="admin-fin-estornar.php" method="post">
                    <?php //Envia via Post para Outro Arquivo o Codigo que vai ser removido, na outra tela iremos recuperar o campo codigo ?> 
                    <input type="hidden" name="fin_id" value="<?=$lcto['lanc_id']?>">               
                    <button type="submit" name="estornar" class="btn btn-link btn-sm text-info alert-link" <?php echo($lcto['lanc_pago'] == 'sim')? '':'disabled="disabled"';?>><i class="fas fa-undo-alt"></i> Estornar</button>
                  </form>

                  <form class="form-inline" action="admin-fin-deletar.php" method="post">
                    <?php //Envia via Post para Outro Arquivo o Codigo que vai ser removido, na outra tela iremos recuperar o campo codigo ?> 
                    <input type="hidden" name="fin_id" value="<?=$lcto['lanc_id']?>">               
                    <button type="submit" name="deletar" class="btn btn-link btn-sm text-danger alert-link" <?php echo($lcto['lanc_pago'] == 'sim')? 'disabled="disabled"':'';?>><i class="fas fa-trash-alt"></i> Remover</button>              
                  </form>
              </div>

					</td> 
          
       </tbody>

        <?php endforeach; ?>

  </table>

  </div> <!-- Fim Table responsive -->

      <!-- Totais Labels -->
      <div class="container">
        
        <hr class="my-1">

        <!-- Totais Gerais Por Tipo (Despesas/Receitas) --> 
        <?php if(!empty($lstfinTotaisPorTipo)) : ?>      
            <?php foreach($lstfinTotaisPorTipo as $totaltipo) : ?>            
                <?php  if($totaltipo['categoria_tipo'] == 'D') :  ;?>    
                      <br/><strong class="text-danger">Despesas</strong> 
                      <small class="text-info">Abertos  : <?php echo number_format($totaltipo['TotalSumValorParcelasAbertoGeral'],2,',','.') ;?></small>
                      <small class="text-primary">Quitados : <?php echo number_format($totaltipo['TotalSumValorPagosGeral'],2,',','.') ;?></small>      
                <?php  else: ;?>  
                      <br/><strong class="text-success">Receitas</strong>
                      <small class="text-info">Abertos  : <?php echo number_format($totaltipo['TotalSumValorParcelasAbertoGeral'],2,',','.') ;?></small>
                      <small class="text-primary">Quitados : <?php echo number_format($totaltipo['TotalSumValorPagosGeral'],2,',','.') ;?></small>      
                <?php  endif;?>             
            <?php endforeach; ?>       
        <?php endif; ?> <!-- fim lsttotaisportipo -->
        
        <?php if(!empty($lstfinTotais)){ ?>
          <!-- Totais Gerais sem Distincao de Tipo (Despesas/Receitas) -->  
          <br/><strong class="text-info">Total Geral</strong>
          <small class="text-info">Abertos  : <?php echo number_format($lstfinTotais['TotalSumValorParcelasAbertoGeral'],2,',','.') ;?></small>      
          <small class="text-primary">Quitados : <?php echo number_format($lstfinTotais['TotalSumValorPagosGeral'],2,',','.') ;?></small>     
        
        <?php } ?>
      
        <?php if(!empty($lstfin)){ ?>
          <br/><small class="text-muted">Qtde Registros encontrados : <?php echo count($lstfin) ;?></small><br/><br/>       
        <?php } ?>

        
      </div> <!-- fim container total labels -->


      <!-- Inicio Paginador -->     
            
          <nav aria-label="Page navigation">
            <ul class="pagination pagination-sm justify-content-center">
                    
                    <li>
                        <a class="page-link" href="admin-fin-lista.php?pagina=1" aria-label="Previous">
                            <span aria-hidden="true">Primeira Página</span>
                        </a>                        
                    </li>
                    
                        <?php for($i=1;$i<=$qtde_paginas_paginador;$i++){ ?>

                            <?php 
                                if ($pagina_atual == $i){ 
                                    //Já possui a plavra class no page-item                                      
                                    //$class_active = 'class=active' ;
                                    $class_active = 'active';
                                }else{
                                    $class_active ='';                                    
                                }                              
                            ?>
                            
                      
                            <!-- Concatenacao com GET -->
                            <li class="page-item <?php echo $class_active; ?>"><a class="page-link" href="admin-fin-lista.php?<?php 
                            
                            
                            $tudo_get = $_GET;                  //Recupera Tudo que Está no GET
                            $tudo_get['pagina'] = $i;           //Adiciona a Página clicada
                            echo http_build_query($tudo_get);   //Monta novo Get

                            ?>"><?php echo $i; ?> </a></li>



                        <?php } ?>

                    <li>
                        <a class="page-link" href="admin-fin-lista.php?pagina=<?php echo $qtde_paginas_paginador; ?>" aria-label="Next">
                            <span aria-hidden="true">Ultima Página</span>
                        </a>
                    </li>

            </ul>
        </nav>
    
    <!-- Fim Paginador -->
       

  </div>
</main>

<?php
  require 'footer.php';
?>