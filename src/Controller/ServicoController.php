<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ServicoController extends Controller{

    /**
     * @Route("/solicitar", name="solicitarProfissional")
     */
    public function solicitarProfissional() {
        return $this->render('solicitarServico.html.twig');
        
    }

}
