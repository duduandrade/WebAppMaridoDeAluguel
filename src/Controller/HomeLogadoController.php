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
            $this->get('session')->set('nomeUsuario', $usuario->getNome());
            if ($usuario != false) {
                if ($tipoUsuario == "P") {
                    $profissional = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());
                    $solicitacoesProf = ServicoController::buscarServicoEmEsperaProfissional($profissional->getIdprofissionais(), $this->getDoctrine());
                    $this->get('session')->set('mostrarCasa', $profissional->getMostrarcasa());
                    $this->get('session')->set('mostrarAtual', $profissional->getMostraratual());
                    $this->get('session')->set('statusDisponivel', $profissional->getStatusdisponivel());

                    return $this->render('solicitacoesEmEsperaProfissional.html.twig', array("solicitacoesProf" => $solicitacoesProf));
                } else {
                    if ($tipoUsuario == "C") {

                        //verificar se o cliente ja tem uma solicitacao em andamento
                        $idUsuario = $this->get('session')->get('idUsuario');

                        $jatem = UsuarioController::jaTemsolicitacaoCliente($this->getDoctrine(), $idUsuario);
                        if ($jatem) {
                            $this->get('session')->set('jatem', true);

                            return $this->redirectToRoute("solicitacoes");
                        } else {
                            $this->get('session')->set('jatem', false);

                            return $this->render('homeLogado.html.twig', array("nome" => $usuario->getNome(), "fixed" => true));
                        }
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
