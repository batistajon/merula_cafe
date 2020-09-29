<?php

namespace Src\Controllers;

use Router\Controller\Action;
use Router\Model\Container;

/**
 * AdminController - Controla os recursos privados da aplicacao
 */
class AdminController extends Action
{
    public function admin()
    {
        $this->validaAutenticacao();
        $comentario = Container::getModel('Comentario');
        $comentario->__set('id_usuario', $_SESSION['id']);
        $comentarios = $comentario->getAll();
        $this->view->comentarios = $comentarios;

        $this->render('admin');
    }

    public function comentario()
    {
        $this->validaAutenticacao();
        $comentario = Container::getModel('Comentario');
        $comentario->__set('comentario', $_POST['comentario']);
        $comentario->__set('id_usuario', $_SESSION['id']);
        $comentario->salvar();

        header('Location: /admin');
    }

    public function beneficio()
    {
        $this->validaAutenticacao();

        $pesquisar = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '';

        $usuarios = array();

        if ($pesquisar != '') {
            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome', $pesquisar);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios = $usuario->getAll();
        }

        $this->view->usuarios = $usuarios;

        $this->render('beneficio');
    }

    public function acao()
    {
        $this->validaAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_beneficio = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if ($acao == 'fav') {
            $usuario->favBeneficio($id_usuario_beneficio);

        } elseif ($acao == 'nofav') {
            $usuario->noFavBeneficio($id_usuario_beneficio);
        }
        
        header('Location: /beneficio');
    }

    public function deleteComentario()
    {
       $this->validaAutenticacao();

       $acao = isset($_GET['acao']) ? $_GET['acao'] : '';

       $comentario = Container::getModel('Comentario');
       $comentario->__set('id_usuario', $_SESSION['id']);
       $comentario->__set('id', $acao);
       $comentario->delete();

       header('Location: /admin');
    }

    public function validaAutenticacao()
    {
        session_start();

        if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=erro');
        } else {
            $nome = explode(' ', $_SESSION['nome']);
            echo '<h3>Ola, '.ucfirst($nome['0']).'</h3>';
        }
    }
}