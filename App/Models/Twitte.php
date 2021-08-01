<?php

namespace App\Models;

use MF\Model\Model;

class Twitte extends Model {

    private $id;
    private $user_id;
    private $twitte;
    private $data;

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

        $query = "INSERT INTO twittes(user_id, twitte)value(:user_id, :twitte)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $this->__get('user_id'));
        $stmt->bindValue(':twitte', $this->__get('twitte'));
        $stmt->execute();

        return $this;

    }

    public function getAll()
    {

        $query = "SELECT  t.id, t.user_id, u.nome, t.twitte, 
                  DATE_FORMAT(t.data, '%d/%m/%Y %H:%i') as data 
                  FROM twittes as t LEFT JOIN users as u on(t.user_id = u.id) 
                  WHERE user_id = :user_id OR t.user_id in (select user_id_following from following_users where user_id = :user_id) 
                  ORDER BY t.data DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $this->__get('user_id'));
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);

    }

    public function deletarTwitte($twitte_id) 
    {

        $query = "DELETE FROM twittes WHERE user_id = :user_id AND id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':user_id', $this->__get('id'));
        $stmt->bindValue(':id', $twitte_id);
        $stmt->execute();

        return true;

    }

}