<?php

namespace Src\Controllers;

use Router\Controller\Action;
use Router\Model\Container;

/**
 * IndexController
 */
class IndexController extends Action {

	public function index()
	{
		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
		$this->render('index');
	}

	public function sobre()
	{
		$this->render('sobre');
	}

	public function contato()
	{
		$this->render('contato');
	}

	public function clubeImpacto()
	{
		$this->render('clube-de-impacto');
	}

	public function paymentCreditCard()
	{
		$this->render('payment');
	}

	public function redirectAssinar()
	{
		$this->view->assinar = isset($_POST['radio']) ? $_POST['radio'] : '';

		if($this->view->assinar == 'moido')
		{
			echo "<script>window.location.href='http://pag.ae/7WrFko9pt'</script>";

		} else
		{
			echo "<script>window.location.href='http://pag.ae/7WrFkXTTP'</script>";
		}
	}

	public function cadastro()
	{
		$this->view->erroCadastro = false;

		$this->render('cadastro');
	}

	public function registrar()
	{
		/**
		 * Recebe os dados do formulario via post
		 */
		$usuario = Container::getModel('Usuario');

		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', md5($_POST['senha']));

		$usuario->salvar();

		/**
		 * Condicao para sucesso do resgitro
		 */
		
		/* if ($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0) {

			$usuario->salvar();

			$this->render('cadastro_sucesso');

		} else {

			$this->view->erroCadastro = true;

			$this->render('cadastro');
		} */
	}
}