<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Usuarios;

/**
 * Description of UsuarioController
 *
 * @author DuDu
 */
class UsuarioController extends Controller {

    static function verificarEmailCadastrado($email, $doctrine) {
        $validarEmail = $doctrine->getRepository(Usuarios::class)
                ->findBy(array('email' => $email));

        if (!empty($validarEmail)) {
            return false;
        } else {
            return true;
        }
    }

    static function buscarUsuarioPorEmail($email, $doctrine) {
        $usuarioEmail = $doctrine->getRepository(Usuarios::class)
                ->findOneBy(array('email' => $email));

        if (!$usuarioEmail) {
            return false;
        } else {
            return $usuarioEmail;
        }
    }

    static function salvarUsuario($usuarioCadastro,$doctrine) {
        $sucesso = false;
        try {
            $em = $doctrine->getManager();
            $em->persist($usuarioCadastro);
            $em->flush();
            $sucesso = true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            $sucesso = $e->getMessage();
        }
        return $sucesso;
    }

}
