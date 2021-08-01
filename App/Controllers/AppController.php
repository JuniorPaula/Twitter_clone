<?php

namespace App\Controllers;


use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action {

    public function timeline() 
    {
     
        $this->validaAutenticacao();
        
        // recuperar os twittes do banco 
        $twitte = Container::getModel('Twitte');
        $twitte->__set('user_id', $_SESSION['id']);
        
        $twittes = $twitte->getAll();
        
        $this->view->twittes = $twittes;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);
        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_twittes =$usuario->getTotalTwitte();
        $this->view->total_seguindo =$usuario->getTotaSeguindo();
        $this->view->total_seguidores =$usuario->getTotaSeguidores(); 

        
        $this->render('timeline');
        
        
        
    }
    
    public function twitte() 
    {
     
        $this->validaAutenticacao();

        $twitte = Container::getModel('Twitte');

        $twitte->__set('twitte', $_POST['twitte']);
        $twitte->__set('user_id', $_SESSION['id']);

        $twitte->salvar();

        header('Location: /timeline');
      

    }

    public function validaAutenticacao() 
    {

        session_start();

        if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=error');
        }

    }

    public function quemSeguir()
    {

        $this->validaAutenticacao();

        $pesquisar = isset($_GET['pesquisar']) ? $_GET['pesquisar'] : '';

        $usuarios = array();

        if($pesquisar != '') {
           $usuario = Container::getModel('Usuario');

           $usuario->__set('nome', $pesquisar);
           $usuario->__set('id', $_SESSION['id']);
           $usuarios = $usuario->getAll();

         
        }

        $this->view->usuarios = $usuarios;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);
        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_twittes =$usuario->getTotalTwitte();
        $this->view->total_seguindo =$usuario->getTotaSeguindo();
        $this->view->total_seguidores =$usuario->getTotaSeguidores(); 
        
        $this->render('quemSeguir');
    
    }

    public function acao()
    {

        $this->validaAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $user_id_following = isset($_GET['user_id']) ? $_GET['user_id'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if($acao == 'seguir') {
            $usuario->seguirUsuario($user_id_following);

        } else if($acao == 'deixar_de_seguir') {
            $usuario->deixarSeguirUsuario($user_id_following);

        } 

        header('Location: /quem_seguir');


    }

    public function deletar()
    {

        $this->validaAutenticacao();
        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $twitte_id= isset($_GET['twitte_id']) ? $_GET['twitte_id'] : '';

        $twitte = Container::getModel('Twitte');
        $twitte->__set('id', $_SESSION['id']);

        if($acao == 'deletar') {
          $twitte->deletarTwitte($twitte_id);

        }

        header('Location: /timeline');

    }

}

?>