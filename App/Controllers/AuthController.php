<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action
{
    /*
    |------------------------------------------------------------------------------------------------------------------
    | Autenticar
    |------------------------------------------------------------------------------------------------------------------
    */

    public function autenticar()
    {
        $usuario = Container::getModel('Usuario');
        $usuario->__set('email', $_POST['email']);
        $usuario->__set('senha', md5($_POST['senha']));

        $usuario->autenticar();

        if (!empty(!empty($usuario->__get('id')) && !empty($usuario->__get('nome')))) {
            session_start();
            $_SESSION['id'] = $usuario->__get('id');
            $_SESSION['nome'] = $usuario->__get('nome');

            header("Location: /timeline");
            exit;
        } else {
            header("Location: /?login=erro");
            exit;
        }
    }

    /*
    |------------------------------------------------------------------------------------------------------------------
    | Sair
    |------------------------------------------------------------------------------------------------------------------
    */

    public function sair()
    {
        session_start();
        session_destroy();
        
        header("Location: /");
        exit;
    }
}
