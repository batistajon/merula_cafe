<?php

namespace Router\Controller;

abstract class Action {

    protected $view;
    
    public function __construct()
    {
        $this->view = new \stdClass();
    }

    protected function render($view, $layout = 'layout')
    {
        @session_start();
        $this->view->primeiroNome = \ucfirst(explode(' ', @$_SESSION['user']['nome'])[0]);
        $this->view->page = $view; 

        if(file_exists(dirname(__DIR__, 3) . "/src/Views/" . $layout . ".phtml")) {

            require_once dirname(__DIR__, 3) . "/src/Config.php";

            require_once dirname(__DIR__, 3) . "/src/Views/" . $layout . ".phtml";
            
        }
        
        $this->content();
    }

    protected function content()
    {
        $classAtual = get_class($this);

        $classAtual = str_replace('Src\\Controllers\\', '', $classAtual);

        $classAtual = strtolower(str_replace('Controller', '', $classAtual));
  
        require_once dirname(__DIR__, 3) . "/src/Views/" . $classAtual . "/" . $this->view->page . ".phtml";     
    }

    protected function templateEmail($templateEmail)
    {
        require_once dirname(__DIR__, 3) . "/src/Views/email/" . $templateEmail . ".phtml";
    }
}