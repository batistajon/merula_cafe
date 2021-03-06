<?php

namespace Src\Models;

use Router\Model\Model;

class PaymentDb extends Model
{
    private $id;
    private $tipo_pg;
    private $cod_trans;
    private $status;
    private $link_pagamento;
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

	public function saveCheckoutDb($xml)
	{
		$query = 'INSERT INTO pagamentos(tipo_pg, cod_trans, status, link_pagamento, carrinho_id, created)VALUES(:tipo_pg, :cod_trans, :status, :link_pagamento, :carrinho_id, NOW())';

		$stmt = $this->db->prepare($query);
		
        $stmt->bindValue(':tipo_pg', $xml->paymentMethod->type);
		$stmt->bindValue(':cod_trans', $xml->code);
		$stmt->bindValue(':status', $xml->status);
		$stmt->bindValue(':link_pagamento', $xml->paymentLink);
		$stmt->bindValue(':carrinho_id', $xml->reference);
        $stmt->execute();

        return $this;
    }
    
    public function updateNotificationsDb()
    {        
        $query = 'UPDATE pagamentos SET status = :status WHERE carrinho_id = :carrinho_id';

        $stmt = $this->db->prepare($query);

		$stmt->bindValue(':status', $this->__get('status'));
		$stmt->bindValue(':carrinho_id', $this->__get('carrinho_id'));
        $stmt->execute();

        return $this;
    }
}    