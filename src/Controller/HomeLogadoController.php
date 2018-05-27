<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;
use Swift_Mailer;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeLogadoController extends Controller {

    /**
     * @Route("/home", name="home")
     */
    public function homeAction(\Swift_Mailer $mailer) {
         $message = (new \Swift_Message('Bem-vindo ao Marido de aluguel'))
        ->setFrom('duduandrade94@gmail.com')
        ->setTo('duduandrade94@gmail.com')
        ->setBody("",'text/plain')
        /*
         * If you also want to include a plaintext version of the message
        ->addPart(
            $this->renderView(
                'emails/registration.txt.twig',
                array('name' => $name)
            ),
            'text/plain'
        )
        */
    ;

    $mailer->send($message);
        return $this->render('homeLogado.html.twig');
    }
   
}
