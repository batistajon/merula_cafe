<?php

namespace Src\Models;

use Router\Model\Model;

class Comentario extends Model
{
    private $id;
    private $id_usuario;
    private $comentario;
    private $data;

    public function __get($var)
    {
        return $this->$var;
    }

    public function __set($var, $value)
    {
        $this->$var = $value;
    }

    public function salvar()
    {
       $query = "INSERT INTO comentarios(id_usuario, comentario)VALUES(:id_usuario, :comentario)";
       $stmt = $this->db->prepare($query);
       $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
       $stmt->bindValue(':comentario', $this->__get('comentario'));
       $stmt->execute();

       return $this;
    }

    public function getAll()
    {
        $query = 

        "SELECT
            c.id, c.id_usuario, u.nome, c.comentario, DATE_FORMAT(c.data, '%d/%m/%Y %H:%i') AS data
        FROM
            comentarios AS c LEFT JOIN usuarios AS u ON (c.id_usuario = u.id)
        WHERE 
            c.id_usuario = :id_usuario
            OR c.id_usuario IN (SELECT id_usuario_beneficio FROM usuarios_beneficios WHERE id_usuario = :id_usuario)
        ORDER BY
            c.data DESC    
        ";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function delete()
    {
        $query = "DELETE FROM comentarios WHERE id_usuario = :id_usuario AND id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id_usuario'));
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();

        return true;
    }
}