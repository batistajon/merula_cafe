<?php

namespace Src\Models;

use Router\Model\Model;

class Product extends Model
{
    private $id;
    private $nome_produto;
    private $valor_compra;
    private $valor_venda;

    public function __get($var)
    {
        return $this->$var;
    }

    public function __set($var, $value)
    {
        $this->$var = $value;
    }

    public function getValorVenda()
    {
        $query = "SELECT valor_venda FROM produtos WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}    