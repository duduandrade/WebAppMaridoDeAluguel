<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Usuarios;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swift_Message;
use Swift_SmtpTransport;
use Swift_Mailer;

class AutenticacaoController extends Controller {

    public $formLogin;
    public $erroAutenticacao;

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request) {
        if ($this->get('session')->get('idUsuario')) {

            return $this->redirectToRoute('home');
        } else {
            $usuario = new Usuarios();

            $this->gerarFormulario($usuario);
            $this->formLogin->handleRequest($request);

            if ($this->formLogin->isSubmitted() && $this->formLogin->isValid()) {
                $usuario = $this->formLogin->getData();
                if ($this->validarAutenticacao($usuario->getEmail(), $usuario->getSenha())) {
                    return new JsonResponse(array(
                        'erro' => false,
                        'mensagem' => 'Sucesso',
                        'data' => null
                    ));
                } else {
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
    }

    public function gerarFormulario($usuario) {
        $this->formLogin = $this->createFormBuilder($usuario)
                ->add('email', TextType::class, array('label' => false))
                ->add('senha', PasswordType::class, array('label' => false))
                ->getForm();
    }

    public function validarAutenticacao($email, $senhaUsuario) {
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
            $this->get('session')->set('tipoUsuario', $tipoUsuario);
            return true;
        }
    }

    public function enviarEmail(\Swift_Mailer $mailer) {

        $message = (new \Swift_Message('Bem-vindo ao Marido de aluguel'))
                ->setFrom('duduandrade94@gmail.com')
                ->setTo('duduandrade94@gmail.com')
                ->setBody("", 'text/plain')
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
    }

    /**
     * @Route("/logout")
     */
    public function logout() {

        $this->get('session')->invalidate();
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/esquecisenha")
     */
    public function esqueciSenha(Request $request) {
        $usuario = new Usuarios;
        $this->gerarFormulario($usuario);
        $this->formLogin->handleRequest($request);

        if ($this->formLogin->isSubmitted()) {
            $usuario = $this->formLogin->getData();

            if (UsuarioController::verificarEmailCadastrado($usuario->getEmail(), $this->getDoctrine())) {
                if ($this->enviarEmailNovaSenha($usuario->getEmail())) {
                    return new JsonResponse(array(
                        'erro' => false,
                        'mensagem' => 'E-mail enviado com sucesso.',
                        'data' => null
                    ));
                } else {
                    return new JsonResponse(array(
                        'erro' => true,
                        'mensagem' => 'Falha ao enviar e-mail',
                        'data' => null
                    ));
                }
            } else {
                return new JsonResponse(array(
                    'erro' => true,
                    'mensagem' => 'E-mail não cadastrado',
                    'data' => null
                ));
            }
        }
    }

    public function enviarEmailNovaSenha($email) {
        try {
            $link = $this->gerarLinkNovaSenha($email);

            $retornoUsuario = UsuarioController::buscarUsuarioPorEmail($email, $this->getDoctrine());

            $message = (new \Swift_Message('Esqueci minha senha'))
                    ->setFrom('wepsuporteapp@gmail.com')
                    ->setTo($email)
                    ->setBody(
                    $this->renderView(
                            'emailNovaSenha.html.twig', array('link' => $link, 'nomeUsuario' => $retornoUsuario->getNome())
                    ), "text/html");
            $this->container->get('mailer')->send($message);
        } catch (Exception $ex) {
            return $ex;
        }
        return true;
    }

    public function gerarLinkNovaSenha($email) {
        $rota = 'changepassword';
        $sistema = $this->container->getParameter('url_sistema');

        $hashEmail = base64_encode($email);
        $link = $sistema . '/' . $rota . '/' . $hashEmail . '';
        return $link;
    }

    /**
     * @Route("/changepassword/{email}")
     */
    public function trocarSenha(Request $request, $email) {
        $emailDescriptografado = base64_decode($email);
        $retornoUsuario = UsuarioController::buscarUsuarioPorEmail($emailDescriptografado, $this->getDoctrine());
        if ($retornoUsuario) {
            $formNovaSenha = $this->createFormBuilder($retornoUsuario)
                    ->add('senha', RepeatedType::class, array(
                        'type' => PasswordType::class,
                        'invalid_message' => 'The password fields must match.',
                        'options' => array('attr' => array('class' => 'password-field')),
                        'required' => true,
                        'first_options' => array('label' => 'Password'),
                        'second_options' => array('label' => 'Repeat Password')))
                    ->getForm();

            $formNovaSenha->handleRequest($request);

            if ($formNovaSenha->isSubmitted() && $formNovaSenha->isValid()) {
                if ($this->atualizarSenha($retornoUsuario)) {
                    return new JsonResponse(array(
                        'erro' => false,
                        'mensagem' => 'Senha alterada com sucesso.',
                        'data' => null
                    ));
                } else {
                    return new JsonResponse(array(
                        'erro' => true,
                        'mensagem' => 'Falha ao alterar senha.',
                        'data' => null
                    ));
                }
            }
            return $this->render('novaSenha.html.twig', array(
                        'formNovaSenha' => $formNovaSenha->createView(),
            ));
        } else {
            return $this->render('novaSenha.html.twig', array(
                        'erro' => true));
        }
    }

    function atualizarSenha($usuario) {
        $sucesso = false;
        try {
            $this->em = $this->getDoctrine()->getManager();
            $this->em->persist($usuario);
            $this->em->flush();
            $sucesso = true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            $sucesso = false;
        }
        return $sucesso;
    }

}
