<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action
{

    /*
    |------------------------------------------------------------------------------------------------------------------
    | Timeline
    |------------------------------------------------------------------------------------------------------------------
    */

    public function timeline()
    {

        $this->validaAutenticacao();
        // Recuperação dos tweets
        $tweet = Container::getModel('Tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);

        // variáveis de paginação
        $totalRegistrosPagina = 5;
        $pagina = isset($_GET['pagina']) ? $_GET['pagina'] : 1;
        $deslocamento = ($pagina - 1) * $totalRegistrosPagina;

        $tweets = $tweet->getPorPagina($totalRegistrosPagina, $deslocamento);
        $total_tweets = $tweet->getTotalRegistros();

        $this->view->totalDePaginas = ceil($total_tweets['total'] / $totalRegistrosPagina);
        $this->view->pagina_ativa = $pagina;

        $this->view->tweets = $tweets;


        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('timeline');
    }

    /*
    |------------------------------------------------------------------------------------------------------------------
    | Tweet
    |------------------------------------------------------------------------------------------------------------------
    */

    public function tweet()
    {

        $this->validaAutenticacao();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('tweet', $_POST['tweet']);
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar();

        header('Location: /timeline');
        exit;
    }

    /*
    |------------------------------------------------------------------------------------------------------------------
    | Valida autenticação
    |------------------------------------------------------------------------------------------------------------------
    */

    public function validaAutenticacao()
    {
        session_start();

        if (!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=erro');
            exit;
        }
    }

    /*
    |------------------------------------------------------------------------------------------------------------------
    | Quem seguir
    |------------------------------------------------------------------------------------------------------------------
    */

    public function quemSeguir()
    {
        $this->validaAutenticacao();

        $pesquisarPor = isset($_GET['pesquisar-por']) ? $_GET['pesquisar-por'] : '';

        $usuarios = array();

        if ($pesquisarPor != '') {

            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome', $pesquisarPor);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios = $usuario->getAll();
        }

        $tweet = Container::getModel('Tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweets = $tweet->getAll();

        $this->view->tweets = $tweets;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->view->usuarios = $usuarios;

        $this->render('quemSeguir');
    }

    /*
    |------------------------------------------------------------------------------------------------------------------
    | Ação
    |------------------------------------------------------------------------------------------------------------------
    */

    public function acao()
    {
        $this->validaAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $idUsuarioSeguindo = isset($_GET['id-usuario']) ? $_GET['id-usuario'] : '';

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        if ($acao == 'seguir') {
            $usuario->seguirUsuario($idUsuarioSeguindo);
        } else if ($acao == 'deixar-de-seguir') {
            $usuario->deixarSeguirUsuario($idUsuarioSeguindo);
        }

        header('Location: /quem-seguir');
        exit;
    }

    /*
    |------------------------------------------------------------------------------------------------------------------
    | Deletar tweet
    |------------------------------------------------------------------------------------------------------------------
    */

    public function deletarTweet()
    {
        $this->validaAutenticacao();

        $idTweet = isset($_GET['id_tweet']) ? $_GET['id_tweet'] : null;

        if ($idTweet != null && filter_var($idTweet, FILTER_VALIDATE_INT)) {
            $tweet = Container::getModel('Tweet');
            $tweet->deletarTweet($_GET['id_tweet']);
        }

        header('Location: /timeline');
        exit;
    }
}
