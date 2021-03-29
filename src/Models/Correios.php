<?php

namespace Src\Models;

use Router\Model\Model;

class Correios extends Model
{
    private $retorna;
    private $data;
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
    private $volumeTotal;


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
        $url="http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem={$this->__get('cepOrigem')}&sCepDestino={$this->__get('cepDestino')}&nVlPeso={$this->__get('peso')}&nCdFormato={$this->__get('formato')}&nVlComprimento={$this->__get('comprimento')}&nVlAltura={$this->__get('altura')}&nVlLargura={$this->__get('largura')}&sCdMaoPropria={$this->__get('maoPropria')}&nVlValorDeclarado={$this->__get('valorDeclarado')}&sCdAvisoRecebimento={$this->__get('avisoRecebimento')}&nCdServico={$this->__get('codigo')}&nVlDiametro={$this->__get('diametro')}&StrRetorno=xml&nIndicaCalculo=3"; 
        
        $frete = simplexml_load_string(file_get_contents($url));

        $this->retorna = $frete;
    }
}