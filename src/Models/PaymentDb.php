<?php

namespace Src\Models;

use Router\Model\Model;

class PaymentDb extends Model
{
    private $id;
    private $tipo_pg;
    private $cod_trans;
    private $status;
    private $link_boleto;
    private $link_db_online;
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

    /**
     * Salva as transacoes do pagseguro no banco de dados
     */
	public function saveCheckoutDb()
	{
		$query = 'INSERT INTO pagamentos(tipo_pg, cod_trans, status, link_boleto, carrinho_id, created)VALUES(:tipo_pg, :cod_trans, :status, :link_boleto, :carrinho_id, NOW())';

		$stmt = $this->db->prepare($query);
		
        $stmt->bindValue(':tipo_pg', $this->tipo_pg);
		$stmt->bindValue(':cod_trans', $this->cod_trans);
		$stmt->bindValue(':status', $this->status);
		$stmt->bindValue(':link_boleto', $this->link_boleto);
		$stmt->bindValue(':carrinho_id', $this->carrinho_id);
        $stmt->execute();
	}
}    