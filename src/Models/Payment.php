<?php

namespace Src\Models;

use PDO;
use Router\Model\Model;

class Payment extends Model
{
	private $email;       
	private $token;
	private $url_retorno;
	private $url;
	private $email_token;
	private $moeda;
	private $buildQuery;
	private $retorna;
	private $paymentMode;
	private $paymentMethod;
	private $receiverEmail;
	private $extraAmount;
	private $itemId1;
	private $itemDescription1;
	private $itemAmount1;
	private $itemQuantity1;
	private $reference;
	private $senderName;
	private $senderCPF;
	private $senderEmail;
	private $senderHash;
	private $shippingAddressRequired;
	private $shippingAddressStreet;
	private $shippingAddressNumber;
	private $shippingAddressComplement;
	private $shippingAddressDistrict;
	private $shippingAddressPostalCode;
	private $shippingAddressCity;
	private $shippingAddressState;
	private $shippingAddressCountry;
	private $shippingType;
	private $shippingCost;
	private $installmentQuantity;
	private $installmentValue;
	private $noInterestInstallmentQuantity;
	private $creditCardHolderName;
	private $creditCardHolderCPF;
	private $creditCardHolderBirthDate;
	private $creditCardHolderAreaCode;
	private $creditCardHolderPhone;
	private $billingAddressStreet;
	private $billingAddressNumber;
	private $billingAddressComplement;
	private $billingAddressDistrict;
	private $billingAddressPostalCode;
	private $billingAddressCity;
	private $billingAddressState;
	private $billingAddressCountry;
	private $creditCardToken;
	private $senderAreaCode;
	private $senderPhone;
	private $savedb;

	
	public function __construct()
	{
		require_once dirname(__DIR__ , 1) . "/Config.php";

		$this->email            = PAGSEGURO['email'];
		$this->token            = PAGSEGURO['token'];
		$this->url_retorno      = PAGSEGURO['url_notificacao'];
		$this->url              = PAGSEGURO['url_pag'];
		$this->moeda            = PAGSEGURO['moeda'];
		$this->paymentMode      = "default";
	}

	public function __get($var)
    {
        return $this->$var;
    }

    public function __set($var, $value)
    {
        $this->$var = $value;
	}
	
	public function sessionCheckout()
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
		
	public function executeCheckout()
	{ 	 
		$DadosArray["email"] = $this->email;
		$DadosArray["token"] = $this->token;
		$DadosArray["paymentMode"] = $this->paymentMode;
		$DadosArray["paymentMethod"] = $this->__get('paymentMethod'); 
		$DadosArray['receiverEmail'] = $this->email;
		$DadosArray['currency'] = $this->moeda;
		$DadosArray['extraAmount'] = $this->__get('extraAmount');
		$DadosArray['itemId1'] = $this->__get('itemId1');
		$DadosArray['itemDescription1'] = $this->__get('itemDescription1');
		$DadosArray['itemAmount1'] = $this->__get('itemAmount1');
		$DadosArray['itemQuantity1'] = $this->__get('itemQuantity1');
		$DadosArray['notificationURL'] = $this->url_retorno;
		$DadosArray['reference'] = $this->__get('reference');
		$DadosArray['senderName'] = $this->__get('senderName');
		$DadosArray['senderCPF'] = $this->__get('senderCPF');
		$DadosArray['senderAreaCode'] = $this->__get('senderAreaCode');
		$DadosArray['senderPhone'] = $this->__get('senderPhone');
		$DadosArray['senderEmail'] = $this->__get('senderEmail');
		$DadosArray['senderHash'] = $this->__get('senderHash');
		$DadosArray['shippingAddressRequired'] = $this->__get('shippingAddressRequired');
		$DadosArray['shippingAddressStreet'] = $this->__get('shippingAddressStreet');
		$DadosArray['shippingAddressNumber'] = $this->__get('shippingAddressNumber');
		$DadosArray['shippingAddressComplement'] = $this->__get('shippingAddressComplement');
		$DadosArray['shippingAddressDistrict'] = $this->__get('shippingAddressDistrict');
		$DadosArray['shippingAddressPostalCode'] = $this->__get('shippingAddressPostalCode');
		$DadosArray['shippingAddressCity'] = $this->__get('shippingAddressCity');
		$DadosArray['shippingAddressState'] = $this->__get('shippingAddressState');
		$DadosArray['shippingAddressCountry'] = $this->__get('shippingAddressCountry');
		$DadosArray['shippingType'] = $this->__get('shippingType');
		$DadosArray['shippingCost'] = $this->__get('shippingCost');
		$DadosArray['creditCardToken'] = $this->__get('creditCardToken');
		$DadosArray['installmentQuantity'] = $this->__get('installmentQuantity');
		$DadosArray['installmentValue'] = $this->__get('installmentValue');
		$DadosArray['noInterestInstallmentQuantity'] = $this->__get('noInterestInstallmentQuantity');
		$DadosArray['creditCardHolderName'] = $this->__get('creditCardHolderName');
		$DadosArray['creditCardHolderCPF'] = $this->__get('creditCardHolderCPF');
		$DadosArray['creditCardHolderBirthDate'] = $this->__get('creditCardHolderBirthDate');
		$DadosArray['creditCardHolderAreaCode'] = $this->__get('creditCardHolderAreaCode');
		$DadosArray['creditCardHolderPhone'] = $this->__get('creditCardHolderPhone');
		$DadosArray['billingAddressStreet'] = $this->__get('billingAddressStreet');
		$DadosArray['billingAddressNumber'] = $this->__get('billingAddressNumber');
		$DadosArray['billingAddressComplement'] = $this->__get('billingAddressComplement');
		$DadosArray['billingAddressDistrict'] = $this->__get('billingAddressDistrict');
		$DadosArray['billingAddressPostalCode'] = $this->__get('billingAddressPostalCode');
		$DadosArray['billingAddressCity'] = $this->__get('billingAddressCity');
		$DadosArray['billingAddressState'] = $this->__get('billingAddressState');
		$DadosArray['billingAddressCountry'] = $this->__get('billingAddressCountry');

		$this->buildQuery = http_build_query($DadosArray);
	
		$url = $this->url . "transactions";
	
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, Array("Content-Type: application/x-www-form-urlencoded; charset=UTF-8"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->buildQuery);

		$retorno = curl_exec($curl);

		curl_close($curl);

		$xml = simplexml_load_string($retorno);

		$this->savedb = $xml;

		//$this->saveDb();
		
		$this->retorna = ['erro' => true, 'dados' => $this->savedb];
	}

	public function saveDb()
	{
		$this->retorna = $this->saveDb;

		$query = "insert into pagamentos (tipo_pg, cod_trans, status, link_boleto, carrinho_id, created) values (:tipo_pg, :cod_trans, :status, :link_boleto, :carrinho_id, now()";

		$stmt = $this->db->prepare($query);
		$stmt->bindParam(':tipo_pg', $this->savedb->paymentMethod->type, PDO::PARAM_INT);
		$stmt->bindParam(':cod_trans', $this->savedb->code, PDO::PARAM_STR);
		$stmt->bindParam(':status', $this->savedb->status, PDO::PARAM_INT);
		$stmt->bindParam(':link_boleto', $this->savedb->paymentLink, PDO::PARAM_STR);
		$stmt->bindParam(':carrinho_id', $this->savedb->reference, PDO::PARAM_INT);
		$stmt->execute();

		return $this->retorna;
	}
}