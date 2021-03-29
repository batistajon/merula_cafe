<?php

namespace Src\Models;

use Router\Model\Model;

class Usuario extends Model
{
    private $id;
    private $nome;
    private $email;
    private $senha;
    private $cpf;
    private $data_nasc;
    private $phoneAreaCode;
    private $phone;
    private $addressStreet;
    private $addressNumber;
    private $addressComplement;
    private $addressDistrict;
    private $addressPostalCode;
    private $addressCity;
    private $addressState;
    private $addressCountry;

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
        $query = "INSERT INTO usuarios(nome, email, senha, cpf, data_nasc, phoneAreaCode, phone, addressStreet, addressNumber, addressComplement, addressDistrict, addressPostalCode, addressCity, addressState, addressCountry, created)VALUES(:nome, :email, :senha, :cpf, :data_nasc, :phoneAreaCode, :phone, :addressStreet, :addressNumber, :addressComplement, :addressDistrict, :addressPostalCode, :addressCity, :addressState, :addressCountry, NOW())";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
        $stmt->bindValue(':cpf', $this->__get('cpf'));
        $stmt->bindValue(':data_nasc', $this->__get('data_nasc'));
        $stmt->bindValue(':phoneAreaCode', $this->__get('phoneAreaCode'));
        $stmt->bindValue(':phone', $this->__get('phone'));
        $stmt->bindValue(':addressStreet', $this->__get('addressStreet'));
        $stmt->bindValue(':addressNumber', $this->__get('addressNumber'));
        $stmt->bindValue(':addressComplement', $this->__get('addressComplement'));
        $stmt->bindValue(':addressDistrict', $this->__get('addressDistrict'));
        $stmt->bindValue(':addressPostalCode', $this->__get('addressPostalCode'));
        $stmt->bindValue(':addressCity', $this->__get('addressCity'));
        $stmt->bindValue(':addressState', $this->__get('addressState'));
        $stmt->bindValue(':addressCountry', $this->__get('addressCountry'));
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
        $query = "SELECT id, nome, email, senha, cpf, data_nasc, phoneAreaCode, phone, addressStreet, addressNumber, addressComplement, addressDistrict, addressPostalCode, addressCity, addressState, addressCountry FROM usuarios WHERE email = :email AND senha = :senha";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue('email', $this->__get('email'));
        $stmt->bindValue('senha', $this->__get('senha'));
        $stmt->execute();

        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($usuario['id'] != '' && $usuario['nome'] != '') {

            $this->__set('id', $usuario['id']);
            $this->__set('nome', $usuario['nome']);
            $this->__set('email', $usuario['email']);
            $this->__set('cpf', $usuario['cpf']);
            $this->__set('data_nasc', $usuario['data_nasc']);
            $this->__set('phoneAreaCode', $usuario['phoneAreaCode']);
            $this->__set('phone', $usuario['phone']);
            $this->__set('addressStreet', $usuario['addressStreet']);
            $this->__set('addressNumber', $usuario['addressNumber']);
            $this->__set('addressComplement', $usuario['addressComplement']);
            $this->__set('addressDistrict', $usuario['addressDistrict']);
            $this->__set('addressPostalCode', $usuario['addressPostalCode']);
            $this->__set('addressCity', $usuario['addressCity']);
            $this->__set('addressState', $usuario['addressState']);
            $this->__set('addressCountry', $usuario['addressCountry']);

        } else {

            return false;
        }

        return $this;
      }

      public function startSessionUser()
      {
        session_start();

        $_SESSION['user'] = [
            'id' => $this->__get('id'),
            'nome' => $this->__get('nome'),
            'email' => $this->__get('email'),
            'cpf' => $this->__get('cpf'),
            'data_nasc' => $this->__get('data_nasc'),
            'phoneAreaCode' => $this->__get('phoneAreaCode'),
            'phone' => $this->__get('phone'),
            'addressStreet' => $this->__get('addressStreet'),
            'addressNumber' => $this->__get('addressNumber'),
            'addressComplement' => $this->__get('addressComplement'),
            'addressDistrict' => $this->__get('addressDistrict'),
            'addressPostalCode' => $this->__get('addressPostalCode'),
            'addressCity' => $this->__get('addressCity'),
            'addressState' => $this->__get('addressState'),
            'addressCountry' => $this->__get('addressCountry')
        ];

        print_r(json_encode($_SESSION));
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