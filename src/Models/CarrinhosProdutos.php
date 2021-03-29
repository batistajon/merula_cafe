<?php

namespace Src\Models;

use Router\Model\Container;
use Router\Model\Model;
use Src\Models\Product;

class CarrinhosProdutos extends Model
{
    private $id;
    private $nome_produto;
    private $valor_cotacao;
    private $valor_venda;
    private $qnt_produto;
    private $metodo_preparo;
    private $produto_id;
    private $carrinho_id;
    private $created;
    private $modified;

    /* public function __construct()
    {
        session_start();
    } */

    public function __get($var)
    {
        return $this->$var;
    }

    public function __set($var, $value)
    {
        $this->$var = $value;
    }

    public function addProducts()
    {
        session_start();

        /* if($this->__get('produto_id') == 2 || $this->__get('produto_id') == 5) {

            $_SESSION['assinatura'][$this->__get('produto_id')] = [
                'id' => $this->__get('produto_id'),
                'description' => $this->__get('nome_produto'),
                'metodo' => $this->__get('metodo_preparo'),
                'price' => $this->__get('valor_venda'),
                'quantity' => $this->__get('qnt_produto'),
                'total' => $this->__get('valor_venda') * $this->__get('qnt_produto'),
            ];

        } else { */

            $_SESSION['products'][$this->__get('produto_id')] = [
                'id' => $this->__get('produto_id'),
                'description' => $this->__get('nome_produto'),
                'metodo' => $this->__get('metodo_preparo'),
                'price' => $this->__get('valor_venda'),
                'quantity' => $this->__get('qnt_produto'),
                'total' => $this->__get('valor_venda') * $this->__get('qnt_produto'),
            ];
        //}      
    }

    public function updateProductsMore()
    {
        session_start();

       if(isset($_SESSION['products']) && array_key_exists($_POST['id'], $_SESSION['products'])){

            $_SESSION['products'][$_POST['id']]['quantity'] += 1;
            $_SESSION['products'][$_POST['id']]['total'] += $_SESSION['products'][$_POST['id']]['price'];
        }

        print_r(json_encode($_SESSION['products']));
    }

    public function updateProductsLess()
    {
        session_start();

       if(isset($_SESSION['products']) && array_key_exists($_POST['id'], $_SESSION['products'])){

            $_SESSION['products'][$_POST['id']]['quantity'] -= 1;
            $_SESSION['products'][$_POST['id']]['total'] -= $_SESSION['products'][$_POST['id']]['price'];
        }

        if($_SESSION['products'][$_POST['id']]['quantity'] < 0) {
            unset($_SESSION['products'][$_POST['id']]);
        }

        print_r(json_encode($_SESSION['products']));
    }

    public function clearProducts($data)
    {
        session_start();
        print_r(json_encode($data));
        unset($_SESSION['products'][$data['id']]);
    }

    public function endCheckout()
    {
        session_start();
        unset($_SESSION['products']);
    }

    public function getQuantityCart()
    {
        $quantity = 0;

        if(isset($_SESSION['products'])) {
            foreach ($_SESSION['products'] as $product) {
                $quantity += $product['quantity'];
            }
        }

        return $quantity;
    }

    public function getSubtotalCart()
    {
        $subtotal = 0;

        if(isset($_SESSION['products'])) {
            foreach ($_SESSION['products'] as $product) {
                $subtotal +=  $product['total'];
            }

            return $subtotal;
        }

        return $subtotal; 
    }

    public function listSubscribe()
    {
        @session_start();

        $html = "";

        if(@$_SESSION && @$_SESSION['products'] != "") {
            
            foreach ($_SESSION['products'] as $product) {

                if($product['id'] == 2 || $product['id'] == 5) {
                    $html .= "<tr class='trRow' id='trItem{$product['id']}'>";

                    $html .= "<td>{$product['description']}</td>";

                    $html .= "<td id='tdPriceAssinatura{$product['id']}'>".str_replace('.', ',', number_format($product['price'], 2))."</td>";

                    $html .= "<td id='freteAssinatura{$product['id']}'></td>";

                    $html .= "<td id='tdTotalAssinatura{$product['id']}'>".str_replace('.', ',', number_format($product['total'], 2))." / mês</td>";

                    $html .= "</tr>";
                }    
            }
        } 

        return $html;
    }

    public function listSubscribePayment()
    {
        @session_start();

        $html = "";

        if($_SESSION && $_SESSION['products'] != "") {
            
            foreach ($_SESSION['products'] as $product) {

                if($product['id'] == 2 || $product['id'] == 5) {
                    $html .= "<tr id='trItemAssinatura{$product['id']}'>";

                    $html .= "<td id='tdPriceAssinaturaPay{$product['id']}'>".str_replace('.', ',', number_format($product['total'], 2))."</td>";

                    $html .= "<td id='freteAssinaturaPay{$_SESSION['totalAssinatura']['frete']}'>{$_SESSION['totalAssinatura']['frete']}</td>";

                    $html .= "<td id='tdTotalAssinaturaPay{$_SESSION['totalAssinatura']['total']}'>".str_replace('.', ',', number_format($_SESSION['totalAssinatura']['total'], 2))." / mês</td>";

                    $html .= "</tr>";
                }    
            }
        } 

        return $html;
    }

    public function listProducts()
    {
        @session_start();

        $html = "";

        if(@$_SESSION && @$_SESSION['products'] != "") {
            
            foreach ($_SESSION['products'] as $product) {
                $html .= "<tr class='trRow' id='trItem{$product['id']}'>";

                $html .= "<td class='actionIcons'><a data-action='deleteProduct' data-id='{$product['id']}' href='clear-cart'><i class='social-icon-cart fas fa-trash-alt'></i></a></td>";

                $html .= "<td>{$product['description']}</td>";

                $html .= "<td>{$product['metodo']}</td>";

                $html .= "<td id='tdQuantity{$product['id']}'>{$product['quantity']}</td>";

                $html .= "<td>".str_replace('.', ',', number_format($product['price'], 2))."</td>";

                if($product['id'] == 2 || $product['id'] == 5) {

                    $html .= "<td id='tdTotal{$product['id']}'>".str_replace('.', ',', number_format($product['total'], 2))." / mês + frete</td>";

                } else {

                    $html .= "<td id='tdTotal{$product['id']}'>".str_replace('.', ',', number_format($product['total'], 2))."</td>";
                }

                $html .= "<td class='actionIcons'><a data-action='updatePlus' data-id='{$product['id']}' href='add-cart'><i class='social-icon-cart fas fa-plus'></i></a></td>";

                $html .= "<td class='actionIcons'><a data-action='updateLess' data-id='{$product['id']}' href='add-cart'><i class='social-icon-cart fas fa-minus'></i></a></td>";

                $html .= "</tr>";
            }
        } 

        return $html;
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

    public function setCarrinhoByUserId()
    {
        
    }

    public function getCarrinhoByUserId()
    {

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