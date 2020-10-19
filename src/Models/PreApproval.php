<?php

namespace Src\Models;

use Router\Model\Container;
use Router\Model\Model;

class PreApproval extends Model
{
    private $url;
    private $charge;
    private $reference;
    private $name;
    private $details;
    private $amountPerPayment;
    private $maxAmountPerPayment;
    private $maxTotalAmount;
    private $maxAmountPerPeriod;
    private $cancelURL;
    private $maxPaymentsPerPeriod;
    private $period;
    private $initialDate;
    private $finalDate;
    private $buildQuery;
    private $retorna;
    private $plan;
    private $email;
    private $token;
	private $url_retorno;
	private $email_token;
	private $moeda;
	private $paymentMode;
	private $paymentMethod;
	private $receiverEmail;
	private $extraAmount;
	private $itemId1;
	private $itemDescription1;
	private $itemAmount1;
	private $itemQuantity1;
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
    private $urlPayPlan;
    private $codePayPlan;
    private $planQuery;

    public function __construct()
	{
		require_once dirname(__DIR__ , 1) . "/Config.php";

		$this->email            = PAGSEGURO['email'];
		$this->token            = PAGSEGURO['token'];
        $this->url              = PAGSEGURO['adesaoPlan'];
        $this->urlPayPlan       = PAGSEGURO['payPlan'];
	}

	public function __get($var)
    {
        return $this->$var;
    }

    public function __set($var, $value)
    {
        $this->$var = $value;
    }

    public function createPlanPreApproval()
    {        
        require_once dirname(__DIR__ , 1) . "/Config.php";

        $DadosArray["name"] = $this->__get('name');
		$DadosArray["reference"] = $this->__get('reference');
		$DadosArray["charge"] = $this->__get('charge');
		$DadosArray["period"] = $this->__get('period'); 
		$DadosArray['amountPerPayment'] = $this->__get('amountPerPayment');
		$DadosArray['details'] = $this->__get('details');
		$DadosArray['cancelURL'] = $this->__get('cancelURL');

        $this->buildQuery = "
                        <?xml version='1.0' encoding='ISO-8859-1' standalone='yes'?>
                        <preApprovalRequest>
                        <preApproval>
                        <name>" . $DadosArray["name"] . "</name>
                        <reference>" . $DadosArray["reference"] . "</reference>
                        <charge>" . $DadosArray["charge"] . "</charge>
                        <period>" . $DadosArray["period"] . "</period>
                        <amountPerPayment>" . $DadosArray["amountPerPayment"] . "</amountPerPayment>
                        <details>" . $DadosArray["details"] . " </details>
                        <cancelURL>" . $DadosArray["cancelURL"] . "</cancelURL>
                        <membershipFee>" . " " . "</membershipFee>
                        <trialPeriodDuration>" . " " . "</trialPeriodDuration>
                        </preApproval>
                        <maxUses>500</maxUses>
                        </preApprovalRequest>
                    ";

        $this->url = PAGSEGURO['createapprov'] . "request/?email=". PAGSEGURO['email'] ."&token=". PAGSEGURO['token'];
        //echo $this->url;

        $curl = curl_init($this->url);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/vnd.pagseguro.com.br.v3+xml;charset=ISO-8859-1', 'Content-Type: application/xml;charset=ISO-8859-1'));
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $this->buildQuery);

        $retorno = curl_exec($curl);

        curl_close($curl);
        
        $xml = simplexml_load_string($retorno);

        $this->retorna = ['erro' => true, 'dados' => $xml];
    }

    public function adesaoPlan()
	{
        $DadosArray["plan"] = $this->plan;
        $DadosArray["email"] = $this->email;
		$DadosArray["token"] = $this->token;
		$DadosArray["paymentMode"] = $this->paymentMode;
		$DadosArray["paymentMethod"] = $this->paymentMethod; 
		$DadosArray['receiverEmail'] = $this->email;
		$DadosArray['currency'] = $this->moeda;
		$DadosArray['extraAmount'] = $this->extraAmount;
        $DadosArray['itemId1'] = $this->itemId1;
        $DadosArray['reference'] = $this->reference;
		$DadosArray['itemDescription1'] = $this->itemDescription1;
		$DadosArray['itemAmount1'] = $this->itemAmount1;
		$DadosArray['itemQuantity1'] = $this->itemQuantity1;
		$DadosArray['notificationURL'] = $this->url_retorno;
		$DadosArray['reference'] = $this->reference;
		$DadosArray['senderName'] = $this->senderName;
		$DadosArray['senderCPF'] = $this->senderCPF;
		$DadosArray['senderAreaCode'] = $this->senderAreaCode;
		$DadosArray['senderPhone'] = $this->senderPhone;
		$DadosArray['senderEmail'] = $this->senderEmail;
		$DadosArray['senderHash'] = $this->senderHash;
		$DadosArray['shippingAddressRequired'] = $this->shippingAddressRequired;
		$DadosArray['shippingAddressStreet'] = $this->shippingAddressStreet;
		$DadosArray['shippingAddressNumber'] = $this->shippingAddressNumber;
		$DadosArray['shippingAddressComplement'] = $this->shippingAddressComplement;
		$DadosArray['shippingAddressDistrict'] = $this->shippingAddressDistrict;
		$DadosArray['shippingAddressPostalCode'] = $this->shippingAddressPostalCode;
		$DadosArray['shippingAddressCity'] = $this->shippingAddressCity;
		$DadosArray['shippingAddressState'] = $this->shippingAddressState;
		$DadosArray['shippingAddressCountry'] = $this->shippingAddressCountry;
		$DadosArray['shippingType'] = $this->shippingType;
		$DadosArray['shippingCost'] = $this->shippingCost;
		$DadosArray['creditCardToken'] = $this->creditCardToken;
		$DadosArray['installmentQuantity'] = $this->installmentQuantity;
		$DadosArray['installmentValue'] = $this->installmentValue;
		$DadosArray['noInterestInstallmentQuantity'] = $this->noInterestInstallmentQuantity;
		$DadosArray['creditCardHolderName'] = $this->creditCardHolderName;
		$DadosArray['creditCardHolderCPF'] = $this->creditCardHolderCPF;
		$DadosArray['creditCardHolderBirthDate'] = $this->creditCardHolderBirthDate;
		$DadosArray['creditCardHolderAreaCode'] = $this->creditCardHolderAreaCode;
		$DadosArray['creditCardHolderPhone'] = $this->creditCardHolderPhone;
		$DadosArray['billingAddressStreet'] = $this->billingAddressStreet;
		$DadosArray['billingAddressNumber'] = $this->billingAddressNumber;
		$DadosArray['billingAddressComplement'] = $this->billingAddressComplement;
		$DadosArray['billingAddressDistrict'] = $this->billingAddressDistrict;
		$DadosArray['billingAddressPostalCode'] = $this->billingAddressPostalCode;
		$DadosArray['billingAddressCity'] = $this->billingAddressCity;
		$DadosArray['billingAddressState'] = $this->billingAddressState;
        $DadosArray['billingAddressCountry'] = $this->billingAddressCountry;
        $DadosArray['amountPerPayment'] = $this->amountPerPayment;

         $this->buildQuery = '
            {
                "plan":"'.$DadosArray["plan"].'",
                "reference":"'.$DadosArray['reference'].'",
                "sender":{
                    "name":"'.$DadosArray['senderName'].'",
                    "email":"'.$DadosArray['senderEmail'].'",
                    "ip":"192.168.0.1",
                    "hash":"'.$DadosArray['senderHash'].'",
                    "phone":{
                    "areaCode":"'.$DadosArray['senderAreaCode'].'",
                    "number":"'.$DadosArray['senderPhone'].'"
                },
                "address":{
                    "street":"'.$DadosArray['shippingAddressStreet'].'",
                    "number":"'.$DadosArray['shippingAddressNumber'].'",
                "complement":"'.$DadosArray['shippingAddressComplement'].'",
                    "district":"'.$DadosArray['shippingAddressDistrict'].'",
                    "city":"'.$DadosArray['shippingAddressCity'].'",
                    "state":"'.$DadosArray['shippingAddressState'].'",
                    "country":"'.$DadosArray['shippingAddressCountry'].'",
                    "postalCode":"'.$DadosArray['shippingAddressPostalCode'].'"
                },
                "documents":[
                    {
                        "type":"CPF",
                        "value":"'.$DadosArray['creditCardHolderCPF'].'"
                    }
                ]
                },
                "paymentMethod":{
                    "type":"CREDITCARD",
                    "creditCard":{
                    "token":"'.$DadosArray["creditCardToken"].'",
                    "holder":{
                        "name":"'.$DadosArray['creditCardHolderName'].'",
                        "birthDate":"'.$DadosArray['creditCardHolderBirthDate'].'",
                        "documents":[
                        {
                            "type":"CPF",
                            "value":"'.$DadosArray['creditCardHolderCPF'].'"
                        }
                    ],
                    "billingAddress":{
                        "street":"'.$DadosArray['billingAddressStreet'].'",
                        "number":"'.$DadosArray['billingAddressNumber'].'",
                        "complement":"'.$DadosArray['billingAddressComplement'].'",
                        "district":"'.$DadosArray['billingAddressDistrict'].'",
                        "city":"'.$DadosArray['billingAddressCity'].'",
                        "state":"'.$DadosArray['billingAddressState'].'",
                        "country":"'.$DadosArray['billingAddressCountry'].'",
                        "postalCode":"'.$DadosArray['billingAddressPostalCode'].'"
                    },
                    "phone":{
                        "areaCode":"'.$DadosArray['creditCardHolderAreaCode'].'",
                        "number":"'.$DadosArray['creditCardHolderPhone'].'"
                    }
                }
                }
                }
            }
        ';

        $url = $this->url . "email=" . $this->email . "&token=" . $this->token;
	
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, Array('Accept: application/vnd.pagseguro.com.br.v1+json;charset=ISO-8859-1', 'Content-Type: application/json'));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $this->buildQuery);

		$retorno = curl_exec($curl);

		curl_close($curl);

        $adesaoCode = json_decode($retorno);

        $codeToPayPlan = str_replace('"','',$adesaoCode->code);
        
        if($codeToPayPlan != '') {
            
            $this->planQuery= '
                <payment>
                    <items>
                        <item>
                            <id>'.$DadosArray['itemId1'].'</id>
                            <description>Clube de assinatura de cafes especiais - Merula Cafes Especiais</description>
                            <amount>'.$DadosArray['amountPerPayment'].'</amount>
                            <quantity>1</quantity>
                        </item>
                    </items>
                    <reference>'.$DadosArray['reference'].'</reference>
                    <preApprovalCode>'.$codeToPayPlan.'</preApprovalCode>
                </payment> 
            ';          

            $url = $this->urlPayPlan . "email=" . $this->email . "&token=" . $this->token;
	
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, Array('Accept: application/vnd.pagseguro.com.br.v3+json;charset=ISO-8859-1', 'Content-Type: application/xml;charset=ISO-8859-1'));
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $this->planQuery);

            $retorna = curl_exec($curl);

            curl_close($curl);

            $this->codePayPlan = json_decode($retorna);
        }
        
        $this->retorna = ['erro' => true, 'dados' => $this->codePayPlan];
    }
}