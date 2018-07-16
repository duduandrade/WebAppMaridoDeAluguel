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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use DateTime;

class ProfissionalController extends Controller {

    public $formProfissional;
    public $formCadastroProfissional;

    /**
     * @Route("/procurarprofissional", name="procurarprofissional")
     */
    public function procurarProfissional() {
        $em = $this->getDoctrine()->getManager();

        //s e c sao alias 
        $profissionais = $this->getDoctrine()->getRepository(Profissionais::class)
                ->findAll();
        $enderecos = array();
        foreach ($profissionais as $profissa) {
            $qb = $em->createQueryBuilder();
            $qb->select('p,e')
                    ->from('App\Entity\Enderecoatualprofissional', 'e')
                    ->join('e.profissionaisprofissionais', 'p')
                    ->where($qb->expr()->eq('e.profissionaisprofissionais', $profissa->getIdprofissionais()));
            $result = $qb->getQuery()->getOneOrNullResult();

            $pegarLatLong = 0;
            if ($result != null) {
                $now = new DateTime('now');
                $intervalo = $now->diff($result->getAtualizacao());
                if ($intervalo->format("%i") <= 20) {//pelo menos 20 minutos de atualizacao
                    // $enderecos[] = $result;
                    $enderecos[] = array("teste", $result->getLatitude(), $result->getLongitude(), 1);
                } else {
                    $pegarLatLong = 1;
                }
            } else {
                $pegarLatLong = 1;
            }
            if ($pegarLatLong) {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address='
                        . rawurlencode("" . $profissa->getEnderecoresidencia() . " " . $profissa->getNumero() . " " . $profissa->getCep() . " ".$profissa->getBairro() ));

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                $json = curl_exec($curl);

                curl_close($curl);
                $arrayEndereco = json_decode($json, true);
               // $enderecos[]=$arrayEndereco;
                if ($arrayEndereco["status"]=="OK"){
                  $enderecos[] = array("teste", $arrayEndereco["results"][0]["geometry"]["location"]["lat"], $arrayEndereco["results"][0]["geometry"]["location"]["lng"], 2);
   
                }
            }
        }


        // to get just one result:
        // $product = $qb->setMaxResults(1)->getOneOrNullResult();



        return $this->render('procurarProfissionalMaps.html.twig', array("endereco" => $enderecos));
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
        $profissionalCadastro = new Profissionais();

        $this->formProfissional = $this->createFormBuilder($profissionalCadastro)
                ->add('enderecoresidencia', TextType::class)
                ->add('cep', TextType::class)
                ->add('numero', NumberType::class)
                ->add('bairro', TextType::class)
                ->getForm();

        $this->formCadastroProfissional->handleRequest($request);

        $this->formProfissional->handleRequest($request);
        $data = $request->request->all();



        if (isset($data['form'])) {
            $usuarioCadastro = $this->formCadastroProfissional->getData();
            $profissionalCadastro = $this->formProfissional->getData();
            if (UsuarioController::verificarEmailCadastrado($usuarioCadastro->getEmail(), $this->getDoctrine())) {

                if ((UsuarioController::salvarUsuario($usuarioCadastro, $this->getDoctrine()))) {
                    $objetoUsuario = UsuarioController::buscarUsuarioPorEmail($usuarioCadastro->getEmail(), $this->getDoctrine());
                    if ($objetoUsuario != false) {
                        if ($this->salvarProfissional($objetoUsuario, $profissionalCadastro)) {
                            if ($this->enviarEmailConfirmacao($usuarioCadastro->getEmail())) {
                                return new JsonResponse(array(
                                    'erro' => false,
                                    'mensagem' => 'Profissional cadastrado com sucesso',
                                    'data' => "email enviado"
                                ));
                            } else {
                                return new JsonResponse(array(
                                    'erro' => true,
                                    'mensagem' => 'Profissional cadastrado com sucesso. Email nao enviado.',
                                    'data' => null
                                ));
                            }
                        } else {
                            return new JsonResponse(array(
                                'erro' => true,
                                'mensagem' => 'Falha ao cadastrar profissional, tente novamente',
                                'data' => null
                            ));
                        }
                    } else {
                        return new JsonResponse(array(
                            'erro' => true,
                            'mensagem' => 'Falha ao cadastrar profissional, tente novamente',
                            'data' => null
                        ));
                    }
                } else {
                    return new JsonResponse(array(
                        'erro' => true,
                        'mensagem' => 'Falha ao cadastrar profissional, tente novamente',
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
        return $this->render('cadastrarProfissional.html.twig', array(
                    'form' => $this->formCadastroProfissional->createView(),
                    'formProfisional' => $this->formProfissional->createView()
        ));
    }

    public function salvarProfissional($usuarioCadastro, $profissionalCadastro) {
        $profissional = $profissionalCadastro;
        try {
            $profissional->setUsuariosusuarios($usuarioCadastro);
            $profissional->setSomaavaliacoes(0);
            $profissional->setStatusaprovado(0);
            $sucesso = false;

            $em = $this->getDoctrine()->getManager();
            $em->persist($profissional);
            $em->flush();
            $sucesso = true;
        } catch (\Doctrine\DBAL\DBALException $e) {
            $sucesso = false;
        }
        return $sucesso;
    }

    public function enviarEmailConfirmacao($email) {
        try {

            $retornoUsuario = UsuarioController::buscarUsuarioPorEmail($email, $this->getDoctrine());

            $message = (new \Swift_Message('Bem vindo profissional'))
                    ->setFrom('wepsuporteapp@gmail.com')
                    ->setTo($email)
                    ->setBody(
                    $this->renderView(
                            'emailConfirmacao.html.twig', array('nomeUsuario' => $retornoUsuario->getNome())
                    ), "text/html");
            $this->container->get('mailer')->send($message);
        } catch (Exception $ex) {
            return $ex;
        }
        return true;
    }

}
