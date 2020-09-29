<?php

namespace Src\Controllers;

use Router\Controller\Action;
use Router\Model\Container;
use Src\Pagseguro\PagSeguro;

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
}