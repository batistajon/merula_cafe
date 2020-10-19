<?php

namespace Src\Models;

use Router\Model\Model;

class Correios extends Model
{
    private $retorna;
    private $cepOrigem;
    private $cepDestino;
    private $peso;
    private $formato;
    private $comprimento;
    private $altura;
    private $largura;
    private $maoPropria;
    private $valorDeclarado;
    private $avisoRecebimento;
    private $codigo;
    private $diametro;


    public function __get($var)
    {
        return $this->$var;
    }

    public function __set($var, $value)
    {
        $this->$var = $value;
	}

    //Pesquisa de preço e prazo de encomendas do correio
    public function pesquisaPrecoPrazo()
    {
        $url="http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem={$this->cepOrigem}&sCepDestino={$this->cepDestino}&nVlPeso={$this->peso}&nCdFormato={$this->formato}&nVlComprimento={$this->comprimento}&nVlAltura={$this->altura}&nVlLargura={$this->largura}&sCdMaoPropria={$this->maoPropria}&nVlValorDeclarado={$this->valorDeclarado}&sCdAvisoRecebimento={$this->avisoRecebimento}&nCdServico={$this->codigo}&nVlDiametro={$this->diametro}&StrRetorno=xml&nIndicaCalculo=3"; 
        
        $frete = simplexml_load_string(file_get_contents($url));

        $this->retorna = $frete;
    }
}