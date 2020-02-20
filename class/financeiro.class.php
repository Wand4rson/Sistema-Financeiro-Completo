<?php

class Financeiro{


public function AddFinanceiro(        
        $lanc_documento,
        $lanc_descricao,
        $lanc_parcela, 
        $lanc_valor_totaldocumento, 
        $lanc_valor_porparcela, 
        $lanc_datavencimento,
        $categoria_id){

        global $conn;


            if(empty($lanc_documento)){
                $_SESSION['msgErros']  = "Nro Documento não informado.<br/>";
                return false;
                exit;
            }

            if(empty($lanc_descricao)){
                $_SESSION['msgErros']  = "Descrição Lançamento não informado.<br/>";
                return false;
                exit;
            }

            if(empty($lanc_valor_porparcela)){
                $_SESSION['msgErros']  = "Valor Parcela do Lançamento não informado.<br/>";
                return false;
                exit;
            }

            if(empty($lanc_datavencimento)){
                $_SESSION['msgErros']  = "Vencimento do Lançamento não informado.<br/>";
                return false;
                exit;
            }

        $sql ="INSERT INTO tab_lancamentos(
                user_codigo,                
                lanc_documento,
                lanc_descricao,
                lanc_parcela,
                lanc_valor_totaldocumento,
                lanc_valor_porparcela,
                lanc_datalancamento,
                lanc_datavencimento, 
                ip_lancamento,
                lanc_datacadastro,
                lanc_horacadastro,
                categoria_id)
                
        VALUES (
                :user_codigo,                
                :lanc_documento,
                :lanc_descricao,
                :lanc_parcela,
                :lanc_valor_totaldocumento,
                :lanc_valor_porparcela,
                :lanc_datalancamento,
                :lanc_datavencimento,
                :ip_lancamento,
                :lanc_datacadastro,
                :lanc_horacadastro,
                :categoria_id)";        

                
        try{
                $sql = $conn->prepare($sql);
                $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);                
                $sql->bindValue("lanc_documento", $lanc_documento);
                $sql->bindValue("lanc_descricao",$lanc_descricao);
                $sql->bindValue("lanc_parcela",$lanc_parcela);
                
                $sql->bindValue("lanc_valor_totaldocumento",$lanc_valor_totaldocumento);
                $sql->bindValue("lanc_valor_porparcela",$lanc_valor_porparcela);
                
                $sql->bindValue("lanc_datalancamento", date('Y-m-d'));
                $sql->bindValue("lanc_datavencimento", $lanc_datavencimento);
                
                $sql->bindValue("ip_lancamento", $_SERVER['REMOTE_ADDR']);
                $sql->bindValue("lanc_datacadastro",date('Y-m-d'));
                $sql->bindValue("lanc_horacadastro", date('H:s:i', time()));

                $sql->bindValue("categoria_id", $categoria_id);


                return $sql->execute();
        }catch(PDOException $e){
                $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                return false;
        }

}


/*Adicionar Multiplas Parcelas
- Qtde Parcelas Lançar,
- Data de Vencimento da Primeira Parcela,
- Tipo de Intervalo das Parcelas a Lançar (Dia Fixo, Qtde Dias Somar)
        - Dia Fixo : Caso a Primeira parcela seja 15/04/2019
                - As demais serão no mesmo dia ex: 15/05/2019, 15/06/2019 ....
        - Qtde Dias Somar : Caso a primeira Parcela seja 15/04/2019 e a Qtde de dias Somar seja 2
                - As demais serão 17/04/2019, 19/04/2019, 21/04/2019.... Até a Qtde de parcelas desejadas
*/

public function AddFinanceiroMultiplos(
        $qtde_parcelas_lancar,
        $data_vencimento_primeira_parcela_lancar,
        $tipo_intervalo_parcelas_lancar,
        $intervalo_dias_entre_as_parcelas,
        $lanc_documento,
        $lanc_descricao,        
        $lanc_valor_totaldocumento, 
        $lanc_valor_porparcela, 
        $lanc_datavencimento,
        $categoria_id){

        global $conn;


            if(empty($qtde_parcelas_lancar)){
                $_SESSION['msgErros']  = "Qtde de Parcelas a Lançar não informado.<br/>";
                return false;
                exit;
            }
            
            if(empty($data_vencimento_primeira_parcela_lancar)){
                $_SESSION['msgErros']  = "Vencimento da primeira parcela não informado.<br/>";
                return false;
                exit;
            }
            
            if(empty($tipo_intervalo_parcelas_lancar)){
                $_SESSION['msgErros']  = "Tipo de Intervalo do parcelamento não informado.<br/>";
                return false;
                exit;
            }
            
            if(empty($intervalo_dias_entre_as_parcelas)){
                $_SESSION['msgErros']  = "Intervalo de dias entre as parcelas não informado.<br/>";
                return false;
                exit;
            }


            if(empty($lanc_documento)){
                $_SESSION['msgErros']  = "Nro Documento não informado.<br/>";
                return false;
                exit;
            }
            
            if(empty($lanc_descricao)){
                $_SESSION['msgErros']  = "Descrição Lançamento não informado.<br/>";
                return false;
                exit;
            }

            if(empty($lanc_valor_porparcela)){
                $_SESSION['msgErros']  = "Valor Parcela do Lançamento não informado.<br/>";
                return false;
                exit;
            }

            if(empty($lanc_datavencimento)){
                $_SESSION['msgErros']  = "Vencimento do Lançamento não informado.<br/>";
                return false;
                exit;
            }
        


        /*Campo Chave para Registrar nas Parcelas, para Identificar que fazem parte de um mesmo Lçto
        Afim de permitir excluir todos  e etc.*/    
        $documento_chave_geral = "";
        $documento_chave_geral = $lanc_documento."-". uniqid();



        //Inicia o Lçto da Parcela nº 1, estudar possibilidade de lançar parcela a continuar da informada//
        for($i=1;$i<=$qtde_parcelas_lancar;$i++){
                echo $i;                                
      
                
                                
                        /* Tratamento para Controle dos Vencimentos */
                        if ($tipo_intervalo_parcelas_lancar == 'QtdeDias')
                        {
                        //QtdeDias
                                $nro_parcela = $i;

                                if ($nro_parcela == 1)
                                {
                                        //Primeira Parcela Usa Vencimento da Primeira Informado.
                                        //Novo Vencimento
                                        $vencimento_parcela = $data_vencimento_primeira_parcela_lancar;
                                }
                                else
                                {
                                        
                                        $data_fixa = new DateTime($vencimento_parcela);                //Vencimento Padrão Default por Iteracao
                                        $IntervalString = "P".$intervalo_dias_entre_as_parcelas."D";   // Ex : P5D = + 5 Dias
                                        $data_fixa->add(new DateInterval($IntervalString));            //Adiciona a Qtde de Dias Informado
                                        $vencimento_parcela = $data_fixa->format('Y-m-d');             // Novo Vencimento
                                }                        
                        }
                        else
                        {
                        //DiaFixo

                                        $nro_parcela = $i;

                                        if ($nro_parcela == 1)
                                        {
                                                //Primeira Parcela Usa Vencimento da Primeira Informado.
                                                //Novo Vencimento
                                                $vencimento_parcela = $data_vencimento_primeira_parcela_lancar;
                                        }
                                        else
                                        {
                                               
                                                $data_fixa = new DateTime($vencimento_parcela);    //Vencimento Padrão Default por Iteracao
                                                $data_fixa->add(new DateInterval("P1M"));          //Adiciona 1 Mês a Cada Iteração
                                                $vencimento_parcela = $data_fixa->format('Y-m-d'); // Novo Vencimento

                                        }                        
                                //
                        }
                        /* ------ Fim Tratamento Controle de Vencimentos ------- */

                
                        $sql ="INSERT INTO tab_lancamentos(
                                user_codigo,                
                                lanc_documento,
                                lanc_descricao,
                                lanc_parcela,
                                lanc_valor_totaldocumento,
                                lanc_valor_porparcela,
                                lanc_datalancamento,
                                lanc_datavencimento, 
                                ip_lancamento,
                                lanc_datacadastro,
                                lanc_horacadastro,
                                categoria_id,
                                lanc_mult_qtdeparcelaslancar,
                                lanc_mult_intervalotipo,
                                lanc_mult_intervalo_dias_entre_asparcelas,
                                lanc_mult_documento_chave,
                                lanc_mult_data_primeiro_vencimento)                
                        VALUES (
                                :user_codigo,                
                                :lanc_documento,
                                :lanc_descricao,
                                :lanc_parcela,
                                :lanc_valor_totaldocumento,
                                :lanc_valor_porparcela,
                                :lanc_datalancamento,
                                :lanc_datavencimento,
                                :ip_lancamento,
                                :lanc_datacadastro,
                                :lanc_horacadastro,
                                :categoria_id,
                                :lanc_mult_qtdeparcelaslancar,
                                :lanc_mult_intervalotipo,
                                :lanc_mult_intervalo_dias_entre_asparcelas,
                                :lanc_mult_documento_chave,
                                :lanc_mult_data_primeiro_vencimento)";        

                                
                       try
                       {
                                $sql = $conn->prepare($sql);
                                $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);                
                                $sql->bindValue("lanc_documento", $lanc_documento);
                                $sql->bindValue("lanc_descricao",$lanc_descricao);
                                //$sql->bindValue("lanc_parcela",$lanc_parcela);
                                //$sql->bindValue("lanc_datavencimento", $lanc_datavencimento);
                                

                                $sql->bindValue("lanc_parcela",$nro_parcela);
                                $sql->bindValue("lanc_datavencimento", $vencimento_parcela);                        
                                $sql->bindValue("lanc_valor_porparcela",$lanc_valor_porparcela);                                
                                $sql->bindValue("lanc_valor_totaldocumento",$lanc_valor_totaldocumento);
                                $sql->bindValue("lanc_datalancamento", date('Y-m-d'));                                
                                $sql->bindValue("ip_lancamento", $_SERVER['REMOTE_ADDR']);
                                $sql->bindValue("lanc_datacadastro",date('Y-m-d'));
                                $sql->bindValue("lanc_horacadastro", date('H:s:i', time()));
                                $sql->bindValue("categoria_id", $categoria_id);

                                
                                //Lçtos Multiplos
                                $sql->bindValue("lanc_mult_qtdeparcelaslancar",$qtde_parcelas_lancar);
                                $sql->bindValue("lanc_mult_intervalotipo",$tipo_intervalo_parcelas_lancar);
                                $sql->bindValue("lanc_mult_intervalo_dias_entre_asparcelas",$intervalo_dias_entre_as_parcelas);                
                                $sql->bindValue("lanc_mult_data_primeiro_vencimento",$data_vencimento_primeira_parcela_lancar);

                                $sql->bindValue("lanc_mult_documento_chave",$documento_chave_geral);

                                $sql->execute();

                               
                        }catch(PDOException $e){
                                $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                                return false;
                        }
                //
        
        }//Fim Laço For

        //Não Apresentou Erro, Retorna True;
        return true;
}


public function AlterarFinanceiro(
        $lanc_id,        
        $lanc_documento,
        $lanc_descricao,
        $lanc_parcela, 
        $lanc_valor_totaldocumento, 
        $lanc_valor_porparcela, 
        $lanc_datavencimento,
        $categoria_id){

        global $conn;

       
        if(empty($lanc_documento)){
                $_SESSION['msgErros']  = "Nro Documento não informado.<br/>";
                return false;
                exit;
        }

        if(empty($lanc_descricao)){
                $_SESSION['msgErros']  = "Descrição Lançamento não informado.<br/>";
                return false;
                exit;
        }

        if(empty($lanc_valor_porparcela)){
                $_SESSION['msgErros']  = "Valor Parcela do Lançamento não informado.<br/>";
                return false;
                exit;
        }

        if(empty($lanc_datavencimento)){
                $_SESSION['msgErros']  = "Vencimento do Lançamento não informado.<br/>";
                return false;
                exit;
        }

        /* Verifica se já foi Quitado não permite Excluir */
        $sql = "SELECT * FROM tab_lancamentos WHERE user_codigo=:user_codigo AND lanc_id=:lanc_id AND lanc_pago='sim'";
        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
        $sql->bindValue("lanc_id", $lanc_id);
        $sql->execute();

        if($sql->rowCount() > 0 ){                
                $_SESSION['msgErros']  = "Lançamento quitado. Impossíve Alterar.<br/>";
                return false;
                exit;
        }
        /*Fim Lçto Quitado*/


        $sql ="UPDATE tab_lancamentos SET                        
                lanc_documento=:lanc_documento,
                lanc_descricao=:lanc_descricao,
                lanc_parcela=:lanc_parcela,
                lanc_valor_totaldocumento=:lanc_valor_totaldocumento,
                lanc_valor_porparcela=:lanc_valor_porparcela,                
                lanc_datavencimento=:lanc_datavencimento,
                categoria_id=:categoria_id                 
        WHERE
                user_codigo=:user_codigo AND lanc_id=:lanc_id";

        
        try{
                $sql = $conn->prepare($sql);

                $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
                $sql->bindValue("lanc_id", $lanc_id);
                
                $sql->bindValue("lanc_documento", $lanc_documento);
                $sql->bindValue("lanc_descricao",$lanc_descricao);
                $sql->bindValue("lanc_parcela",$lanc_parcela);
                
                $sql->bindValue("lanc_valor_totaldocumento",$lanc_valor_totaldocumento);
                $sql->bindValue("lanc_valor_porparcela",$lanc_valor_porparcela);                
                $sql->bindValue("lanc_datavencimento", $lanc_datavencimento);

                $sql->bindValue("categoria_id", $categoria_id);

                return $sql->execute();
        }catch(PDOException $e){
                $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                return false;
        }

}


public function DeletarFinanceiroID($lanc_id){
        global $conn;

        /* Verifica se já foi Quitado não permite Excluir */
        $sql = "SELECT * FROM tab_lancamentos WHERE user_codigo=:user_codigo AND lanc_id=:lanc_id AND lanc_pago='sim'";
        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
        $sql->bindValue("lanc_id", $lanc_id);
        $sql->execute();

        if($sql->rowCount() > 0 ){                
            $_SESSION['msgErros']  = "Lançamento quitado. Impossíve excluir.<br/>";
            return false;
            exit;
        }
        /*Fim Lçto Quitado*/


        $sql ="DELETE FROM tab_lancamentos WHERE user_codigo=:user_codigo AND lanc_id=:lanc_id";

        try{
                $sql = $conn->prepare($sql);

                $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
                $sql->bindValue("lanc_id", $lanc_id);

                return $sql->execute();
        }catch(PDOException $e){
                $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                return false;
        }

}

/*Lista todas as informações financeiras, com Limit 
para controle de paginação*/
public function getListaFinanceiroAll($limitInicial, $limitFinal, $filtros = ''){

        
        global $conn;

//        echo "Filtros Informados : <br/>";
//       print_r($filtros);

        $filtrostring = array();
        //Preencheu as duas Datas, faz Comparação Datas//
        if((!empty($filtros['data_inicial'])) && (!empty($filtros['data_final']))){
                //echo "DataInicial Preenchida : ".$filtros['data_inicial']."<br/>"; 
                //echo "DataFinal Preenchida   : ".$filtros['data_final']."<br/>";    
                $filtrostring[] = "(lanc_datavencimento>=:data_inicial_vencimento AND lanc_datavencimento<=:data_final_vencimento)";                
        }
        //Preencheu a categoria utiliza nos filtros//
        if (!empty($filtros['categoria_codigo'])){                
                $filtrostring[] = "categoria_id=:categoria_id_filtro";
        }

        $result = array();

        $sql ="SELECT *, 
                (SELECT cat_descricao FROM tab_categorias WHERE user_codigo=tab_lancamentos.user_codigo AND cat_id=tab_lancamentos.categoria_id) AS categoria_nome,
                (SELECT tipo_lancamento FROM tab_categorias WHERE user_codigo=tab_lancamentos.user_codigo AND cat_id=tab_lancamentos.categoria_id) AS categoria_tipo
         FROM 
                tab_lancamentos 
         WHERE user_codigo=:user_codigo AND ".implode(' AND ', $filtrostring)." ORDER BY lanc_datavencimento ASC LIMIT $limitInicial, $limitFinal";


        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);  

        //Preencheu as duas Datas, faz Comparação Datas//
        if((!empty($filtros['data_inicial'])) && (!empty($filtros['data_final']))){
                $sql->bindValue("data_inicial_vencimento",$filtros['data_inicial']);
                $sql->bindValue("data_final_vencimento",$filtros['data_final']);                
        }

        //Preencheu a categoria utiliza nos filtros//
        if (!empty($filtros['categoria_codigo'])){
                $sql->bindValue("categoria_id_filtro",$filtros['categoria_codigo']);                
        }

        $sql->execute();

        if ($sql->rowCount() > 0){
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        }

    
        return $result;
        
}

/*Qtde de Lançamentos do Usuário logado, informação necessária
para controle do páginador */
public function getCountLancamentosPorUsuario($filtros = ''){
        
        global $conn;

        $filtrostring = array();
        

        if((!empty($filtros['data_inicial'])) && (!empty($filtros['data_final']))){                
                $filtrostring[] = "(lanc_datavencimento>=:data_inicial_vencimento AND lanc_datavencimento<=:data_final_vencimento)";                
        }
        
        if (!empty($filtros['categoria_codigo'])){                
                $filtrostring[] = "categoria_id=:categoria_id_filtro";
        }


        $sql ="SELECT COUNT(*) as qtde FROM tab_lancamentos  WHERE user_codigo=:user_codigo AND ".implode(' AND ', $filtrostring)."";

        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);      
        

        if((!empty($filtros['data_inicial'])) && (!empty($filtros['data_final']))){
                $sql->bindValue("data_inicial_vencimento",$filtros['data_inicial']);
                $sql->bindValue("data_final_vencimento",$filtros['data_final']);                
        }
        
        if (!empty($filtros['categoria_codigo'])){
                $sql->bindValue("categoria_id_filtro",$filtros['categoria_codigo']);
        }            


        $sql->execute();
        
        $qtde_lancamentos = $sql->fetch();
        return $qtde_lancamentos['qtde'];

}


/* Function para Listar Totais Pagos e Abertos Ambos(Despesas/Receitas) do Usuário Informado
É complementar a Funcao getListaFinanceiroAll para Totalizar nas Listas */
public function getListaFinanceiroAllTotais($filtros = ''){

        global $conn;

        $filtrostring = array();
        $result = array();

        if((!empty($filtros['data_inicial'])) && (!empty($filtros['data_final']))){                                
                $filtrostring[] = "(lanc_datavencimento>=:data_inicial_vencimento AND lanc_datavencimento<=:data_final_vencimento)";                
        }

        if (!empty($filtros['categoria_codigo'])){                
                $filtrostring[] = "categoria_id=:categoria_id_filtro";
        }


        $sql ="SELECT 	        	
                (SELECT tipo_lancamento FROM tab_categorias WHERE user_codigo=tab_lancamentos.user_codigo AND cat_id=tab_lancamentos.categoria_id) AS categoria_tipo,	        
                (COALESCE(SUM(lanc_valor_porparcela),0) - COALESCE(SUM(lanc_valor_pagamento),0)) AS TotalSumValorParcelasAbertoGeral,
	        COALESCE(SUM(lanc_valor_pagamento),0) AS 'TotalSumValorPagosGeral'
        FROM 
                tab_lancamentos
        WHERE 
                user_codigo=:user_codigo AND ".implode(' AND ', $filtrostring)." GROUP BY user_codigo";

        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);      
                
        if((!empty($filtros['data_inicial'])) && (!empty($filtros['data_final']))){
                $sql->bindValue("data_inicial_vencimento",$filtros['data_inicial']);
                $sql->bindValue("data_final_vencimento",$filtros['data_final']);                
        }        

        if (!empty($filtros['categoria_codigo'])){
                $sql->bindValue("categoria_id_filtro",$filtros['categoria_codigo']);
        }



        $sql->execute();

        if ($sql->rowCount() > 0){
                $result = $sql->fetch();
        }

       
        return $result;

}

/* Function para Listar Totais Pagos e Abertos Por Tipo de Lançamento(Despesas/Receitas) do Usuário Informado
É complementar a Funcao getListaFinanceiroAll para Totalizar nas Listas */
public function getListaFinanceiroAllTotaisPorTipo($filtros = ''){

        global $conn;

        $filtrostring = array();
        
        if((!empty($filtros['data_inicial'])) && (!empty($filtros['data_final']))){                                
                $filtrostring[] = "(lanc_datavencimento>=:data_inicial_vencimento AND lanc_datavencimento<=:data_final_vencimento)";                
        }

        if (!empty($filtros['categoria_codigo'])){                
                $filtrostring[] = "categoria_id=:categoria_id_filtro";
        }


        $result = array();
        
        $sql ="SELECT 	        	
                (SELECT tipo_lancamento FROM tab_categorias WHERE user_codigo=tab_lancamentos.user_codigo AND cat_id=tab_lancamentos.categoria_id) AS categoria_tipo,	        
                (COALESCE(SUM(lanc_valor_porparcela),0) - COALESCE(SUM(lanc_valor_pagamento),0)) AS TotalSumValorParcelasAbertoGeral,
	        COALESCE(SUM(lanc_valor_pagamento),0) AS 'TotalSumValorPagosGeral'
        FROM 
                tab_lancamentos
        WHERE                 
                user_codigo=:user_codigo AND ".implode(' AND ', $filtrostring)." GROUP BY user_codigo, categoria_tipo";


        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);    
        
        if((!empty($filtros['data_inicial'])) && (!empty($filtros['data_final']))){
                $sql->bindValue("data_inicial_vencimento",$filtros['data_inicial']);
                $sql->bindValue("data_final_vencimento",$filtros['data_final']);                
        }        

        if (!empty($filtros['categoria_codigo'])){
                $sql->bindValue("categoria_id_filtro",$filtros['categoria_codigo']);
        }

        
        $sql->execute();

        if ($sql->rowCount() > 0){
                $result = $sql->fetchAll();
        }

        return $result;

}


/* Function para Listar Totais Parcelas em Aberto, A Vencer, Vencidas por Tipo Despesas/Receitas
Utilizado para Quadro de estatisticas de acordo com o Usuario Informado
        - Tipo = Vencer/Vencido;
*/
public function getListaFinanceiroEstatistica($data_base, $tipo){

        global $conn;

        $filtrostring = array();
        $result = array();
        
        /* Controle do Sinal de Comparação */
        if (!empty($tipo == 'Vencer')){                                
                $filtrostring[] = "(lanc_datavencimento>:data_base_vencimento)";  //Parcelas a Vencer               
        }else{
                $filtrostring[] = "(lanc_datavencimento<=:data_base_vencimento)";  //Vencidos, data atual para tras              
        }
        
        $sql ="SELECT 	        	
                (SELECT tipo_lancamento FROM tab_categorias WHERE user_codigo=tab_lancamentos.user_codigo AND cat_id=tab_lancamentos.categoria_id) AS categoria_tipo,	        
                COALESCE(SUM(lanc_valor_porparcela),0)  AS TotalSumValorParcelas
                FROM 
                        tab_lancamentos
                WHERE
                        user_codigo=:user_codigo AND ".implode(' AND ', $filtrostring)." AND lanc_pago='nao' GROUP BY user_codigo, categoria_tipo";                 
                                
                $sql = $conn->prepare($sql);
                $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);    
                $sql->bindValue("data_base_vencimento",$data_base);                
                $sql->execute();


        if ($sql->rowCount() > 0){
                $result = $sql->fetchAll();
        }

        return $result;

}

public function getListaFinanceiroID($lanc_id){
        
        global $conn;

        $result = array();

        $sql ="SELECT * FROM tab_lancamentos WHERE user_codigo=:user_codigo AND lanc_id=:lanc_id";

        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);   
        $sql->bindValue("lanc_id", $lanc_id);             
        $sql->execute();

        if ($sql->rowCount() > 0){
                $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        return $result;
}

public function setLancamentoPago($lanc_id, $lanc_valor_pagamento){
        
        global $conn;

        if(empty($lanc_id)){
                $_SESSION['msgErros']  = "Id Lançamento não informado.<br/>";
                return false;
                exit;
        }

        if(empty($lanc_valor_pagamento)){
                $_SESSION['msgErros']  = "Valor Pago do Documento não informado.<br/>";
                return false;
                exit;
        }

        /* Verifica se já foi Quitado não permite Excluir */
        $sql = "SELECT * FROM tab_lancamentos WHERE user_codigo=:user_codigo AND lanc_id=:lanc_id AND lanc_pago='sim'";
        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
        $sql->bindValue("lanc_id", $lanc_id);
        $sql->execute();

        if($sql->rowCount() > 0 ){                
                $_SESSION['msgErros']  = "Lançamento já quitado. Impossíve Pagar Novamente.<br/>";
                return false;
                exit;
        }
        /*Fim Lçto Quitado*/


        $sql =" UPDATE tab_lancamentos SET
                        lanc_datapagamento=:lanc_datapagamento,
                        lanc_valor_pagamento=:lanc_valor_pagamento,
                        lanc_pago=:lanc_pago
                WHERE 
                        user_codigo=:user_codigo  AND lanc_id=:lanc_id";

        
        try{
                $sql = $conn->prepare($sql);

                $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
                $sql->bindValue("lanc_id", $lanc_id);

                $sql->bindValue("lanc_datapagamento", date('Y-m-d'));
                $sql->bindValue("lanc_valor_pagamento", $lanc_valor_pagamento);
                $sql->bindValue("lanc_pago",'sim');

                return $sql->execute();
        }catch(PDOException $e){
                $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                return false;
        }
}


public function setLancamentoEstornoPago($lanc_id){
        
        global $conn;

        if(empty($lanc_id)){
                $_SESSION['msgErros']  = "Id Lançamento não informado.<br/>";
                return false;
                exit;
        }


        /* Verifica se já foi Quitado não permite Excluir */
        $sql = "SELECT * FROM tab_lancamentos WHERE user_codigo=:user_codigo AND lanc_id=:lanc_id AND lanc_pago='nao'";
        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
        $sql->bindValue("lanc_id", $lanc_id);
        $sql->execute();

        if($sql->rowCount() > 0 ){                
                $_SESSION['msgErros']  = "Lançamento não quitado. Impossíve Estornar.<br/>";
                return  false;
                exit;
        }
        /*Fim Lçto Quitado*/


        $sql =" UPDATE tab_lancamentos SET
                        lanc_datapagamento=:lanc_datapagamento,
                        lanc_valor_pagamento=:lanc_valor_pagamento,
                        lanc_pago=:lanc_pago
                WHERE 
                        user_codigo=:user_codigo  AND lanc_id=:lanc_id";

        
        try{
                $sql = $conn->prepare($sql);

                $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
                $sql->bindValue("lanc_id", $lanc_id);

                $sql->bindValue("lanc_datapagamento", '');
                $sql->bindValue("lanc_valor_pagamento", 0);
                $sql->bindValue("lanc_pago",'nao');

                return $sql->execute();
        }catch(PDOException $e){
                $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                return false;
        }
}


}

?>