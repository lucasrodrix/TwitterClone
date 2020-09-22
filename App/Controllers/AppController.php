<?php
    namespace App\Controllers;
    
    use MF\Controller\Action;
    use MF\Model\Container;

    class AppController extends Action{
        public function timeline(){
            $this->validaAutenticacao();
            
            $tweet = Container::getModel('Tweet');            
            $tweet->__set('id_usuario', $_SESSION['id']);
            
            $tweets = $tweet->getAll();

            $this->view->tweets = $tweets;
            
            $usuario = Container::getModel('Usuario');
            $usuario->__set('id', $_SESSION['id']);

            $this->view->infoUser = $usuario->getInfoUser();
            $this->view->totalTweets = $usuario->getTotalTweets();
            $this->view->totalFollows = $usuario->getTotalFollows();
            $this->view->totalFollowers = $usuario->getTotalFollowers();
           
            $this->render('timeline');
        }

        public function tweet(){
            $this->validaAutenticacao();
            
            $tweet = Container::getModel('Tweet');

            $tweet->__set('tweet', $_POST['tweet']);
            $tweet->__set('id_usuario', $_SESSION['id']);
            $tweet->salvar();
            header('Location: /timeline');
        }

        public function validaAutenticacao(){
            session_start();

            if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == ''){
                header('Location: /?login=erro');
            }
        }

        public function quemSeguir(){
            $this->validaAutenticacao();
            $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

            $usuarios = array();
            if($pesquisarPor != ''){
                $usuario = Container::getModel('Usuario');
                $usuario->__set('id', $_SESSION['id']);
                $usuario->__set('nome', $pesquisarPor);
                $usuarios = $usuario->getAll();
            }
            $this->view->usuarios = $usuarios;

            $usuario = Container::getModel('Usuario');
            $usuario->__set('id', $_SESSION['id']);

            $this->view->infoUser = $usuario->getInfoUser();
            $this->view->totalTweets = $usuario->getTotalTweets();
            $this->view->totalFollows = $usuario->getTotalFollows();
            $this->view->totalFollowers = $usuario->getTotalFollowers();

            $this->render('quemSeguir');
        }
        public function acao(){
            $this->validaAutenticacao();

            $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
            $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';

            $usuario = Container::getModel('Usuario');
            $usuario->__set('id', $_SESSION['id']);

            if($acao == 'follow'){
                $usuario->followUser($id_usuario_seguindo);
            }elseif($acao == 'unfollow'){
                $usuario->unfollowUser($id_usuario_seguindo);
            }

            header('Location: /quem_seguir');
        }

        public function removerTweet(){
            $this->validaAutenticacao();
            $id = isset($_GET['id']) ? $_GET['id'] : '';
            $tweet = Container::getModel('Tweet');

            $tweet->__set('id', $id);
            $tweet->deletar();
            header('Location: /timeline');
        }        

    }
?>