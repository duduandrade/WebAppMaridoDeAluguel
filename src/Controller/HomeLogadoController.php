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
        $idUsuario = $this->get('session')->get('idUsuario');
        $tipoUsuario = $this->get('session')->get('tipoUsuario');
        if ($idUsuario != null) {
            $usuario = UsuarioController::buscarUsuarioPorId($idUsuario, $this->getDoctrine());
            if ($usuario != false) {
                if ($tipoUsuario == "P") {
                    
                    return $this->render('homeLogadoProfissional.html.twig', array("nome" => $usuario->getNome()));
                } else {
                    if ($tipoUsuario == "C") {
                        $servicoAndamento = ServicoController::buscarServioEmAndamento($idUsuario, $this->getDoctrine());

                        return $this->render('homeLogado.html.twig', array("nome" => $usuario->getNome(), "servico" => $servicoAndamento));
                    } else {
                        //se nao for cliente nem profissional
                    }
                }
            } else {
                return $this->redirectToRoute("logout");
            }
        } else {
            return $this->redirectToRoute("login");
        }
    }

}
