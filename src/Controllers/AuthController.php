<?php

namespace Src\Controllers;

use Router\Controller\Action;
use Router\Model\Container;

/**
 * AuthController - Controla as autenticacoes dos usuarios
 */
class AuthController extends Action
{
    private $url;

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

    public function pagamento()
    {
        require_once dirname(__DIR__, 1). '/Config.php';

        $this->url = PAGSEGURO['url_pag'] . "sessions?email=". PAGSEGURO['email'] ."&token=". PAGSEGURO['token'];
        //echo $this->url;

        $curl = curl_init($this->url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $retorno = curl_exec($curl);

        curl_close($curl);

        $xml = simplexml_load_string($retorno);
        echo json_encode($xml);
    }

    public function checkout()
    {
        $Payment = Container::getModel('Payment');

        $data =  filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
        $Payment->__set('paymentMethod', $data["paymentMethod"]);
        $Payment->__set('receiverEmail', $data["receiverEmail"]);
        $Payment->__set('extraAmount', $data["extraAmount"]);
        $Payment->__set('itemId1', $data["itemId1"]);
        $Payment->__set('itemDescription1', $data["itemDescription1"]);
        $Payment->__set('itemAmount1', $data["itemAmount1"]);
        $Payment->__set('itemQuantity1', $data["itemQuantity1"]);
        $Payment->__set('reference', $data["reference"]);
        $Payment->__set('senderName', $data["senderName"]);
        $Payment->__set('senderCPF', $data["senderCPF"]);
        $Payment->__set('senderAreaCode', $data["senderAreaCode"]);
        $Payment->__set('senderPhone', $data["senderPhone"]);
        $Payment->__set('senderEmail', $data["senderEmail"]);
        $Payment->__set('senderHash', $data["hashCartao"]);
        $Payment->__set('shippingAddressRequired', $data["shippingAddressRequired"]);
        $Payment->__set('shippingAddressStreet', $data["shippingAddressStreet"]);
        $Payment->__set('shippingAddressNumber', $data["shippingAddressNumber"]);
        $Payment->__set('shippingAddressComplement', $data["shippingAddressComplement"]);
        $Payment->__set('shippingAddressDistrict', $data["shippingAddressDistrict"]);
        $Payment->__set('shippingAddressPostalCode', $data["shippingAddressPostalCode"]);
        $Payment->__set('shippingAddressCity', $data["shippingAddressCity"]);
        $Payment->__set('shippingAddressState', $data["shippingAddressState"]);
        $Payment->__set('shippingAddressCountry', $data["shippingAddressCountry"]);
        $Payment->__set('shippingType', $data["shippingType"]);
        $Payment->__set('shippingCost', $data["shippingCost"]);
        $Payment->__set('creditCardToken', $data['tokenCartao']);
        $Payment->__set('installmentQuantity', $data["qntParcelas"]);
        $Payment->__set('installmentValue', $data["valorParcelas"]);
        $Payment->__set('noInterestInstallmentQuantity', $data["noIntInstalQuantity"]);
        $Payment->__set('creditCardHolderName', $data["creditCardHolderName"]);
        $Payment->__set('creditCardHolderCPF', $data["creditCardHolderCPF"]);
        $Payment->__set('creditCardHolderBirthDate', $data["creditCardHolderBirthDate"]);
        $Payment->__set('creditCardHolderAreaCode', $data["senderAreaCode"]);
        $Payment->__set('creditCardHolderPhone', $data["senderPhone"]);
        $Payment->__set('billingAddressStreet', $data["billingAddressStreet"]);
        $Payment->__set('billingAddressNumber', $data["billingAddressNumber"]);
        $Payment->__set('billingAddressComplement', $data["billingAddressComplement"]);
        $Payment->__set('billingAddressDistrict', $data["billingAddressDistrict"]);
        $Payment->__set('billingAddressPostalCode', $data["billingAddressPostalCode"]);
        $Payment->__set('billingAddressCity', $data["billingAddressCity"]);
        $Payment->__set('billingAddressState', $data["billingAddressState"]);
        $Payment->__set('billingAddressCountry', $data["billingAddressCountry"]);

        $Payment->executeCheckout();

		header('Content-Type: application/json');
		echo json_encode($Payment->__get('retorna'));
    }
}