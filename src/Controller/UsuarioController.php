<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UsuarioController extends Controller{

    /**
     * @Route("/login", name="login")
     */
    public function login() {
        return $this->render('login.html.twig');
        
    }

}
