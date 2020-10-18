<?php

namespace Src\Models;

use Router\Model\Model;

class Usuario extends Model
{
    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($var)
    {
        return $this->$var;
    }

    public function __set($var, $value)
    {
        $this->$var = $value;
    }

    /**
     * Salva o usuario no banco de dados
     */
    public function salvar()
    {
        $query = "INSERT INTO usuarios(nome, email, senha, created)VALUES(:nome, :email, :senha, NOW())";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->execute();

        return $this;
    }
    
     /**
      * valida se um cadastro pode ser feito
      */
    public function validarCadastro()
    {
        $valido = true;

        if (strlen($this->__get('nome')) < 3) {
            $valido = false;
        }

        if (strlen($this->__get('email')) < 3) {
            $valido = false;
        }

        if (strlen($this->__get('senha')) < 3) {
            $valido = false;
        }

        return $valido;
    }

      /**
       * Recuperar usuario por email
       */
      public function getUsuarioPorEmail()
      {
          $query = "SELECT nome, email FROM usuarios WHERE email = :email";
          $stmt = $this->db->prepare($query);
          $stmt->bindValue(':email', $this->__get('email'));
          $stmt->execute();

          return $stmt->fetchAll(\PDO::FETCH_ASSOC);
      }

      public function auth()
      {
          $query = "SELECT id, nome, email FROM usuarios WHERE email = :email AND senha = :senha";
          $stmt = $this->db->prepare($query);
          $stmt->bindValue('email', $this->__get('email'));
          $stmt->bindValue('senha', $this->__get('senha'));
          $stmt->execute();

          $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

          if($usuario['id'] != '' && $usuario['nome'] != '') {
              $this->__set('id', $usuario['id']);
              $this->__set('nome', $usuario['nome']);
          }  

          return $this;
      }

      public function getAll()
      {
          $query = "SELECT u.id, u.nome, u.email, (SELECT count(*) FROM usuarios_beneficios AS ub WHERE ub.id_usuario = :id_usuario AND ub.id_usuario_beneficio = u.id) AS fav_sn FROM usuarios AS u WHERE u.nome LIKE :nome AND u.id != :id_usuario";
          $stmt = $this->db->prepare($query);
          $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
          $stmt->bindValue(':id_usuario', $this->__get('id'));
          $stmt->execute();

          return $stmt->fetchAll(\PDO::FETCH_ASSOC);

      }

      public function favBeneficio($id_usuario_beneficio)
      {
        $query = "INSERT INTO usuarios_beneficios(id_usuario, id_usuario_beneficio)VALUES(:id_usuario, :id_usuario_beneficio)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_usuario', $this->__get('id'));
        $stmt->bindValue(':id_usuario_beneficio', $id_usuario_beneficio);
        $stmt->execute();

        return true;

      }

      public function noFavBeneficio($id_usuario_beneficio)
      {
        $query = "DELETE FROM usuarios_beneficios WHERE id_usuario = :id_usuario AND id_usuario_beneficio = :id_usuario_beneficio";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue('id_usuario', $this->__get('id'));
        $stmt->bindValue('id_usuario_beneficio', $id_usuario_beneficio);
        $stmt->execute();

        return true;
      }
}