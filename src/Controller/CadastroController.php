<?php

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
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\Usuarios;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CadastroController extends Controller {

    public $formCadastro;
    public $em; //entityManager

    /**
     * @Route("/cadastrar", name="cadastrar")
     */

    public function cadastrar(Request $request) {
        $usuarioCadastro = new Usuarios();
        $this->formCadastro = $this->createFormBuilder($usuarioCadastro)
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
                ->add('tipousuario', HiddenType::class, array(
                    'data' => 'C'
                ))
                ->add('cadastrar', SubmitType::class, array('label' => 'Cadastrar'))
                ->getForm();
        $this->formCadastro->handleRequest($request);

        if ($this->formCadastro->isSubmitted() && $this->formCadastro->isValid()) {

            $usuarioCadastro = $this->formCadastro->getData();
            if (UsuarioController::verificarEmailCadastrado($usuarioCadastro->getEmail(), $this->getDoctrine())) {

                if (UsuarioController::salvarUsuario($usuarioCadastro, $this->getDoctrine())) {
                    return new JsonResponse(array(
                        'erro' => false,
                        'mensagem' => 'Usuário cadastrado com sucesso',
                        'data' => null
                    ));
                } else {
                    return new JsonResponse(array(
                        'erro' => true,
                        'mensagem' => 'Falha ao cadastrar usuário, tente novamente',
                        'data' => null
                    ));
                }
            } else {
                return new JsonResponse(array(
                    'erro' => true,
                    'mensagem' => 'Email já cadastrado',
                    'data' => null
                ));
            }
        }
        return $this->render('cadastro.html.twig', array(
                    'form' => $this->formCadastro->createView(),
        ));
    }

}
