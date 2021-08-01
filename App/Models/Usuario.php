<?php

namespace App\Models;

use MF\Model\Model;

class Usuario extends Model {
    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($atributo)
    {
       return $this->$atributo;
    }

    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function salvar()
    {
        $query = "INSERT INTO users SET nome = :nome, email = :email, senha = :senha";
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(":nome", $this->__get('nome'));
        $stmt->bindValue(":email", $this->__get('email'));
        $stmt->bindValue(":senha", $this->__get('senha'));
        $stmt->execute();

        return $this;
    }

    public function validarCadastro() 
    {
        $valido = true;

        if(strlen($this->__get('nome')) < 3) {
            $valido = false;
        }

        if(strlen($this->__get('email')) < 3) {
            $valido = false;
        }

        if(strlen($this->__get('senha')) < 3) {
            $valido = false;
        }

        return $valido;
    }

    public function getEmailUsuario()
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt  = $this->db->prepare($query);
        $stmt->bindValue(":email", $this->__get('email'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function autenticar() 
    {

        $query = "SELECT * FROM users WHERE email = :email AND senha = :senha";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':senha', $this->__get('senha'));
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

        $query = "SELECT u.id, u.nome, u.email,
                  (select count(*) from following_users as us 
                    where us.user_id = :user_id and us.user_id_following = u.id) as seguindo_sn 
                  FROM users as u WHERE u.nome LIKE :nome AND u.id != :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':user_id', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function seguirUsuario($user_id_following)
    {

        $query = "INSERT INTO following_users(user_id, user_id_following)VALUES(:user_id, :user_id_following)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $this->__get('id'));
        $stmt->bindValue(':user_id_following', $user_id_following);
        $stmt->execute();

        return true;

    }

    public function deixarSeguirUsuario($user_id_following)
    {

        $query = "DELETE FROM following_users WHERE user_id = :user_id AND user_id_following = :user_id_following";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $this->__get('id'));
        $stmt->bindValue(':user_id_following', $user_id_following);
        $stmt->execute();

        return true;

    }

    public function getInfoUsuario() 
    {

        $query = "SELECT nome FROM users WHERE id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function getTotalTwitte() 
    {

        $query = "SELECT COUNT(*) as total_twitte FROM twittes WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function getTotaSeguindo() 
    {

        $query = "SELECT COUNT(*) as total_seguindo FROM following_users WHERE user_id = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

    public function getTotaSeguidores() 
    {

        $query = "SELECT COUNT(*) as total_seguidores FROM following_users WHERE user_id_following = :user_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $this->__get('id'));
        $stmt->execute();

        return $stmt->fetch(\PDO::FETCH_ASSOC);

    }

}

?>