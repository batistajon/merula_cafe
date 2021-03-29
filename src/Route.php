<?php

namespace Src;

use Router\Init\Bootstrap;

class Route extends Bootstrap {

	protected function initRoutes() {

		$routes['home'] = [
			'route'      => '/', 
			'controller' => 'indexController', 
			'action'     => 'index'
		];

		$routes['sobre'] = [
			'route'      => '/sobre', 
			'controller' => 'indexController', 
			'action'     => 'sobre'
		];

		$routes['private-policy'] = [
			'route'      => '/private-policy', 
			'controller' => 'indexController', 
			'action'     => 'privatePolicy'
		];

		$routes['pagamento'] = [
			'route'      => '/pagamento', 
			'controller' => 'authController', 
			'action'     => 'pagamento'
		];

		$routes['carrinho'] = [
			'route'      => '/carrinho', 
			'controller' => 'authController', 
			'action'     => 'showCart'
		];

		$routes['login'] = [
			'route'      => '/login', 
			'controller' => 'indexController', 
			'action'     => 'login'
		];

		$routes['facebook'] = [
			'route'      => '/facebook', 
			'controller' => 'authController', 
			'action'     => 'facebook'
		];

		$routes['google'] = [
			'route'      => '/google', 
			'controller' => 'authController', 
			'action'     => 'google'
		];

		$routes['login-validate'] = [
			'route'      => '/login-validate', 
			'controller' => 'indexController', 
			'action'     => 'loginValidate'
		];

		$routes['payment'] = [
			'route'      => '/payment', 
			'controller' => 'authController', 
			'action'     => 'payment'
		];

		$routes['contato'] = [
			'route'      => '/contato', 
			'controller' => 'indexController', 
			'action'     => 'contato'
		];

		$routes['clube-de-impacto'] = [
			'route'      => '/clube-de-impacto', 
			'controller' => 'indexController', 
			'action'     => 'clubeImpacto'
		];

		$routes['assinar'] = [
			'route'      => '/assinar', 
			'controller' => 'indexController', 
			'action'     => 'redirectAssinar'
		];

		$routes['email'] = [
			'route'      => '/email', 
			'controller' => 'indexController', 
			'action'     => 'email'
		];

		$routes['comprar'] = [
			'route'      => '/comprar', 
			'controller' => 'indexController', 
			'action'     => 'redirectComprar'
		];

		$routes['notificacao-pagseguro'] = [
			'route'      => '/notificacao-pagseguro', 
			'controller' => 'authController', 
			'action'     => 'notificationPagseguro'
		];

		$routes['review-transactions'] = [
			'route'      => '/review-total-transactions', 
			'controller' => 'authController', 
			'action'     => 'reviewTotalTransactions'
		];

		$routes['review-advanced-transactions'] = [
			'route'      => '/review-advanced-transactions', 
			'controller' => 'authController', 
			'action'     => 'reviewAdvancedTransactions'
		];

		$routes['add-cart'] = [
			'route'      => '/add-cart', 
			'controller' => 'authController', 
			'action'     => 'addCart'
		];

		$routes['update-products'] = [
			'route'      => '/update-products', 
			'controller' => 'authController', 
			'action'     => 'updateProducts'
		];

		$routes['cart'] = [
			'route'      => '/cart', 
			'controller' => 'authController', 
			'action'     => 'cart'
		];

		$routes['data-session'] = [
			'route'      => '/data-session', 
			'controller' => 'authController', 
			'action'     => 'dataSession'
		];

		$routes['get-cart-data'] = [
			'route'      => '/get-cart-data', 
			'controller' => 'authController', 
			'action'     => 'getCartData'
		];

		$routes['clear-cart'] = [
			'route'      => '/clear-cart', 
			'controller' => 'authController', 
			'action'     => 'clearCart'
		];

		$routes['checkout'] = [
			'route'      => '/checkout', 
			'controller' => 'authController', 
			'action'     => 'checkout'
		];

		$routes['final-payment'] = [
			'route'      => '/final-payment', 
			'controller' => 'indexController', 
			'action'     => 'finalPayment'
		];

		$routes['subscribe'] = [
			'route'      => '/subscribe', 
			'controller' => 'authController', 
			'action'     => 'subscribe'
		];

		$routes['get-total-assinatura'] = [
			'route'      => '/get-total-assinatura', 
			'controller' => 'authController', 
			'action'     => 'getTotalAssinatura'
		];

		$routes['create-plan'] = [
			'route'      => '/create-plan', 
			'controller' => 'authController', 
			'action'     => 'createPlan'
		];
		
		$routes['precos-e-prazos-correios-assinatura'] = [
			'route'      => '/precos-e-prazos-correios-assinatura', 
			'controller' => 'authController', 
			'action'     => 'PrecosEPrazosCorreiosAssinatura'
		];

		$routes['precos-e-prazos-correios-cart'] = [
			'route'      => '/precos-e-prazos-correios-cart', 
			'controller' => 'authController', 
			'action'     => 'PrecosEPrazosCorreiosCart'
		];

		//TODO: fazer limeza das rotas corretamente

		$routes['cadastro'] = [
			'route'      => '/cadastro', 
			'controller' => 'indexController', 
			'action'     => 'cadastro'
		];

		$routes['registrar'] = [
			'route'      => '/registrar', 
			'controller' => 'indexController', 
			'action'     => 'registrar'
		];

		$routes['autenticar'] = [
			'route'      => '/autenticar', 
			'controller' => 'AuthController', 
			'action'     => 'autenticar'
		];

		$routes['admin'] = [
			'route'      => '/admin', 
			'controller' => 'AdminController', 
			'action'     => 'admin'
		];

		$routes['sair'] = [
			'route'      => '/sair', 
			'controller' => 'authController', 
			'action'     => 'sair'
		];

		$routes['comentario'] = [
			'route'      => '/comentario', 
			'controller' => 'AdminController', 
			'action'     => 'comentario'
		];

		$routes['beneficio'] = [
			'route'      => '/beneficio', 
			'controller' => 'AdminController', 
			'action'     => 'beneficio'
		];

		$routes['acao'] = [
			'route'      => '/acao',	
			'controller' => 'AdminController', 
			'action'     => 'acao'
		];
		
		$routes['delete_comentario'] = [
			'route'      => '/delete_comentario', 
			'controller' => 'AdminController', 
			'action'     => 'deleteComentario'
		];

		$routes['qrcode'] = [
			'route'      => '/qrcode', 
			'controller' => 'IndexController', 
			'action'     => 'qrcode'
		];

		$routes['produtores'] = [
			'route'      => '/produtores', 
			'controller' => 'IndexController', 
			'action'     => 'produtores'
		];

		$this->setRoutes($routes);
	}
}