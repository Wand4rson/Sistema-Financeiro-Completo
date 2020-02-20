<?php

class Categorias{

    public function getCategorias(){

        global $conn;
        

        $result = array();
        $sql = "SELECT * FROM tab_categorias WHERE user_codigo=:user_codigo";
        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
        $sql->execute();

        if ($sql->rowCount() > 0){
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        return $result;
    }

    public function getCategoriasPorID($cat_id){

        global $conn;
        
        $result = array();
        $sql = "SELECT * FROM tab_categorias WHERE user_codigo=:user_codigo AND cat_id=:cat_id";
        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
        $sql->bindValue("cat_id", $cat_id);
        $sql->execute();

        if ($sql->rowCount() > 0){
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
        }

        return $result;
    }

    public function RemoveCategoriaID($cat_id){
        global $conn;
        
        $sql = "DELETE FROM tab_categorias WHERE user_codigo=:user_codigo AND cat_id=:cat_id";
        $sql = $conn->prepare($sql);
        $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
        $sql->bindValue("cat_id", $cat_id);
        
        try
        {            
            return $sql->execute();
        }catch(PDOException $e){
            $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
            return false;
        }

    }

    public function AddCategoria($cat_descricao, $cat_ativo, $tipo_lancamento){
        global $conn;

        if(empty($cat_descricao)){
            $_SESSION['msgErros']  = "Descrição Obrigatória não informado.<br/>";
            return false;
            exit;
        }

        if(empty($cat_ativo)){
            $_SESSION['msgErros']  = "Status Obrigatória não informado.<br/>";
            return false;
            exit;
        }

        if(empty($tipo_lancamento)){
            $_SESSION['msgErros']  = "Tipo Lançamento Obrigatória não informado.<br/>";
            return false;
            exit;
        }


        $sql= " INSERT INTO tab_categorias(
            user_codigo,
            tipo_lancamento,            
            cat_descricao,
            cat_ativo,
            cat_datacadastro,
            cat_horacadastro,
            ip_lancamento)
        VALUES (
            :user_codigo,
            :tipo_lancamento,            
            :cat_descricao,
            :cat_ativo,
            :cat_datacadastro,
            :cat_horacadastro,
            :ip_lancamento)";
     
            try{
                
                $sql = $conn->prepare($sql);
                $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
                $sql->bindValue("tipo_lancamento", $tipo_lancamento);
                $sql->bindValue("cat_descricao", $cat_descricao);
                $sql->bindValue("cat_ativo", $cat_ativo);
                $sql->bindValue("cat_datacadastro", date('Y-m-d'));
                $sql->bindValue("cat_horacadastro", date('H:i:s', time()));
                $sql->bindValue("ip_lancamento",$_SERVER['REMOTE_ADDR']);

                return $sql->execute();
            }catch(PDOException $e){
                $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                return false;
            }
    }    
    
    
    /*Rotina Complementar ao cadastro de Usuários, ou seja Terminou o Cadastro já automaticamente
    adiciona algumas categorias defaults.*/
    public function AddCategoriaDefault($usuario_id,$cat_descricao, $tipo_lancamento){
        global $conn;

        if(empty($cat_descricao)){
            $_SESSION['msgErros']  = "Descrição Obrigatória não informado.<br/>";
            return false;
            exit;
        }

        if(empty($tipo_lancamento)){
            $_SESSION['msgErros']  = "Tipo Lançamento Obrigatória não informado.<br/>";
            return false;
            exit;
        }


        $sql= " INSERT INTO tab_categorias(
            user_codigo,
            tipo_lancamento,            
            cat_descricao,            
            cat_datacadastro,
            cat_horacadastro,
            ip_lancamento)
        VALUES (
            :user_codigo,
            :tipo_lancamento,            
            :cat_descricao,            
            :cat_datacadastro,
            :cat_horacadastro,
            :ip_lancamento)";
     
            try{
                
                $sql = $conn->prepare($sql);
                $sql->bindValue("user_codigo", $usuario_id);
                $sql->bindValue("tipo_lancamento", $tipo_lancamento);
                $sql->bindValue("cat_descricao", $cat_descricao);                
                $sql->bindValue("cat_datacadastro", date('Y-m-d'));
                $sql->bindValue("cat_horacadastro", date('H:i:s', time()));
                $sql->bindValue("ip_lancamento",$_SERVER['REMOTE_ADDR']);

                return $sql->execute();
            }catch(PDOException $e){
                $_SESSION['msgErros']  = "Categoria Default sistema. Infome Suporte : ".$e->getMessage() ."<br/>";
                return false;
            }
    }    

    public function EditCategoria($cat_id, $cat_descricao, $cat_ativo, $tipo_lancamento){
        global $conn;

        if(empty($cat_descricao)){
            $_SESSION['msgErros']  = "Descrição Obrigatória não informado.<br/>";
            return false;
            exit;
        }

        if(empty($cat_ativo)){
            $_SESSION['msgErros']  = "Status Obrigatória não informado.<br/>";
            return false;
            exit;
        }

        if(empty($tipo_lancamento)){
            $_SESSION['msgErros']  = "Tipo Lançamento Obrigatória não informado.<br/>";
            return false;
            exit;
        }


        $sql= " 
        UPDATE tab_categorias SET
            tipo_lancamento=:tipo_lancamento,            
            cat_descricao=:cat_descricao,
            cat_ativo=:cat_ativo
        WHERE
            user_codigo=:user_codigo AND cat_id=:cat_id";
     
            try{
                
                $sql = $conn->prepare($sql);
                $sql->bindValue("user_codigo", $_SESSION['UserIDLogin']);
                $sql->bindValue("cat_id", $cat_id);

                $sql->bindValue("tipo_lancamento", $tipo_lancamento);
                $sql->bindValue("cat_descricao", $cat_descricao);
                $sql->bindValue("cat_ativo", $cat_ativo);
                
                return $sql->execute();
            }catch(PDOException $e){
                $_SESSION['msgErros']  = $e->getMessage() ."<br/>";
                return false;
            }
    }

}


?>