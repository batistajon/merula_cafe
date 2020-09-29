<?php

namespace Router\Controller;

    abstract class Action {

        protected $view;
        
        public function __construct() {
            $this->view = new \stdClass();
        }

        protected function render($view, $layout = 'layout') {

            $this->view->page = $view;

                if(file_exists("src/Views/" . $layout . ".phtml")) {

                    require_once "src/Views/" . $layout . ".phtml";

                }
                    $this->content();

                }


        protected function content() {

            $classAtual = get_class($this);

            $classAtual = str_replace('Src\\Controllers\\', '', $classAtual);

            $classAtual = strtolower(str_replace('Controller', '', $classAtual));
            
            require_once "src/Views/" . $classAtual . "/" . $this->view->page . ".phtml";
        }

    }
?>   