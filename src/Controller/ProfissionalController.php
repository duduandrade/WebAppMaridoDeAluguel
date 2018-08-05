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
use \Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Entity\Usuarios;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Profissionais;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use DateTime;
use DateTimeZone;
use App\Entity\Enderecoatualprofissional;

class ProfissionalController extends Controller {

    public $formProfissional;
    public $formCadastroProfissional;

    /**
     * @Route("/procurarprofissional", name="procurarprofissional")
     */
    public function procurarProfissional() {
        $idUsuario = $this->get('session')->get('idUsuario');
        $tipoUsuario = $this->get('session')->get('tipoUsuario');
        if ($idUsuario != null) {
            $em = $this->getDoctrine()->getManager();
            $request = $this->container->get('request_stack')->getCurrentRequest();
            $session = $request->getSession();
            if ($session->has('idservicosolicitado') && $session->has('quantidade')) {
                $qbProf1 = $em->createQueryBuilder('s');

                $nots = $qbProf1->select('pp.idprofissionais')
                        ->from('App\Entity\Solicitacoes', 's')
                        ->join('s.profissionaisprofissionais', 'pp')
                        ->where($qbProf1->expr()->neq('s.statussolicitacao', 9))//só se ja foi cancelada
                        ->andWhere($qbProf1->expr()->neq('s.statussolicitacao', 1))//ou se ele ainda nao aceitou
                        ->andWhere($qbProf1->expr()->neq('s.statussolicitacao', 8)); //ou se ja foi finalizada


                $qbProf = $em->createQueryBuilder();

                $qbProf->select('p,u')
                        ->from('App\Entity\Profissionais', 'p')
                        ->join('p.usuariosusuarios', 'u')
                        ->where($qbProf->expr()->notIn('p.idprofissionais', $nots->getDQL()))
                        ->andWhere($qbProf->expr()->eq('p.statusdisponivel', 1));

                $profissionais = $qbProf->getQuery()->execute();

                $enderecos = array();
                foreach ($profissionais as $profissa) {
                    if ($profissa->getMostraratual()) {
                        $qb = $em->createQueryBuilder();
                        $qb->select('p,e')
                                ->from('App\Entity\Enderecoatualprofissional', 'e')
                                ->join('e.profissionaisprofissionais', 'p')
                                ->where($qb->expr()->eq('e.profissionaisprofissionais', $profissa->getIdprofissionais()));
                        $result = $qb->getQuery()->getOneOrNullResult();

                        $pegarLatLong = 0;
                        if ($result != null) {
                            $UTC = new DateTimeZone("UTC");
                            $newTZ = new DateTimeZone("America/Sao_Paulo");

                            $now = new DateTime('now', $UTC);
                            $now->setTimezone($newTZ);
                            $intervalo = $now->diff($result->getAtualizacao());
                            if ($intervalo->format("%i") <= 10) {//pelo menos 10 minutos de atualizacao
                                // $enderecos[] = $result;
                                $enderecos[] = array("atual",
                                    $result->getLatitude(),
                                    $result->getLongitude(),
                                    $profissa->getIdprofissionais(),
                                    $profissa->getEnderecoresidencia() . ", " . $profissa->getNumero(),
                                    $profissa);
                            }
                        }
                    }

                    if ($profissa->getMostrarcasa()) {
                        if ($profissa->getLatend() == null && $profissa->getLngend() == null) {

                            $curl = curl_init();
                            curl_setopt($curl, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address='
                                    . rawurlencode("" . $profissa->getNumero() . " " . $profissa->getEnderecoresidencia() . ", " . $profissa->getCep() . " " . $profissa->getBairro() . "&key" . $this->container->getParameter('key_maps')));

                            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                            $json = curl_exec($curl);


                            $arrayEndereco = json_decode($json, true);
                            // $enderecos[]=$arrayEndereco;
                            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                            if ($httpcode == 200) {
                                if ($arrayEndereco["status"] == "OK") {
                                    $enderecos[] = array("casa",
                                        $arrayEndereco["results"][0]["geometry"]["location"]["lat"],
                                        $arrayEndereco["results"][0]["geometry"]["location"]["lng"],
                                        $profissa->getIdprofissionais(),
                                        $profissa->getEnderecoresidencia() . ", " . $profissa->getNumero(),
                                        $profissa);
                                }
                            }
                            curl_close($curl);
                        } else {
                            $enderecos[] = array("casa",
                                $profissa->getLatend(),
                                $profissa->getLngend(),
                                $profissa->getIdprofissionais(),
                                $profissa->getEnderecoresidencia() . ", " . $profissa->getNumero(),
                                $profissa);
                        }
                    }
                    $servicos = ServicoController::buscarSolicitacoesConcluidasProfAvaliadas($profissa->getIdprofissionais(), $this->getDoctrine());
                    if (!$servicos) {
                        $estrelas[$profissa->getIdprofissionais()] = 0;
                    } else {
                        $totalServicos = count($servicos);
                        $estrelas[$profissa->getIdprofissionais()] = $profissa->getSomaavaliacoes() / $totalServicos;
                    }
                }
                return $this->render('procurarProfissionalMaps.html.twig', array("endereco" => $enderecos, "profissionais" => $profissionais, "estrelas" => $estrelas));
            } else {
                return $this->redirectToRoute("solicitar");
            }
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/editarPerfil", name="editarPerfil")
     */
    public function editarPerfil() {
        $idUsuario = $this->get('session')->get('idUsuario');
        if ($idUsuario != null) {
            $profissional = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());
            $usuario = UsuarioController::buscarUsuarioPorId($idUsuario, $this->getDoctrine());
            return $this->render('editarPerfilProfissional.html.twig', array("perfil" => $profissional, "usuario" => $usuario));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/uploadFotoProfissional", name="uploadFotoProfissional")
     */
    public function uploadFotoProfissional(Request $request) {
        $idUsuario = $this->get('session')->get('idUsuario');
        if ($idUsuario != null) {
            $target_dir = "../public/img/prof/";
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
// Check if image file is a actual image or fake image
            if (isset($_POST["submit"])) {
                $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
                if ($check !== false) {
                    echo "File is an image - " . $check["mime"] . ".";
                    $uploadOk = 1;
                } else {
                    echo "File is not an image.";
                    $uploadOk = 0;
                }
            }
// Check if file already exists
            if (file_exists($target_file)) {
                echo "Sorry, file already exists.";
                $uploadOk = 0;
            }
// Check file size
            if ($_FILES["fileToUpload"]["size"] > 500000) {
                echo "Sorry, your file is too large.";
                $uploadOk = 0;
            }
// Allow certain file formats
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                $uploadOk = 0;
            }
// Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
            } else {
                $temp = explode(".", $_FILES["fileToUpload"]["name"]);
                $newfilename = round(microtime(true)) . '.' . end($temp);
                $target_file = $target_dir . $newfilename;
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    echo "The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.";
                    $profissional = UsuarioController::buscarUsuarioPorId($idUsuario, $this->getDoctrine());
                    $em = $this->getDoctrine()->getManager();
                    $profissional->setFoto($newfilename);
                    $em->persist($profissional);
                    $em->flush();
                    return $this->redirectToRoute("editarPerfil");
                } else {
                    echo "Sorry, there was an error uploading your file.";
                }
            }
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/atualizarPerfilProf", name="atualizarPerfilProf")
     */
    public function atualizarPerfilProf(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $idUsuario = $this->get('session')->get('idUsuario');
                $usuario = UsuarioController::buscarUsuarioPorId($idUsuario, $this->getDoctrine());
                $usuario->setNome($data['nome']);
                $usuario->setCpf($data['cpf']);
                $usuario->setTelefone($data['telefone']);
                $em = $this->getDoctrine()->getManager();
                $em->persist($usuario);

                $profissional = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());
                $profissional->setEnderecoresidencia($data['enderecoresidencia']);
                $profissional->setNumero($data['numero']);
                $profissional->setBairro($data['bairro']);
                $cepFormatado = str_replace(" ", "_", preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($data['cep']))));

                $profissional->setCep($cepFormatado);

                $em->persist($profissional);
//salvar as coordenadas do endereco
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address='
                        . rawurlencode("" . $profissional->getNumero() . "+" . $profissional->getEnderecoresidencia() . "," . $cepFormatado . " " . $profissional->getBairro() . "&key" . $this->container->getParameter('key_maps')));

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

                $json = curl_exec($curl);


                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($httpcode == 200) {
                    $arrayEndereco = json_decode($json, true);
                    // $enderecos[]=$arrayEndereco;
                    if ($arrayEndereco["status"] == "OK") {

                        $profissional->setLatend($arrayEndereco["results"][0]["geometry"]["location"]["lat"]);
                        $profissional->setLngend($arrayEndereco["results"][0]["geometry"]["location"]["lng"]);
                        $em->persist($profissional);
                        curl_close($curl);
                    }
                }
                $em->flush();
                return new JsonResponse(array(
                    'erro' => false,
                    'mensagem' => $data,
                    'data' => null
                ));
            } else {
                return $this->redirectToRoute("login");
            }
        } else {
            return new JsonResponse(array(
                'erro' => true,
                'mensagem' => 'nao é json',
                'data' => null
            ));
        }
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
                ->add('tipousuario', HiddenType::class, array(
                    'data' => 'P'
                ))
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
                                'mensagem' => 'Falha ao cadastrar profissional, tente novamente.',
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
            //salvar as coordenadas do endereco
            $curl = curl_init();
            $cepFormatado = str_replace(" ", "_", preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($profissional->getCep()))));
            $profissional->setCep($cepFormatado);
            curl_setopt($curl, CURLOPT_URL, 'https://maps.googleapis.com/maps/api/geocode/json?address='
                    . rawurlencode("" . $profissional->getNumero() . "+" . $profissional->getEnderecoresidencia() . "," . $cepFormatado . " " . $profissional->getBairro() . "&key" . $this->container->getParameter('key_maps')));

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

            $json = curl_exec($curl);


            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpcode == 200) {
                $arrayEndereco = json_decode($json, true);
                // $enderecos[]=$arrayEndereco;
                if ($arrayEndereco["status"] == "OK") {

                    $profissional->setLatend($arrayEndereco["results"][0]["geometry"]["location"]["lat"]);
                    $profissional->setLngend($arrayEndereco["results"][0]["geometry"]["location"]["lng"]);
                    $em->persist($profissional);
                    curl_close($curl);
                    $sucesso = true;
                }
            } else {
                $sucesso = false;
            }
            $em->flush();
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

    /**
     * @Route("/localAtual", name="localAtual")
     */
    public function localAtual(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $idUsuario = $this->get('session')->get('idUsuario');

                $em = $this->getDoctrine()->getManager();


                $qbProf = $em->createQueryBuilder();
                $qbProf->select('p,u')
                        ->from('App\Entity\Profissionais', 'p')
                        ->join('p.usuariosusuarios', 'u')
                        ->where($qbProf->expr()->eq('p.usuariosusuarios', $idUsuario));

                $resultProfissional = $qbProf->getQuery()->getOneOrNullResult();

                if ($resultProfissional != null) {
                    $UTC = new DateTimeZone("UTC");
                    $newTZ = new DateTimeZone("America/Sao_Paulo");

                    $now = new DateTime('now', $UTC);
                    $now->setTimezone($newTZ);

                    //ver se ele ja tem um registro de endereco atual senao cria
                    $objetoLocalAtual = $this->getDoctrine()->getRepository(Enderecoatualprofissional::class)
                            ->findOneBy(array('profissionaisprofissionais' => $resultProfissional->getIdprofissionais()));

                    if ($objetoLocalAtual != null) {

                        $qb = $em->createQueryBuilder();

                        $result = $qb->update('App\Entity\Enderecoatualprofissional', 'e')
                                        ->set('e.latitude', $qb->expr()->literal($data['lat']))
                                        ->set('e.longitude', $qb->expr()->literal($data['lng']))
                                        ->set('e.endereco', $qb->expr()->literal($data['endereco']))
                                        ->set('e.atualizacao', $qb->expr()->literal($now->format('Y-m-d H:i:s')))
                                        ->where('e.profissionaisprofissionais = ?1')
                                        ->setParameter(1, $qb->expr()->literal($resultProfissional->getIdprofissionais()))
                                        ->getQuery()->getSingleScalarResult();
                        if ($result != null) {
                            return new JsonResponse(array(
                                'erro' => false,
                                'mensagem' => '',
                                'data' => null
                            ));
                        } else {
                            return new JsonResponse(array(
                                'erro' => true,
                                'mensagem' => 'Falha ao atualizar localizacao',
                                'data' => null
                            ));
                        }
                    } else {
                        $profissional = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());
                        $em = $this->getDoctrine()->getManager();

                        $novaLocalAtual = new Enderecoatualprofissional();
                        $novaLocalAtual->setLatitude($data['lat']);
                        $novaLocalAtual->setLongitude($data['lng']);
                        $novaLocalAtual->setAtualizacao($now);
                        $novaLocalAtual->setEndereco($data['endereco']);
                        $novaLocalAtual->setProfissionaisprofissionais($profissional);

                        $em->persist($novaLocalAtual);
                        $em->flush();

                        return new JsonResponse(array(
                            'erro' => false,
                            'mensagem' => '',
                            'data' => null
                        ));
                    }
                } else {
                    return new JsonResponse(array(
                        'erro' => true,
                        'mensagem' => 'Profissional nao encontrado',
                        'data' => null
                    ));
                }
            } else {
                return $this->redirectToRoute("login");
            }
        } else {
            return new JsonResponse(array(
                'erro' => true,
                'mensagem' => 'Formato invalido',
                'data' => null
            ));
        }
    }

    static public function buscarProfissionalPorId($idProfissional, $doctrine) {
        $objetoProfissional = $doctrine->getRepository(Profissionais::class)
                ->findOneBy(array('idprofissionais' => $idProfissional));

        if (!$objetoProfissional) {
            return false;
        } else {
            return $objetoProfissional;
        }
    }

    static public function buscarProfissionalPorIdUsuario($idUsuario, $doctrine) {
        $objetoProfissional = $doctrine->getRepository(Profissionais::class)
                ->findOneBy(array('usuariosusuarios' => $idUsuario));

        if (!$objetoProfissional) {
            return false;
        } else {
            return $objetoProfissional;
        }
    }

    /**
     * @Route("/mostrarMinhaLocalAtual", name="mostrarMinhaLocalAtual")
     */
    public function mostrarMinhaLocalAtual(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $em = $this->getDoctrine()->getManager();
                $idUsuario = $this->get('session')->get('idUsuario');
                $profissional = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());
                $profissional->setMostraratual($data['permissaoLocalAtual']);
                $em->persist($profissional);
                $em->flush();
                $this->get('session')->set('mostrarAtual', $data['permissaoLocalAtual']);

                return new JsonResponse(array(
                    'erro' => false,
                    'mensagem' => '',
                    'data' => null
                ));
            } else {
                return $this->redirectToRoute('login');
            }
        }
    }

    /**
     * @Route("/mostrarMinhaCasa", name="mostrarMinhaCasa")
     */
    public function mostrarMinhaCasa(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $em = $this->getDoctrine()->getManager();
                $idUsuario = $this->get('session')->get('idUsuario');
                $profissional = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());
                $profissional->setMostrarcasa($data['permissaoCasa']);
                $em->persist($profissional);
                $em->flush();
                $this->get('session')->set('mostrarCasa', $data['permissaoCasa']);

                return new JsonResponse(array(
                    'erro' => false,
                    'mensagem' => '',
                    'data' => null
                ));
            } else {
                return $this->redirectToRoute('login');
            }
        }
    }

    /**
     * @Route("/salvarAvaliacaoCliente", name="salvarAvaliacaoCliente")
     */
    public function salvarAvaliacaoCliente(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $em = $this->getDoctrine()->getManager();
                $idUsuario = $this->get('session')->get('idUsuario');
                $solicitacao = ServicoController::buscarSolicitacaoPorId($data['solicitacaoId'], $this->getDoctrine());
                $solicitacao->setAvaliacao($data['estrelas']);
                $prof = $solicitacao->getProfissionaisprofissionais();
                $profissional = ProfissionalController::buscarProfissionalPorId($prof, $this->getDoctrine());
                $profissional->setSomaavaliacoes($profissional->getSomaavaliacoes() + $data['estrelas']);
                $em->persist($solicitacao);
                $em->persist($profissional);
                $em->flush();

                return new JsonResponse(array(
                    'erro' => false,
                    'mensagem' => '',
                    'data' => null
                ));
            } else {
                $this->redirectToRoute("login");
            }
        }
    }

    /**
     * @Route("/alterarStatusDisponivel", name="alterarStatusDisponivel")
     */
    public function alterarStatusDisponivel(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $em = $this->getDoctrine()->getManager();
                $idUsuario = $this->get('session')->get('idUsuario');
                $profissional = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());
                $profissional->setStatusdisponivel($data['statusDisponivel']);
                $em->persist($profissional);
                $em->flush();
                $this->get('session')->set('statusDisponivel', $data['statusDisponivel']);

                return new JsonResponse(array(
                    'erro' => false,
                    'mensagem' => '',
                    'data' => null
                ));
            } else {
                return $this->redirectToRoute('login');
            }
        }
    }

}
