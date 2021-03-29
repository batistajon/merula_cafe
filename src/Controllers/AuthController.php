<?php

namespace Src\Controllers;

use League\OAuth2\Client\Provider\Facebook;
use Router\Controller\Action;
use Router\Model\Container;

/**
 * AuthController - Controla as autenticacoes dos usuarios
 */
class AuthController extends Action
{
    public function autenticar()
    {
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        print_r($data);
        /* $Usuario = Container::getModel('Usuario');

        $Usuario->__set('email', $data['emailLogin']);
        $Usuario->__set('senha', md5($data['senhaLogin']));
        $Usuario->auth();

        if($Usuario->__get('id') != '' && $Usuario->__get('nome') != '') {
            
            $Usuario->startSessionUser();

        } else {

            echo json_encode('erro ao efetuar login php');
        } */
    }

    public function facebook()
    {
        /* require dirname(__DIR__, 1) . "/Config.php";
        $facebook = new Facebook(FACEBOOK_LOGIN);

        $error = filter_input(INPUT_GET, 'error', FILTER_DEFAULT);
        $code = filter_input(INPUT_GET, 'code', FILTER_DEFAULT); */

        echo 'ola';
        //print_r($code);

        /* if(!$error && !$code) {
            $auth_url = $facebook->getAuthorizationUrl(["scope" => "email"]);
            header("Location: {$auth_url}");
            return;
        }

        if($error) {
            header('Location: /login');
        }

        if($code && empty(@$_SESSION['facebook_auth'])) {

             try {
                $token = $facebook->getAccessToken('authorization_code', ['code' => $code]);
                $_SESSION['facebook_auth'] = serialize($facebook->getResourceOwner($token));

            } catch(\Exception $exception) {

                header('Location: /login');
            }
        } */
        /* $inputGet = filter_input(INPUT_GET, 'error', FILTER_DEFAULT);
        var_dump($inputGet); */
    }

    public function sair()
    {
        session_start();
        session_destroy();

        header('Location: /');
    }

    public function dataSession()
    {   
        @session_start();

        print_r(json_encode($_SESSION));     
    }

    public function addCart()
    {   
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $id = 0;

        if($data['metodo_preparo'] == 'graos' && $data['valorVenda'] == '29') {
            $id = 1;
        }

        if($data['metodo_preparo'] == 'moido' && $data['valorVenda'] == '29') {
            $id = 4;
        }

        if($data['metodo_preparo'] == 'graos' && $data['valorVenda'] == '25') {
            $id = 5;
        }

        if($data['metodo_preparo'] == 'moido' && $data['valorVenda'] == '25') {
            $id = 2;
        }

        if($data['metodo_preparo'] == 'prensa' && $data['valorVenda'] == '25') {
            $id = 2;
        }

        if($data['metodo_preparo'] == 'prensa' && $data['valorVenda'] == '25') {
            $id = 2;
        }

        $Product = Container::getModel('Product');
        $Product->__set('id', $id);
        $cartProducts = $Product->getProductById();

        $CarrinhosProdutos = Container::getModel('CarrinhosProdutos');
        $CarrinhosProdutos->__set("produto_id", $cartProducts['id']);
        $CarrinhosProdutos->__set("nome_produto", $cartProducts['nome_produto']);
        $CarrinhosProdutos->__set("valor_venda", $cartProducts['valor_venda']);
        $CarrinhosProdutos->__set("metodo_preparo", $cartProducts['metodo_preparo']);
        $CarrinhosProdutos->__set("qnt_produto", $data['qnt_pacotes']);
        $CarrinhosProdutos->addProducts();
        
        print_r(json_encode($_SESSION));
    }

    public function cart()
    {
        $CarrinhosProdutos = Container::getModel('CarrinhosProdutos');
        $this->view->lista = $CarrinhosProdutos->listProducts();

        $this->render('carrinho');
    }

    public function updateProducts()
    {
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $CarrinhosProdutos = Container::getModel('CarrinhosProdutos');

        if($data['action'] == 'updatePlus') {

            $CarrinhosProdutos->updateProductsMore();

        } else if($data['action'] == 'updateLess') {

            $CarrinhosProdutos->updateProductsLess();

        } else if($data['action'] == 'deleteProduct') {

            $CarrinhosProdutos->clearProducts($data);
        }
    }

    public function pagamento()
    {
        $Payment = Container::getModel('Payment');
        $Payment->sessionCheckout();
    }

	public function payment()
	{   
        @session_start();
        
        if(!@$_SESSION['user']) {
            require dirname(__DIR__, 1) . "/Config.php";
            echo "<script>window.location.href='".APP['root']."'</script>";

        } else {
            $CarrinhosProdutos = Container::getModel('CarrinhosProdutos');
            $this->view->listProducts = $CarrinhosProdutos->listProducts();

            $this->view->reference = $_SESSION['user']['id'];
            $this->view->nome = $_SESSION['user']['nome'];
            $this->view->primeiroNome = \ucfirst(explode(' ', $this->view->nome)[0]);
            $this->view->dataNasc = $_SESSION['user']['data_nasc'];
            $this->view->cpf = $_SESSION['user']['cpf'];
            $this->view->email = $_SESSION['user']['email'];
            $this->view->ddd = $_SESSION['user']['phoneAreaCode'];
            $this->view->phone = $_SESSION['user']['phone'];
            $this->view->cep = $_SESSION['user']['addressPostalCode'];
            $this->view->number = $_SESSION['user']['addressNumber'];
            $this->view->complement = $_SESSION['user']['addressComplement'];
            $this->view->addressStreet = $_SESSION['user']['addressStreet'];
            $this->view->addressDistrict = $_SESSION['user']['addressDistrict'];
            $this->view->addressCity = $_SESSION['user']['addressCity'];
            $this->view->addressState = $_SESSION['user']['addressState'];
            $this->view->addressCountry = $_SESSION['user']['addressCountry'];
            
            $this->view->freteCarrinho = $_SESSION['frete']['shippingCost'];
            $this->view->listSubscribePayment = $CarrinhosProdutos->listSubscribePayment();
            $this->view->clube = 0;
            
            $this->render('payment');
        }        
    }

    public function getCartData()
    {
        $CarrinhosProdutos = Container::getModel('CarrinhosProdutos');
        print_r(json_encode($CarrinhosProdutos->getSubtotalCart()));
    }
    
    public function showCart()
    {
        @session_start(); 

        if(!@$_SESSION['products']) {
            require dirname(__DIR__, 1) . "/Config.php";
            echo "<script>window.location.href='".APP['root']."'</script>";
            
        } else {

            $CarrinhosProdutos = Container::getModel('CarrinhosProdutos');
            $this->view->listProducts = $CarrinhosProdutos->listProducts();
            $this->view->subtotal = str_replace('.', ',', number_format($CarrinhosProdutos->getSubtotalCart(), 2));
            $this->view->listSubscribe = $CarrinhosProdutos->listSubscribe();
            
            $this->render('carrinho');
        }
    }

    public function checkout()
    {
        $data =  filter_input_array(INPUT_POST, FILTER_DEFAULT);

        //print_r(json_encode($data));

        @session_start();

        $listaProdutosCarrinho = $_SESSION['products'];
        
        $Payment = Container::getModel('Payment');
        $Payment->__set('listaProdutosCarrinho', $listaProdutosCarrinho);

        $Payment->__set('paymentMethod', $data["paymentMethod"]);
        $Payment->__set('receiverEmail', $data["receiverEmail"]);
        $Payment->__set('extraAmount', $data["extraAmount"]);
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
        $Payment->__set('shippingCost', str_replace(',', '.', $data["shippingCost"]));
        $Payment->__set('creditCardToken', $data['tokenCartao']);
        $Payment->__set('installmentQuantity', $data["qntParcelas"]);

        if($data["valorParcelas"]) {

            $Payment->__set('installmentValue', explode(' ', str_replace(',','.',$data["valorParcelas"]))[1]);
            
        } else {

            $Payment->__set('installmentValue', 0);
        }   

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

        echo(json_encode($checkout));
    }

    public function createPlan()
    {
        @session_start();

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
        $preApproval->__set('trialPeriodDuration', 30);

        $preApproval->createPlanPreApproval();

        $codePlan = $preApproval->__get('retorna');

        print_r(json_encode($codePlan));
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
        $preApproval->__set('shippingCost', str_replace(',', '.', $data["shippingCost"]));
        $preApproval->__set('creditCardToken', $data['tokenCartao']);
        $preApproval->__set('installmentQuantity', $data["qntParcelas"]);
        $preApproval->__set('installmentValue', explode(' ', str_replace(',','.',$data["valorParcelas"]))[1]);
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

        header('Content-Type: application/json');
        echo json_encode($adesaoCode);
    }

    public function reviewTotalTransactions()
    {
        $Payment = Container::getModel('Payment');
        $Payment->getReviewTotalTransactions();
    }

    public function reviewAdvancedTransactions()
    {
        $Payment = Container::getModel('Payment');
        $Payment->__set('reviewAdvancedTransactions', $_GET['code']);
        $Payment->getReviewAdvancedTransactions();
    }

    public function notificationPagseguro()
    {
        $Payment = Container::getModel('Payment');
        $Payment->__set('notificationCode', 'B680E07AAD73AD735CD664A71FB728445CEE'/* $_POST['notificationCode'] */);
        $Payment->paymentNotifications();

        $EmailNotification = Container::getModel('EmailNotification');
        $EmailNotification->__set('dadosEmail', $Payment->__get('notificationEmail'));

        $dadosEmail = $EmailNotification->__get('dadosEmail');

        /* echo '<pre>';
        print_r($dadosEmail); */

        if($dadosEmail->status == 3) {

            $EmailNotification->sendSuccessPayment();
        }

        if($dadosEmail->status == 7) {

            $EmailNotification->sendCancelPayment(); 
        }
    }

    public function PrecosEPrazosCorreiosCart()
    {
        $data =  filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

        session_start();

        $dadosFrete = $_SESSION['products'];

        $totalComprimento = 19;
        $totalAltura = 10;
        $totalLargura = 27;
        $totalPeso = 0.350;
        $doubleFrete = false;

        foreach($dadosFrete as $key => $frete) {

            if($frete['id'] == 1 && $frete['quantity'] >= 3 || $frete['id'] == 2 && $frete['quantity'] >= 3) {

                $totalComprimento = ($totalComprimento * $frete['quantity'])/2;
                $totalAltura = ($totalAltura * $frete['quantity'])/2;
                $totalLargura = ($totalLargura * $frete['quantity'])/2;
                $totalPeso = ($totalPeso * $frete['quantity']);

            } elseif($frete['id'] == 1 && $frete['quantity'] > 9) {

                $totalComprimento = 45;
                $totalAltura = 30;
                $totalLargura = 30;
                $totalPeso = ($totalPeso * $frete['quantity']);

            } elseif($frete['id'] == 1 && $frete['quantity'] = 1 || $frete['id'] == 2 && $frete['quantity'] = 1) {

                $totalComprimento = ($totalComprimento * $frete['quantity']);
                $totalAltura = ($totalAltura * $frete['quantity']);
                $totalLargura = ($totalLargura * $frete['quantity']);
                $totalPeso = ($totalPeso * $frete['quantity']);
            }   
        }

        $totalVolume = $totalComprimento + $totalAltura + $totalLargura;
        $quebra = 3;
        $totalVolume = $totalVolume / $quebra;

        $Correios = Container::getModel('Correios');

        $Correios->__set('cepOrigem', '21555300');
        $Correios->__set('cepDestino', $data['cep']);
        $Correios->__set('peso', $totalPeso);
        $Correios->__set('formato', '1');
        $Correios->__set('comprimento', $totalVolume);
        $Correios->__set('altura', $totalVolume);
        $Correios->__set('largura', $totalVolume);
        $Correios->__set('maoPropria', 'n');
        $Correios->__set('valorDeclarado', '0');
        $Correios->__set('avisoRecebimento', 'n');

        if($data['shippingType'] == 1) {
            $Correios->__set('codigo', '04510');

        } else if($data['shippingType'] == 2) {
            $Correios->__set('codigo', '04014');
        } 
        
        $Correios->__set('diametro', '0');

        $Correios->pesquisaPrecoPrazo();
        
        $dadosGeraisFrete = $Correios->__get('retorna')->cServico;

        header('Content-Type: application/json');
        echo json_encode($dadosGeraisFrete);
    }

    public function PrecosEPrazosCorreiosAssinatura()
    {
        $data =  filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS);

        //print_r(json_encode($data));

        $totalComprimento = 19;
        $totalAltura = 10;
        $totalLargura = 27;
        $totalPeso = 0.350;
        $doubleFrete = false;

        

        if($data['quantity'] >= 3) {

            $totalComprimento = ($totalComprimento * $data['quantity'])/2;
            $totalAltura = ($totalAltura * $data['quantity'])/2;
            $totalLargura = ($totalLargura * $data['quantity'])/2;
            $totalPeso = ($totalPeso * $data['quantity']);

        } elseif($data['quantity'] <= 2) {

            $totalComprimento = ($totalComprimento * $data['quantity']);
            $totalAltura = ($totalAltura * $data['quantity']);
            $totalLargura = ($totalLargura * $data['quantity']);
            $totalPeso = ($totalPeso * $data['quantity']);
        }   
        
        $totalVolume = $totalComprimento + $totalAltura + $totalLargura;
        $quebra = 3;
        $totalVolume = $totalVolume / $quebra;

        $Correios = Container::getModel('Correios');

        $Correios->__set('cepOrigem', '21555300');
        $Correios->__set('cepDestino', $data['cep']);
        $Correios->__set('peso', $totalPeso);
        $Correios->__set('formato', '1');
        $Correios->__set('comprimento', $totalVolume);
        $Correios->__set('altura', $totalVolume);
        $Correios->__set('largura', $totalVolume);
        $Correios->__set('maoPropria', 'n');
        $Correios->__set('valorDeclarado', '0');
        $Correios->__set('avisoRecebimento', 'n');

        if($data['shippingType'] == 1) {
            $Correios->__set('codigo', '04510');

        } else if($data['shippingType'] == 2) {
            $Correios->__set('codigo', '04014');
        } 
        
        $Correios->__set('diametro', '0');

        $Correios->pesquisaPrecoPrazo();
        
        $dadosGeraisFrete = $Correios->__get('retorna')->cServico;

        header('Content-Type: application/json');
        echo json_encode($dadosGeraisFrete);
    }

    public function PrecosEPrazosCorreios()
    {
        $data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        $Correios = Container::getModel('Correios');
        $Correios->__set('data', $data);

        //print_r(json_encode($data));

        $dadosFrete = $_SESSION['products'];

        $totalComprimento = 0;
        $totalAltura = 0;
        $totalLargura = 0;
        $totalPeso = 0;
        $doubleFrete = false;

        foreach($dadosFrete as $key => $frete) {

            if($frete['produto_id'] == 1 && $frete['qnt_produto'] > 1 || $frete['produto_id'] == 2) {

                $totalComprimento += ($frete['comprimento_frete'] * $frete['qnt_produto'])/2;
                $totalAltura += ($frete['altura_frete'] * $frete['qnt_produto'])/2;
                $totalLargura += ($frete['largura_frete'] * $frete['qnt_produto'])/2;
                $totalPeso += ($frete['peso_frete'] * $frete['qnt_produto']);

            } elseif($frete['produto_id'] == 1 && $frete['qnt_produto'] > 9) {

                $totalComprimento = 45;
                $totalAltura = 30;
                $totalLargura = 30;
                $totalPeso += ($frete['peso_frete'] * $frete['qnt_produto']);
            } else {

                $totalComprimento += ($frete['comprimento_frete'] * $frete['qnt_produto']);
                $totalAltura += ($frete['altura_frete'] * $frete['qnt_produto']);
                $totalLargura += ($frete['largura_frete'] * $frete['qnt_produto']);
                $totalPeso += ($frete['peso_frete'] * $frete['qnt_produto']);
            }   
        }

        if($totalComprimento > 70) {
            $totalComprimento = 80;
            $doubleFrete = true;
        }

        if($totalAltura > 70) {
            $totalAltura = 40;
            $doubleFrete = true;
        }

        if($totalLargura > 70) {
            $totalLargura = 80;
            $doubleFrete = true;
        }

        $totalVolume = ($totalComprimento + $totalAltura + $totalLargura)/3;

        $Correios = Container::getModel('Correios');
        $Correios->__set('data', $data);
        $Correios->__set('cepOrigem', '21555300');
        $Correios->__set('cepDestino', $data['shippingAddressPostalCode']);
        $Correios->__set('peso', $totalPeso);
        $Correios->__set('formato', '1');
        $Correios->__set('comprimento', $totalVolume);
        $Correios->__set('altura', $totalVolume);
        $Correios->__set('largura', $totalVolume);
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
        
        $dadosGeraisFrete = $Correios->__get('retorna')->cServico;

        header('Content-Type: application/json');
        echo json_encode($dadosGeraisFrete);

        /* if($doubleFrete) {

            echo $frete * 2;
        } else {

            echo $frete;
        } */
    }
}