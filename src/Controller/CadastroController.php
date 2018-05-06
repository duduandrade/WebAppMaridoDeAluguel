<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CadastroController extends Controller{

    /**
     * @Route("/cadastrar", name="cadastrar")
     */
    public function cadastrar() {
        return $this->render('cadastro.html.twig');
        
    }

}
