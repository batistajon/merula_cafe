<?php

namespace Src\Controllers;

use Router\Controller\Action;
use Router\Model\Container;
use Src\Models\PagSeguro;

/**
 * AuthController - Controla as autenticacoes dos usuarios
 */
class AuthController extends Action
{
    public function autenticar()
    {
        $usuario = Container::getModel('Usuario');

        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', md5($_POST['senha']));
        $usuario->auth();

        if($usuario->__get('id') != '' && $usuario->__get('nome') != '') {
            
            session_start();

            $_SESSION['id'] = $usuario->__get('id');
            $_SESSION['nome'] = $usuario->__get('nome');

            header('Location: /admin');

        } else {

            header('Location: /?login=erro');
        }
    }

    public function sair()
    {
        session_start();
        session_destroy();
        header('Location: /');
    }

    public function notificacaoPagseguro()
    {
        header("access-control-allow-origin: https://pagseguro.uol.com.br");

        if(isset($_POST['notificationType']) && $_POST['notificationType'] == 'transaction'){
            $PagSeguro = new PagSeguro();
            $response = $PagSeguro->executeNotification($_POST);
            if( $response->status==3 || $response->status==4 ){
                //PAGAMENTO CONFIRMADO
                //ATUALIZAR O STATUS NO BANCO DE DADOS
                
            }else{
                //PAGAMENTO PENDENTE
                echo $PagSeguro->getStatusText($PagSeguro->status);
            }
        }
    }

    public function getStatus()
    {
        if(isset($_GET['reference'])){
            $PagSeguro = new PagSeguro();
            $P = $PagSeguro->getStatusByReference($_GET['reference']);
            echo $PagSeguro->getStatusText($P->status);
        }else{
            echo "Parâmetro \"reference\" não informado!";
        }
    }

    public function checkout()
    {
        header("access-control-allow-origin: https://pagseguro.uol.com.br");
        header("Content-Type: text/html; charset=UTF-8",true);
        date_default_timezone_set('America/Sao_Paulo');

        $PagSeguro = Container::getModel('PagSeguro');
            
        //EFETUAR PAGAMENTO	
        $venda = array("codigo"=>"1",
                    "valor"=>29.00,
                    "descricao"=>"Café Especial Merula - 250g",
                    "nome"=>"Jonathas Batista",
                    "email"=>"c1@sandbox.pagseguro.com.br",
                    "telefone"=>"(21)982019916",
                    "rua"=>"av henfil",
                    "numero"=>"25",
                    "bairro"=>"recreio",
                    "cidade"=>"Rio de Janeiro",
                    "estado"=>"RJ", //2 LETRAS MAIÚSCULAS
                    "cep"=>"22.795-641",
                    "codigo_pagseguro"=>"");
                    
        $PagSeguro->executeCheckout($venda,"http://landingpage.creditowin.com.br/notificacao-pagseguro/".$_GET['codigo']);

        //RECEBER RETORNO
        if( isset($_GET['transaction_id']) ){
            $pagamento = $PagSeguro->getStatusByReference($_GET['codigo']);
            
            $pagamento->codigo_pagseguro = $_GET['transaction_id'];
            if($pagamento->status==3 || $pagamento->status==4){
                //ATUALIZAR DADOS DA VENDA, COMO DATA DO PAGAMENTO E STATUS DO PAGAMENTO
                
            }else{
                //ATUALIZAR NA BASE DE DADOS
            }
        }
    }
}