<?php

/**
 * DATA BASE CONNECTION
 */
define("ROUTER_DB_CONFIG", [
    "driver"   => "mysql",
    "host"     => "localhost",
    "dbname"   => "celke",
    "username" => "root",
    "passwd"   => "123"
]);

/**
 * APP INFO
 */
define("APP", [
    "name"   => "Merula Cafés Especiais de Impacto",
    "desc"   => "Microtorrefação carioca de cafés especiais de impacto. Parte do valor da sua assinatura, ou compra, volta pro pequeno produtor. Visite nosso site",
    "domain" => $_SERVER['HTTP_HOST'],
    "locale" => "pt_BR",
    "root"   => "https://". $_SERVER['HTTP_HOST'] ."/"
]);

/**
 * SOCIAL CONFIG
 */
define("SOCIAL", [
    "facebook_page"   => "https://www.facebook.com/merulacafe",
    "facebook_author" => "jonathas.batista.16",
    "facebook_appId"  => "1",
    "instagram_page"  => "https://www.instagram.com/merulacafe/",
    "twitter_creator" => "@BatistaJon",
    "twitter_site"    => "https://twitter.com/BatistaJon",
    "whatsapp_api"    => "https://api.whatsapp.com/send?phone=5521969199778&text=Queria uma informação sobre o Merula."
]);

/**
 * MAIL CONNECT
 */
define("MAIL", []);

/**
 * SOCIAL LOGIN: FACEBOOK
 */
define("FACEBOOK_LOGIN", []);

/**
 * SOCIAL LOGIN: TWITTER
 */
define("GOOGLE_LOGIN", []);

/**
 * PAGSEGURO INFO
 */
if($_SERVER['SERVER_NAME'] == 'localhost') {
    /**
     * SANDBOX
     */
    define("PAGSEGURO", [
        "email"           => "contato@cafemerula.com.br",
        "token"           => "2565BA929EAC41A38D10A3D35DA1B620",
        "createapprov"    => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals/",
        "adesaoPlan"      => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals?",
        "payPlan"         => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals/payment?",
        "url_pag"         => "https://ws.sandbox.pagseguro.uol.com.br/v2/",
        "script"          => "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js",
        "email_loja"      => "contato@cafemerula.com.br",
        "moeda"           => "BRL",
        "url_site"        => "http://". $_SERVER['HTTP_HOST'] ."/",
        "url_notificacao" => "http://". $_SERVER['HTTP_HOST'] ."/notificacao"         
    ]);

} else {
    /**
     * PAGSEGURO
     */
    define("PAGSEGURO", [
        "email"           => "contato@cafemerula.com.br",
        "token"           => "15d65999-a2e9-4324-8db2-dd8d63bd8559b5b5720a4603a3df6481160be8fccd85812d-19b3-4cc2-be40-9ed72022d41e",
        "createapprov"    => "https://ws.pagseguro.uol.com.br/pre-approvals/",
        "adesaoPlan"      => "https://ws.pagseguro.uol.com.br/pre-approvals?",
        "payPlan"         => "https://ws.pagseguro.uol.com.br/pre-approvals/payment?",
        "url_pag"         => "https://ws.pagseguro.uol.com.br/v2/",
        "script"          => "https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js",
        "email_loja"      => "contato@cafemerula.com.br",
        "moeda"           => "BRL",
        "url_site"        => "https://". $_SERVER['HTTP_HOST'] ."/",
        "url_notificacao" => "https://". $_SERVER['HTTP_HOST'] ."/notificacao"  
    ]);
}    