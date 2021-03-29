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

	public function email()
	{
		$this->render('email');
	}

	public function loginValidate()
	{
		$data =  filter_input_array(INPUT_POST, FILTER_DEFAULT);

		//print_r(json_encode($data));
		session_start();

		$valorFreteTratado = explode(' ', $data['shippingCost'])[1];

		$_SESSION['frete'] = [
			'shippingType' => $data['shippingType'],
			'cep' => $data['cep'],
			'shippingCost' => $valorFreteTratado
		];

		$_SESSION['totalAssinatura'] = [
			'total' => $data['totalAssinatura'],
			'frete' => $data['freteAssinatura']
		];

		print_r(json_encode($_SESSION));
	}

	public function finalPayment()
	{
		session_start();

		unset($_SESSION['totalAssinatura'], $_SESSION['products'], $_SESSION['frete']);

		$this->index();
	}

	public function login()
	{
		$this->render('login');		
	}

	public function qrcode()
	{
		$this->render('qrcode');		
	}

	public function produtores()
	{
		$this->render('produtores');		
	}

	public function sobre()
	{
		$this->render('sobre');
	}

	public function contato()
	{
		$this->render('contato');
	}

	public function privatePolicy()
	{
		$this->render('private-policy');
	}

	public function clubeImpacto()
	{
		$CarrinhosProdutos = Container::getModel('CarrinhosProdutos');
		$this->view->cartQuantity = $CarrinhosProdutos->getQuantityCart();

		$this->render('clube-de-impacto');
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

	public function redirectComprar()
	{
		$this->view->assinar = isset($_POST['radio']) ? $_POST['radio'] : '';

		if($this->view->assinar == 'moido')
		{
			echo "<script>window.location.href='https://pag.ae/7WvAvdNr1'</script>";

		} else
		{
			echo "<script>window.location.href='https://pag.ae/7WvAvYSYH'</script>";
		}
	}

	public function cadastro()
	{
		$this->view->erroCadastro = false;
		$this->render('cadastro');
	}

	public function registrar()
	{
		$data =  filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);
		
		$usuario = Container::getModel('Usuario');

		$usuario->__set('nome', $data['senderName']);
		$usuario->__set('email', $data['senderEmail']);
		$usuario->__set('senha', md5($data['passwd']));
		$usuario->__set('cpf', $data['senderCPF']);
		$usuario->__set('data_nasc', $data['creditCardHolderBirthDate']);
		$usuario->__set('phoneAreaCode', $data['senderAreaCode']);
		$usuario->__set('phone', $data['senderPhone']);
		$usuario->__set('addressStreet', $data['billingAddressStreet']);
		$usuario->__set('addressNumber', $data['billingAddressNumber']);
		$usuario->__set('addressComplement', $data['billingAddressComplement']);
		$usuario->__set('addressDistrict', $data['billingAddressDistrict']);
		$usuario->__set('addressPostalCode', $data['billingAddressPostalCode']);
		$usuario->__set('addressCity', $data['billingAddressCity']);
		$usuario->__set('addressState', $data['billingAddressState']);
		$usuario->__set('addressCountry', $data['billingAddressCountry']);
		
		if ($usuario->validarCadastro() && count($usuario->getUsuarioPorEmail()) == 0) {

			$usuario->salvar();

			echo json_encode(1);

		} else {

			echo json_encode(2);
		}
	}
}