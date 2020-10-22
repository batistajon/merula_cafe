<?php

/**
 * DATA BASE CONNECTION
 */
if($_SERVER['SERVER_NAME'] == 'localhost') { 

    define("ROUTER_DB_CONFIG", [
        "driver"   => "mysql",
        "host"     => "localhost",
        "dbname"   => "dbname",
        "username" => "root",
        "passwd"   => ""
    ]);

} else {

    define("ROUTER_DB_CONFIG", [
        "driver"   => "mysql",
        "host"     => "localhost",
        "dbname"   => "dbname",
        "username" => "username",
        "passwd"   => "passwd"
    ]);
}    

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
    "facebook_page"   => "facebook_page",
    "facebook_author" => "facebook_author",
    "facebook_appId"  => "1",
    "instagram_page"  => "instagram_page",
    "twitter_creator" => "@",
    "twitter_site"    => "https://twitter.com/",
    "whatsapp_api"    => "https://api.whatsapp.com/send?phone=&text=Queria uma informação sobre o Merula."
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
 * 
 * 
 */

define("PAGSEGURO", [
    "email"           => "contato@site.com.br",
    "token"           => "",
    "createapprov"    => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals/",
    "adesaoPlan"      => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals?",
    "payPlan"         => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals/payment?",
    "url_pag"         => "https://ws.sandbox.pagseguro.uol.com.br/v2/",
    "script"          => "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js",
    "pay_notif"       => "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/",
    "email_loja"      => "contato@cafemerula.com.br",
    "moeda"           => "BRL",
    "url_site"        => "http://". $_SERVER['HTTP_HOST'] ."/",
    "url_notificacao" => "http://". $_SERVER['HTTP_HOST'] ."/notificacao-pagseguro"         
]);

/* if($_SERVER['SERVER_NAME'] == 'localhost') {
    /**
     * SANDBOX
    
    define("PAGSEGURO", [
        "email"           => "",
        "token"           => "",
        "createapprov"    => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals/",
        "adesaoPlan"      => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals?",
        "payPlan"         => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals/payment?",
        "url_pag"         => "https://ws.sandbox.pagseguro.uol.com.br/v2/",
        "script"          => "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js",
        "pay_notif"       => "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/",
        "email_loja"      => "contato@cafemerula.com.br",
        "moeda"           => "BRL",
        "url_site"        => "http://". $_SERVER['HTTP_HOST'] ."/",
        "url_notificacao" => "http://". $_SERVER['HTTP_HOST'] ."/notificacao-pagseguro"         
    ]);

} else {
    /**
     * PAGSEGURO
    
    define("PAGSEGURO", [
        "email"           => "",
        "token"           => "",
        "createapprov"    => "https://ws.pagseguro.uol.com.br/pre-approvals/",
        "adesaoPlan"      => "https://ws.pagseguro.uol.com.br/pre-approvals?",
        "payPlan"         => "https://ws.pagseguro.uol.com.br/pre-approvals/payment?",
        "url_pag"         => "https://ws.pagseguro.uol.com.br/v2/",
        "script"          => "https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js",
        "pay_notif"       => "https://ws.pagseguro.uol.com.br/v3/transactions/notifications/",
        "email_loja"      => "contato@cafemerula.com.br",
        "moeda"           => "BRL",
        "url_site"        => "https://". $_SERVER['HTTP_HOST'] ."/",
        "url_notificacao" => "https://". $_SERVER['HTTP_HOST'] ."/notificacao-pagseguro"  
    ]);
} */
