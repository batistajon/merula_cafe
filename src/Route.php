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

		$routes['pagamento'] = [
			'route'      => '/pagamento', 
			'controller' => 'authController', 
			'action'     => 'pagamento'
		];

		$routes['paymentCreditCard'] = [
			'route'      => '/payment', 
			'controller' => 'indexController', 
			'action'     => 'paymentCreditCard'
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

		$routes['pagseguro'] = [
			'route'      => '/notificacao-pagseguro', 
			'controller' => 'authController', 
			'action'     => 'notificacaoPagseguro'
		];

		$routes['status'] = [
			'route'      => '/pagseguro-status', 
			'controller' => 'authController', 
			'action'     => 'getStatus'
		];

		$routes['checkout'] = [
			'route'      => '/checkout', 
			'controller' => 'authController', 
			'action'     => 'checkout'
		];

		$routes['subscribe'] = [
			'route'      => '/subscribe', 
			'controller' => 'authController', 
			'action'     => 'subscribe'
		];

		$routes['create-plan'] = [
			'route'      => '/create-plan', 
			'controller' => 'authController', 
			'action'     => 'createPlan'
		];
		
		$routes['precos-e-prazos-correios'] = [
			'route'      => '/precos-e-prazos-correios', 
			'controller' => 'authController', 
			'action'     => 'PrecosEPrazosCorreios'
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
			'controller' => 'AuthController', 
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

		$this->setRoutes($routes);
	}
}