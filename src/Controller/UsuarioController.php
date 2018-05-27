<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuarios;
use Symfony\Component\HttpFoundation\JsonResponse;

class UsuarioController extends Controller{
    public $formLogin;     
    public $erroAutenticacao;
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request) {
        $usuario = new Usuarios();

            $this->formLogin = $this->createFormBuilder($usuario)
                    ->add('email', TextType::class, array('label' => false))
                    ->add('senha', PasswordType::class, array('label' => false))
                    ->getForm();

            $this->formLogin->handleRequest($request);

        if ($this->formLogin->isSubmitted() && $this->formLogin->isValid()) {
            $usuario = $this->formLogin->getData();
             if ($this->validarAutenticacao($usuario->getEmail(), $usuario->getSenha())) {
                return new JsonResponse(array(
                    'erro' => false,
                    'mensagem' => 'Sucesso',
                    'data' => null
                ));
             }else{
                 return new JsonResponse(array(
                    'erro' => true,
                    'mensagem' => 'Email ou senha incorretos',
                    'data' => null
                ));
             }
        }
        return $this->render('login.html.twig', array(
                    'formLogin' => $this->formLogin->createView(),
        ));
        
    }
    
    public function validarAutenticacao($email, $senhaUsuario){
       //  $senha = hash('sha256', $senhaUsuario);
        $buscaUsuario = $this->getDoctrine()
                ->getRepository(Usuarios::class)
                ->findBy(array('email' => $email, 'senha' => $senhaUsuario));

        if (!$buscaUsuario) {
            $this->erroAutenticacao = "Usuário ou senha inválidos!";
            return false;
        } else {

            $idUser = $buscaUsuario[0]->getIdusuarios();
            $tipoUsuario = $buscaUsuario[0]->getTipousuario();
            $this->get('session')->set('idUsuario', $idUser);
            $this->get('session')->set('tipoUsuario',$tipoUsuario);
            return true;
        }
    }

}
