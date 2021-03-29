<?php
@session_start();
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
        "host"     => "localhost:3306",
        "dbname"   => "cred3737_merula",
        "username" => "cred3737_cred373",
        "passwd"   => "@Oliver2307"
    ]);
}    

/**
 * APP INFO
 */
if($_SERVER['SERVER_NAME'] == 'localhost') { 

    define("APP", [
        "name"   => "Merula Cafés Especiais de Impacto",
        "desc"   => "Microtorrefação carioca de cafés especiais de impacto. Parte do valor da sua assinatura, ou compra, volta pro pequeno produtor. Visite nosso site",
        "domain" => $_SERVER['HTTP_HOST'],
        "locale" => "pt_BR",
        "root"   => "http://". $_SERVER['HTTP_HOST'] ."/"
    ]);

} else {

    define("APP", [
        "name"   => "Merula Cafés Especiais de Impacto",
        "desc"   => "Microtorrefação carioca de cafés especiais de impacto. Parte do valor da sua assinatura, ou compra, volta pro pequeno produtor. Visite nosso site",
        "domain" => $_SERVER['HTTP_HOST'],
        "locale" => "pt_BR",
        "root"   => "https://". $_SERVER['HTTP_HOST'] ."/"
    ]);
}

/**
 * SOCIAL CONFIG
 */
define("SOCIAL", [
    "facebook_page"   => "https://www.facebook.com/merulacafe",
    "facebook_author" => "jonathas.batista.16",
    "facebook_appId"  => "424177548967716",
    "instagram_page"  => "https://www.instagram.com/merulacafe/",
    "twitter_creator" => "@BatistaJon",
    "twitter_site"    => "https://twitter.com/BatistaJon",
    "whatsapp_api"    => "https://api.whatsapp.com/send?phone=5521969199778&text=Queria uma informação sobre o Merula."
]);

/**
 * MAIL CONNECT
 */
define("MAIL", [
    "host"       => "cafemerula.com.br",
    "port"       => "465",
    "user"       => "ola@cafemerula.com.br",
    "passwd"     => "@Oliver2307",
    "from_name"  => "Equipe Merula",
    "from_email" => "ola@cafemerula.com.br"
]);

/**
 * SOCIAL LOGIN: FACEBOOK
 */
define("FACEBOOK_LOGIN", [
    'clientId'          => '424177548967716',
    'clientSecret'      => '9f03e5114ecde65c1f2a715f0ef028b7',
    'redirectUri'       => "https://". $_SERVER['HTTP_HOST'] . "/facebook",
    'graphApiVersion'   => 'v9.0',
]);

/**
 * SOCIAL LOGIN: TWITTER
 */
define("GOOGLE_LOGIN", [
    'clientId'          => '843959155413-19iqa0pdfm6e3poql3k5m6pgm946ijgm.apps.googleusercontent.com',
    'clientSecret'      => 'k2QbrXfi_5cjRyb5M8uYj0Kc',
    'redirectUri'       => "https://". $_SERVER['HTTP_HOST']
]);

/* define("PAGSEGURO", [
    "email"           => "contato@cafemerula.com.br",
    "token"           => "2565BA929EAC41A38D10A3D35DA1B620",
    "createapprov"    => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals/",
    "adesaoPlan"      => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals?",
    "payPlan"         => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals/payment?",
    "url_pag"         => "https://ws.sandbox.pagseguro.uol.com.br/v2/",
    "script"          => "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js",
    "pay_notif"       => "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/",
    "url_review"      => "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/",
    "email_loja"      => "contato@cafemerula.com.br",
    "moeda"           => "BRL",
    "url_site"        => "http://". $_SERVER['HTTP_HOST'] ."/",
    "url_notificacao" => "http://". $_SERVER['HTTP_HOST'] ."/notificacao-pagseguro"         
]); */

if($_SERVER['SERVER_NAME'] == 'localhost') {
    
    define("PAGSEGURO", [
        "email"           => "",
        "token"           => "",
        "createapprov"    => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals/",
        "adesaoPlan"      => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals?",
        "payPlan"         => "https://ws.sandbox.pagseguro.uol.com.br/pre-approvals/payment?",
        "url_pag"         => "https://ws.sandbox.pagseguro.uol.com.br/v2/",
        "script"          => "https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js",
        "pay_notif"       => "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/",
        "url_review"      => "https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/",
        "email_loja"      => "contato@cafemerula.com.br",
        "moeda"           => "BRL",
        "url_site"        => "http://". $_SERVER['HTTP_HOST'] ."/",
        "url_notificacao" => "http://". $_SERVER['HTTP_HOST'] ."/notificacao-pagseguro"         
    ]);

} else {
    
    define("PAGSEGURO", [
        "email"           => "",
        "token"           => "",
        "createapprov"    => "https://ws.pagseguro.uol.com.br/pre-approvals/",
        "adesaoPlan"      => "https://ws.pagseguro.uol.com.br/pre-approvals?",
        "payPlan"         => "https://ws.pagseguro.uol.com.br/pre-approvals/payment?",
        "url_pag"         => "https://ws.pagseguro.uol.com.br/v2/",
        "script"          => "https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js",
        "pay_notif"       => "https://ws.pagseguro.uol.com.br/v3/transactions/notifications/",
        "url_review"      => "https://ws.pagseguro.uol.com.br/v3/transactions/",    
        "email_loja"      => "contato@cafemerula.com.br",
        "moeda"           => "BRL",
        "url_site"        => "https://". $_SERVER['HTTP_HOST'] ."/",
        "url_notificacao" => "https://". $_SERVER['HTTP_HOST'] ."/notificacao-pagseguro"  
    ]);
}
