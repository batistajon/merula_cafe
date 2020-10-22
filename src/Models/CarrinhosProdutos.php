<?php

namespace Src\Models;

use Router\Model\Model;

class CarrinhosProdutos extends Model
{
    private $id;
    private $valor_cotacao;
    private $valor_venda;
    private $qnt_produto;
    private $produto_id;
    private $carrinho_id;
    private $created;
    private $modified;

    public function __get($var)
    {
        return $this->$var;
    }

    public function __set($var, $value)
    {
        $this->$var = $value;
    }

    public function carrinhoCheckout()
    {
        $query = 'SELECT SUM(valor_venda * qnt_produto) AS total_venda, carrinho_id FROM carrinhos_produtos WHERE carrinho_id = :carrinho_id';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':carrinho_id', $this->__get('carrinho_id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getProdutosCarrinho()
    {
        $query = 
		
		'SELECT		
			cp.id, cp.valor_venda, cp.qnt_produto, cp.produto_id, cp.carrinho_id, p.nome_produto
		FROM
			carrinhos_produtos AS cp INNER JOIN produtos AS p ON (p.id = cp.produto_id)
 		WHERE
			carrinho_id = :carrinho_id			
		';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':carrinho_id', $this->__get('carrinho_id'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getFreteProdutosCarrinho()
    {
        $query = 
		
		'SELECT		
			cp.id, p.formato_frete, p.comprimento_frete, p.altura_frete, p.largura_frete, cp.qnt_produto, p.peso_frete, cp.produto_id, cp.carrinho_id
		FROM
			carrinhos_produtos AS cp INNER JOIN produtos AS p ON (p.id = cp.produto_id)
 		WHERE
			carrinho_id = :carrinho_id			
		';

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':carrinho_id', $this->__get('carrinho_id'));
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}    