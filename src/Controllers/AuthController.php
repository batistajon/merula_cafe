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
        $Payment = Container::getModel('Payment');
        $Payment->sessionCheckout();
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

        $checkout = $Payment->__get('retorna');

        echo $checkout;
		//header('Content-Type: application/json');
		//echo json_encode($checkout);
    }

    public function createPlan()
    {
        $preApproval = Container::getModel('PreApproval');

        $data =  filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $preApproval->__set('charge','MANUAL');
        $preApproval->__set('reference','Teste');
		$preApproval->__set('name','Clube de Impacto');
		$preApproval->__set('details','Clube de assinatura de cafes especiais - Merula Cafes Especiais'); //TODO colocar acentos
		$preApproval->__set('amountPerPayment', $data['amount']);
        $preApproval->__set('maxAmountPerPayment', '500.00');
        $preApproval->__set('cancelURL','https://www.cafemerula.com.br/clube-de-impacto/cancel/');
		$preApproval->__set('maxTotalAmount','500.00');
		$preApproval->__set('maxAmountPerPeriod','');
		$preApproval->__set('maxPaymentsPerPeriod','');
		$preApproval->__set('period','MONTHLY');
		$preApproval->__set('initialDate','');
        $preApproval->__set('finalDate','');

        $preApproval->createPlanPreApproval();

        $codePlan = $preApproval->__get('retorna');

        header('Content-Type: application/json');
        echo json_encode($codePlan);
    }

    public function subscribe()
    {
        $preApproval = Container::getModel('PreApproval');

        $data =  filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $preApproval->__set('plan', $data["codePlan"]);
        $preApproval->__set('paymentMethod', $data["paymentMethod"]);
        $preApproval->__set('receiverEmail', $data["receiverEmail"]);
        $preApproval->__set('extraAmount', $data["extraAmount"]);
        $preApproval->__set('itemId1', $data["itemId1"]);
        $preApproval->__set('reference', $data["reference"]);
        $preApproval->__set('itemDescription1', $data["itemDescription1"]);
        $preApproval->__set('amountPerPayment', $data['amount']);
        $preApproval->__set('itemAmount1', $data["itemAmount1"]);
        $preApproval->__set('itemQuantity1', $data["itemQuantity1"]);
        $preApproval->__set('reference', $data["reference"]);
        $preApproval->__set('senderName', $data["senderName"]);
        $preApproval->__set('senderCPF', $data["senderCPF"]);
        $preApproval->__set('senderAreaCode', $data["senderAreaCode"]);
        $preApproval->__set('senderPhone', $data["senderPhone"]);
        $preApproval->__set('senderEmail', $data["senderEmail"]);
        $preApproval->__set('senderHash', $data["hashCartao"]);
        $preApproval->__set('shippingAddressRequired', $data["shippingAddressRequired"]);
        $preApproval->__set('shippingAddressStreet', $data["shippingAddressStreet"]);
        $preApproval->__set('shippingAddressNumber', $data["shippingAddressNumber"]);
        $preApproval->__set('shippingAddressComplement', $data["shippingAddressComplement"]);
        $preApproval->__set('shippingAddressDistrict', $data["shippingAddressDistrict"]);
        $preApproval->__set('shippingAddressPostalCode', $data["shippingAddressPostalCode"]);
        $preApproval->__set('shippingAddressCity', $data["shippingAddressCity"]);
        $preApproval->__set('shippingAddressState', $data["shippingAddressState"]);
        $preApproval->__set('shippingAddressCountry', $data["shippingAddressCountry"]);
        $preApproval->__set('shippingType', $data["shippingType"]);
        $preApproval->__set('shippingCost', $data["shippingCost"]);
        $preApproval->__set('creditCardToken', $data['tokenCartao']);
        $preApproval->__set('installmentQuantity', $data["qntParcelas"]);
        $preApproval->__set('installmentValue', $data["valorParcelas"]);
        $preApproval->__set('noInterestInstallmentQuantity', $data["noIntInstalQuantity"]);
        $preApproval->__set('creditCardHolderName', $data["creditCardHolderName"]);
        $preApproval->__set('creditCardHolderCPF', $data["creditCardHolderCPF"]);
        $preApproval->__set('creditCardHolderBirthDate', $data["creditCardHolderBirthDate"]);
        $preApproval->__set('creditCardHolderAreaCode', $data["senderAreaCode"]);
        $preApproval->__set('creditCardHolderPhone', $data["senderPhone"]);
        $preApproval->__set('billingAddressStreet', $data["billingAddressStreet"]);
        $preApproval->__set('billingAddressNumber', $data["billingAddressNumber"]);
        $preApproval->__set('billingAddressComplement', $data["billingAddressComplement"]);
        $preApproval->__set('billingAddressDistrict', $data["billingAddressDistrict"]);
        $preApproval->__set('billingAddressPostalCode', $data["billingAddressPostalCode"]);
        $preApproval->__set('billingAddressCity', $data["billingAddressCity"]);
        $preApproval->__set('billingAddressState', $data["billingAddressState"]);
        $preApproval->__set('billingAddressCountry', $data["billingAddressCountry"]);

        $preApproval->adesaoPlan();

        $adesaoCode = $preApproval->__get('retorna');

        //header('Content-Type: application/json');
        echo json_encode($adesaoCode);

        //echo json_encode($data);
    }

    public function PrecosEPrazosCorreios()
    {
        $Correios = Container::getModel('Correios');

        $data =  filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

        $Correios->__set('cepOrigem', '21555300');
        $Correios->__set('cepDestino',$data['shippingAddressPostalCode']);
        $Correios->__set('peso', '0.350');
        $Correios->__set('formato', '1');
        $Correios->__set('comprimento', '18');
        $Correios->__set('altura', '10');
        $Correios->__set('largura', '27');
        $Correios->__set('maoPropria', 'n');
        $Correios->__set('valorDeclarado', '0');
        $Correios->__set('avisoRecebimento', 'n');

        if($data['shippingType'] == '1') {
            $Correios->__set('codigo', '04510');
        } else {
            $Correios->__set('codigo', '04014');
        }
        
        $Correios->__set('diametro', '0');

        $Correios->pesquisaPrecoPrazo();

        $frete = $Correios->__get('retorna')->cServico->Valor;

        echo $frete;
    }
}