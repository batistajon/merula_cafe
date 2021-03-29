<?php

namespace Src\Models;

use PHPMailer\PHPMailer\PHPMailer;
use Router\Model\Container;
use Router\Model\Model;

class EmailNotification extends Model
{
    private $dadosEmail;
    private $template;
    private $cart;

    public function __get($var)
    {
        return $this->$var;
    }

    public function __set($var, $value)
    {
        $this->$var = $value;
    }

    public function cartDetails()
    {
        $carrinho = $this->__get('cart');

        $html = "";

        foreach ($carrinho as $keys => $items) {
            foreach ($items as $key => $item) {
                $html .= "<tr>";

                $html .= "<td>{$item->description}</td>";

                $html .= "<td>{$item->quantity}</td>";

                $html .= "<td>R$ ".str_replace('.', ',', $item->amount)."</td>";

                $html .= "</tr>";
            }
        }

        return $html;
    }
    
    public function sendSuccessPayment()
    {
        $dadosEmail = $this->__get('dadosEmail');

        $firstName = ucfirst(explode(' ', $dadosEmail->sender->name)[0]);
        $totalCarrinho = str_replace('.', ',', $dadosEmail->grossAmount);
        $shippingCost = str_replace('.', ',', $dadosEmail->shipping->cost);
        $paymentMethod = null;

        if($dadosEmail->paymentMethod->type == 1) {

            $paymentMethod = 'Cartão de Crédito';

        } elseif($dadosEmail->paymentMethod->type == 2) {

            $paymentMethod = 'Boleto';
        }    

        $this->__set('cart', $dadosEmail->items);
        $cart = $this->cartDetails();

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("ola@cafemerula.com.br", "Equipe Merula");
        $email->setSubject("{$firstName}, sua compra foi aprovada!");
        $email->addTo(/* $dadosEmail->sender->email */"batista.jonathas@gmail.com", "Example User");
        //$email->setTemplateId('d-4211386ca8be422e848790f1dcf901d5');
        $email->addContent("text/plain", "Obrigado por comprar com a gente, {$firstName}.");
        $email->addContent(
            "text/html", "<div style='background-color: rgb(36, 35, 35); padding-top:50px; padding-bottom:90px; color:#666666;'>".
                            "<img src='https://www.cafemerula.com.br/assets/merula_logo.png' alt='Merula' style='width: 80px; display:block;  margin:30px auto'>".
                            "<div style='font-family:Arial, Helvetica, sans-serif; font-size:12px; width:600px; margin:0 auto; padding: 50px; background-color:#fff; color:#666666;'>".
                                "<h3>Olá, ".$firstName.". Viemos com boas notícias: seu pagamento foi aprovado!</h3>".
                                "<p>Já estamos separando o seu café. Daqui a pouco ele deve chegar aí na sua casa.</p>".
                                "<p>Assim que tivermos mais alguma novidade, vamos te avisar. Por isso, fique de olho no seu e-mail, ok?</p>".
                                "<p>O código do seu pedido é: {$dadosEmail->code}</p>".
                                "<p><strong>Segue o resumo da sua compra: </strong></p>".
                                "<hr>".
                                "<table style='text-align:left; width:500px'>".
                                    "<thead>".
                                        "<tr>".
                                            "<td><strong>Descrição</strong></td>".
                                            "<td><strong>Quantidade</strong></td>".
                                            "<td><strong>Preço</strong></td>".
                                        "</tr>".
                                    "</thead>".
                                    "<tbody>".
                                    $cart.
                                    "</tbody>".
                                "</table>".
                                "<p>Frete: R$ ".$shippingCost."</p>".
                                "<p>Total da compra: <strong>R$ ".$totalCarrinho."</strong></p>".
                                "<p>Método de pagamento: ".$paymentMethod."</p>".
                                "<hr>".
                                "<p><strong>Dados da entrega: </strong><br>".
                                $dadosEmail->shipping->address->street.", ".$dadosEmail->shipping->address->number."<br>".
                                $dadosEmail->shipping->address->complement."<br>".
                                $dadosEmail->shipping->address->district."<br>".
                                $dadosEmail->shipping->address->city."<br>".
                                $dadosEmail->shipping->address->postalCode."</p>".
                                "<hr>".
                                "<p>Um abraço!</p>".
                                "<p>Equipe Merula.</p>".
                                "<div style='text-align:center;'>".
                                    "<p><a href='https://www.cafemerula.com.br/' style='color:#666666; margin: 0 auto'>www.cafemerula.com.br</a></p>".
                                    "<hr>".
                                    "<small style='text-align:center;'>Este e-mail foi enviado automaticamente, pedimos por gentileza que não o responda.</small>".
                                "</div>".
                            "</div>".
                        "</div>"    
                    );

        $sendgrid = new \SendGrid('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');
        //$sendgrid->send($email);

        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (\Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }       

    public function sendCancelPayment()
    {
        $dadosEmail = $this->__get('dadosEmail');
        $senderName = explode(' ', $dadosEmail->sender->name);
        $firstName = ucfirst($senderName[0]);

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("ola@cafemerula.com.br", "Equipe Merula");
        $email->setSubject("{$firstName}, sua compra foi recusada");
        $email->addTo(/* $dadosEmail->sender->email */"batista.jonathas@gmail.com", "{$firstName}");
        $email->addContent("text/plain", "Que pena! Mas nao se preocupe, {$firstName}.");
        $email->addContent(
            "text/html", "<div style='background-color: rgb(36, 35, 35); padding-top:50px; padding-bottom:90px; color:#666666;'>".
            "<img src='https://www.cafemerula.com.br/assets/merula_logo.png' alt='Merula' style='width: 80px; display:block;  margin:30px auto'>".
            "<div style='font-family:Arial, Helvetica, sans-serif; font-size:12px; width:600px; margin:0 auto; padding: 50px; background-color:#fff; color:#666666;'>".
                "<h3>Olá, ".$firstName.". Viemos com notícias não muito boas: seu pagamento foi foi recusado.</h3>".
                "<p>Entre em contato com sua operadora.</p>".
                "<p>Caso precise de alguma ajuda com seu pagamento, entre em contato conosco.".
                "<p>Um abraço!</p>".
                "<p>Equipe Merula.</p>".
                "<div style='text-align:center;'>".
                    "<p><a href='https://www.cafemerula.com.br/' style='color:#666666; margin: 0 auto'>www.cafemerula.com.br</a></p>".
                    "<hr>".
                    "<small style='text-align:center;'>Este e-mail foi enviado automaticamente, pedimos por gentileza que não o responda.</small>".
                "</div>".
            "</div>".
        "</div>"
        );

        $sendgrid = new \SendGrid('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');
        try {
            $response = $sendgrid->send($email);
            echo '<br>';
            print $response->statusCode() . "\n";
            echo '<br>';
            print_r($response->headers());
            echo '<br>';
            print $response->body() . "\n";
            echo '<br>';
        } catch (\Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }
}