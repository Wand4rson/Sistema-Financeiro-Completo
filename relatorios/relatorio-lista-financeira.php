<?php session_start();error_reporting(0);ini_set('display_errors', 0);

require_once '../conn.php';
require_once '../pdf/Mpdf.php';    //Referencia do mPDF
require_once '../class/financeiro.class.php';

  //FOI NECESSARIO ATIVAR O BUFFER PARA USAR O SESSION START NO INICIO, USANDO O PHPINI
  //ESSA VERSÃO DO MPDF FOI TESTADA E HOMOLOGADA NA VERSÃO ANTERIOR A 7 DO PHP NÃO SEI SE IRA FUNCIONAR EM VERSÕES MAIS RECENTE


  //Verifica se usuario está logado//
  if (!isset($_SESSION['UserIDLogin']))
  {
    $_SESSION['msgErros'] = "Autenticação não realizada.<br/> Impossível Acessar Relatórios.<br/> Faça Login Novamente <a href='../login.php'>Clicando Aqui</a>";
    echo "<div class='alert alert-danger text-center'>".$_SESSION['msgErros']."</div>";   
    $_SESSION['msgErros'] = "";
    unset($_SESSION['msgErros']);  
    exit;
  }


  
  //Se sessão filtro não foi criado então mostra mensagem para usuário//
  if (!isset($_SESSION['filtros_param_impressao']))
  {
    $_SESSION['msgErros'] = "Nenhum parametro de impressão enviado.<br/> Tente Novamente. <a href='../admin-fin-lista.php'>Clicando Aqui</a>";
    echo "<div class='alert alert-danger text-center'>".$_SESSION['msgErros']."</div>";   
    $_SESSION['msgErros'] = "";
    unset($_SESSION['msgErros']);  
    exit;
  }


  $fin = new Financeiro();


  $filtros =  $_SESSION['filtros_param_impressao'];  

  /* Executa Consulta no DataBase */
  $lstfinTotais = $fin->getListaFinanceiroAllTotais($filtros);
  $lstfinTotaisPorTipo = $fin->getListaFinanceiroAllTotaisPorTipo($filtros);  
  $lstfin = $fin->getListaFinanceiroAll(0,100000,$filtros);    					
  $qtde_anuncios_total_db = $fin -> getCountLancamentosPorUsuario($filtros);
  

// Conteudo a Imprimir
ob_start();                   // Buffer the following html with PHP so we can store it to a variable later 

echo '<!doctype html>';
echo '<html lang="en" class="h-100">';
echo '<head>';
echo '<meta charset="utf-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">';
echo '<meta name="description" content="Aplicativo Financeiro Pessoal - Financeiro">';
echo '<meta name="author" content="Wanderson Santos">';
echo '<meta name="generator" content="">';
echo '<title>Painel - Sistema Financeiro</title>';
echo '<link href="../css/bootstrap.min.css" rel="stylesheet">';    
echo '<link href="../css/sticky-footer-navbar.css" rel="stylesheet">';    
echo '<link href="../icons/css/fontawesome.css" rel="stylesheet">';
echo '<link href="../icons/css/brands.css" rel="stylesheet">';
echo '<link href="../icons/css/solid.css" rel="stylesheet">';
echo '</head>';
echo '<body class="d-flex flex-column h-100">';
echo '<main role="main" class="flex-shrink-0">';
  
echo '<div class="container">';

echo '<p class="alert alert-info text-center">';
echo 'Relatório Financeiro - <small>'.$_SESSION["UserEmailLogin"].'</small>';
echo '<br/>';
echo '<small>Período : '.date('d/m/Y',strtotime($filtros['data_inicial'])).' até '.date('d/m/Y',strtotime($filtros['data_final'])).'</small>'; 
echo '<br/>';
echo '<small>Data/Hora de Impressão :'.date("d/m/Y \à\s\ H:i:s").'</small>';
echo '</p>';
echo '<br/>';
  
  
echo '<div class="table-responsive-xs">';
echo '<table class="table table-hover table-sm">';
echo '<thead>';
echo '<tr>';
echo '<th scope="col">#</th>';
echo '<th scope="col">Descrição</th>';
echo '<th scope="col">Tipo</th>';      
echo '<th scope="col">Vencimento</th>';      
echo '<th scope="col">Valor</th>';                  
echo '</tr>';
echo '</thead>';

foreach($lstfin as $lcto)
{ 

    echo '<tbody>';
    echo '<tr>';
    echo '<td><small>'. $lcto['lanc_id'].'</small></td>';


    echo '<td>'.$lcto['lanc_descricao'].''; 
    echo '<small> - Docto : '. $lcto['lanc_documento']. ' P: '.$lcto['lanc_parcela'].'</small>';
                        
      //Parcela não Paga
      if ($lcto['lanc_pago'] == 'nao')
      {  
        $data_hoje = new DateTime(date("Y-m-d"));                      
        $data_vencimento = new DateTime(date("Y-m-d",strtotime($lcto["lanc_datavencimento"])));                          
        $qtde_dias_vencido = $data_vencimento->diff($data_hoje);                          
        if (($data_vencimento < $data_hoje ))
        {                                                            
          echo '<small class="text-danger"> <i class="fas fa-exclamation-triangle"></i> Vencido há : ' .$qtde_dias_vencido->format('%r%a dias').'</small>';                                                            
        }                  
      }
        //Parcela Paga
        if ($lcto['lanc_pago'] == 'sim')
        {  
          echo '<br/><small class="text-info">';                      
          echo 'Quitado: '.$lcto['lanc_pago'].' - '; 
          echo 'Vlr Pg : '.number_format($lcto['lanc_valor_pagamento'],2,',','.').' - ';
          echo 'Data   : '.date('d/m/Y', strtotime($lcto['lanc_datapagamento'])).'';                      
          echo '</small>';
        }  

    echo '</td>';

    echo '<td><small $class_cor><strong>'.$lcto['categoria_nome'].'</strong></small></td>';            
    echo '<td>'.date('d/m/Y', strtotime($lcto['lanc_datavencimento'])).'</td>';
    echo '<td>'.number_format($lcto['lanc_valor_porparcela'],2,',','.').'</td>';    
    echo '</tr>';
    echo '</tbody>';

  }

echo '</table>';
echo '</div>';
  
echo '<hr class="my-1">';

  if(!empty($lstfin))
  { 
    echo '<br/><small class="text-muted">Qtde Registros encontrados :'.count($lstfin).'</small>';
  }

    
//Totais//
  if(!empty($lstfinTotaisPorTipo)){
    
    foreach($lstfinTotaisPorTipo as $totaltipo){
       
        if($totaltipo['categoria_tipo'] == 'D')
        {
          echo '<br/><strong class="text-danger">Despesas</strong>'; 
          echo '<small class="text-info"> Abertos  :'.number_format($totaltipo['TotalSumValorParcelasAbertoGeral'],2,',','.').'</small>';
          echo '<small class="text-primary"> Quitados :'.number_format($totaltipo['TotalSumValorPagosGeral'],2,',','.').'</small>';      
        }
        else
        {
              echo '<br/><strong class="text-success">Receitas</strong>';
              echo '<small class="text-info"> Abertos  :'.number_format($totaltipo['TotalSumValorParcelasAbertoGeral'],2,',','.').'</small>';
              echo '<small class="text-primary"> Quitados :'.number_format($totaltipo['TotalSumValorPagosGeral'],2,',','.').'</small>';      
        }
      }
  }

  if(!empty($lstfinTotais))
  {    
    echo '<br/><strong class="text-info">Total Geral</strong>';
    echo '<small class="text-info"> Abertos  :'.number_format($lstfinTotais['TotalSumValorParcelasAbertoGeral'],2,',','.').'</small>';      
    echo '<small class="text-primary"> Quitados :'.number_format($lstfinTotais['TotalSumValorPagosGeral'],2,',','.').'</small>';     

  }




echo '</div>';
echo'</main>';

echo '</body>';
echo '</html>';

//Fim do HTML//


//Inicio Biblioteca Imprimir

   
$html = ob_get_contents(); // Recupera o HTML Montado - Now collect the output buffer into a variable
ob_end_clean();            //Limpa Objeto

//var_dump($html);



$nome_arquivo_gerado = "print_".uniqid().".pdf";   //Nome do Arquivo a ser gerado em caso de download sem extensão pdf
$mpdf = new mPDF();                         //Instancia Biblioteca
$mpdf->AddPage();                           //Adiciona uma Pagina
$mpdf->setFooter('Versão 1.0|{DATE j/m/Y}|{PAGENO}');               //Numero da Pagina
$mpdf->WriteHTML($html);                    //Monta Relatorio
$mpdf->Output($nome_arquivo_gerado, 'I');   //Escolhe Tipo de Visualizacao

// I - Abre no navegador
// F - Salva o arquivo no servido
// D - Salva o arquivo no computador do usuário




//Fim Biblioteca Imprimir
?>

