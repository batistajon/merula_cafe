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
        /* $url = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCe
            pOrigem=70002900&sCepDestino=04547000&nVlPeso=1&nCdFormato=1&nVlComprimento=20&nVlAltura=2
            0&nVlLargura=20&sCdMaoPropria=n&nVlValorDeclarado=0&sCdAvisoRecebimento=n&nCdServico=04510&
            nVlDiametro=0&StrRetorno=xml&nIndicaCalculo=3"; */

        $url="http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem={$this->cepOrigem}&sCepDestino={$this->cepDestino}&nVlPeso={$this->peso}&nCdFormato={$this->formato}&nVlComprimento={$this->comprimento}&nVlAltura={$this->altura}&nVlLargura={$this->largura}&sCdMaoPropria={$this->maoPropria}&nVlValorDeclarado={$this->valorDeclarado}&sCdAvisoRecebimento={$this->avisoRecebimento}&nCdServico={$this->codigo}&nVlDiametro={$this->diametro}&StrRetorno=xml&nIndicaCalculo=3"; 
        
      /*   "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx?nCdEmpresa=&sDsSenha=&sCepOrigem={$CepOrigem}&sCepDestino={$CepDestino}&nVlPeso={$Peso}&nCdFormato={$Formato}&nVlComprimento={$Comprimento}&nVlAltura={$Altura}&nVlLargura={$Largura}&sCdMaoPropria={$MaoPropria}&nVlValorDeclarado={$ValorDeclarado}&sCdAvisoRecebimento={$AvisoRecebimento}&nCdServico={$Codigo}&nVlDiametro={$Diametro}&StrRetorno=xml&nIndicaCalculo=3"
 */
        $frete = simplexml_load_string(file_get_contents($url));

        $this->retorna = $frete;
    }
}