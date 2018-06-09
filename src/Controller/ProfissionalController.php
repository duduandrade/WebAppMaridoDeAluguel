<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use App\Entity\Usuarios;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Profissionais;

class ProfissionalController extends Controller {
    public $formCadastroProfissional;
    /**
     * @Route("/procurarprofissional", name="procurarprofissional")
     */
    public function procurarProfissional () {
         return $this->render('procurarProfissionalMaps.html.twig');
    }
    
     /**
     * @Route("/profissional", name="profissional")
     */
    public function profissional(Request $request) {
        $usuarioCadastro = new Usuarios();
        
        $this->formCadastroProfissional = $this->createFormBuilder($usuarioCadastro)
                ->add('nome', TextType::class)
                ->add('senha', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password')))
                ->add('email', EmailType::class)
                ->add('cpf', TextType::class)
                ->add('telefone', TelType::class)
                ->add('cadastrar', SubmitType::class, array('label' => 'Cadastrar'))
                ->getForm();
        $this->formCadastroProfissional->handleRequest($request);

        if ($this->formCadastroProfissional->isSubmitted() && $this->formCadastro->isValid()) {

            $usuarioCadastro = $this->formCadastroProfissional->getData();
          if ( UsuarioController::verificarEmailCadastrado($usuarioCadastro->getEmail())){

            if (UsuarioController::salvarUsuario($usuarioCadastro, $this->getDoctrine()) && $this->salvarProfissional($usuarioCadastro)) {
                
                return new JsonResponse(array(
                    'erro' => false,
                    'mensagem' => 'Profissional cadastrado com sucesso',
                    'data' => null
                ));
            } else {
                return new JsonResponse(array(
                    'erro' => true,
                    'mensagem' => 'Falha ao cadastrar profissional, tente novamente',
                    'data' => null
                ));
            }
          }else{
               return new JsonResponse(array(
                    'erro' => true,
                    'mensagem' => 'Email já cadastrado',
                    'data' => null
                ));
          }
        }
        return $this->render('cadastrarProfissional.html.twig', array(
                    'form' => $this->formCadastroProfissional->createView(),
        ));
    }

    public function salvarProfissional($usuarioCadastro) {
        $profissional = new Profissionais();
        $profissional->setUsuariosusuarios($usuariosusuarios);
        $profissional->setSomaavaliacoes(0);
        $profissional->setStatusaprovado(0);
        $sucesso = false;
        try {
            $em = $this->getDoctrine()->getManager();
            $em->persist($profissional);
            $em->flush();
            $sucesso = true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            $sucesso = false;
        }
        return $sucesso;
    }

}
