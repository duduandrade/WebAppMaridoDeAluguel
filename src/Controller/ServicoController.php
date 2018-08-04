<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Servicos;
use App\Entity\Categoriasservicos;
use Doctrine\ORM\QueryBuilder;
use App\Entity\Solicitacoes;
use App\Entity\Enderecosolicitacao;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use DateTime;
use DateTimeZone;

class ServicoController extends Controller {

    public $em;

    /**
     * @Route("/solicitar", name="solicitarProfissional")
     */
    public function solicitarProfissional() {
        $idUsuario = $this->get('session')->get('idUsuario');
        $tipoUsuario = $this->get('session')->get('tipoUsuario');
        if ($idUsuario != null) {
            //query builder
            $em = $this->getDoctrine()->getManager();
            $qb = $em->createQueryBuilder();
            //s e c sao alias 
            $qb->select('s,c')
                    ->from('App\Entity\Servicos', 's')
                    ->join('s.categoriasservicoscategoriasservicos', 'c');

            $servicos = $qb->getQuery()->getResult();


            $categorias = $this->getDoctrine()->getRepository(Categoriasservicos::class)
                    ->findAll();
            return $this->render('solicitarServico.html.twig', array('servicos' => $servicos, 'categorias' => $categorias, 'fixed' => false));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/concluidas", name="concluidas")
     */
    public function concluidas() {
        $idUsuario = $this->get('session')->get('idUsuario');
        if ($idUsuario != null) {
            $solicitacoesConcluidas = ServicoController::buscarSolicitacoesConcluidas($idUsuario, $this->getDoctrine());
            return $this->render('solicitacoesConcluidasCliente.html.twig', array('solicitacoesConcluidas' => $solicitacoesConcluidas, 'fixed' => true));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/concluidasprof", name="concluidasprof")
     */
    public function concluidasprof() {
        $idUsuario = $this->get('session')->get('idUsuario');

        if ($idUsuario != null) {
            $idProf = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());
            $solicitacoesConcluidas = ServicoController::buscarSolicitacoesConcluidasProf($idProf->getIdprofissionais(), $this->getDoctrine());
            return $this->render('solicitacoesConcluidasProfissional.html.twig', array('solicitacoesConcluidas' => $solicitacoesConcluidas, 'fixed' => true));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    static public function buscarSolicitacoesConcluidas($idUsuario, $doctrine) {
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s,u,p, uP, ser, cat')
                ->from('App\Entity\Solicitacoes', 's')
                ->join('s.usuariosusuarios', 'u')
                ->join('s.profissionaisprofissionais', 'p')
                ->join('s.servicosservico', 'ser')
                ->join('ser.categoriasservicoscategoriasservicos', 'cat')
                ->leftJoin('p.usuariosusuarios', 'uP')
//                ->leftJoin(
//                        'App\Entity\Enderecosolicitacao', 'e', \Doctrine\ORM\Query\Expr\Join::WITH, 'e.solicitacoessolicitacoes = s.idsolicitacoes'
//                )
                ->where($qb->expr()->eq('s.usuariosusuarios', $idUsuario))
                ->andWhere($qb->expr()->eq('s.statussolicitacao', 9))
                ->orWhere($qb->expr()->eq('s.statussolicitacao', 8))
                ->orderBy('s.dtsolicitacao', 'DESC');

        $result = $qb->getQuery()->getResult();
        if ($result != null) {
            return $result;
        } else {
            return false;
        }
    }

    static public function buscarSolicitacoesConcluidasProf($idprof, $doctrine) {
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s,u,p, uP, ser, cat')
                ->from('App\Entity\Solicitacoes', 's')
                ->join('s.usuariosusuarios', 'u')
                ->join('s.profissionaisprofissionais', 'p')
                ->join('s.servicosservico', 'ser')
                ->join('ser.categoriasservicoscategoriasservicos', 'cat')
                ->leftJoin('p.usuariosusuarios', 'uP')
//                ->leftJoin(
//                        'App\Entity\Enderecosolicitacao', 'e', \Doctrine\ORM\Query\Expr\Join::WITH, 'e.solicitacoessolicitacoes = s.idsolicitacoes'
//                )
                ->where($qb->expr()->eq('s.profissionaisprofissionais', $idprof))
                ->andWhere($qb->expr()->eq('s.statussolicitacao', 9))
                ->orWhere($qb->expr()->eq('s.statussolicitacao', 8))
                ->orderBy('s.dtsolicitacao', 'DESC');
        $result = $qb->getQuery()->getResult();
        if ($result != null) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * @Route("/set/{idservico}/{quantidade}", name="set")
     */
    public function set($idservico, $quantidade) {
        $this->get('session')->set('idservicosolicitado', $idservico);
        $this->get('session')->set('quantidade', $quantidade);
        return $this->redirectToRoute('procurarprofissional');
    }

    /**
     * @Route("/orcamento", name="orcamento")
     */
    public function orcamento() {

        $idUsuario = $this->get('session')->get('idUsuario');
        if ($idUsuario != null) {
            $servico = new Servicos();
            $objetoCategoria = $this->getDoctrine()->getRepository(Categoriasservicos::class)
                    ->findOneBy(array('idcategoriasservicos' => 5));

            $servico->setCategoriasservicoscategoriasservicos($objetoCategoria);
            $servico->setNomeservico("Orcamento");
            $servico->setValorservico(0);
            $servico->setUnidademedida("A avaliar");

            $em = $this->getDoctrine()->getManager();
            $em->persist($servico);
             $em->flush();
            $this->get('session')->set('idservicosolicitado', $servico->getIdservico());
            $this->get('session')->set('quantidade', 0);
           
            return $this->redirectToRoute('procurarprofissional');
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/solicitacoes", name="solicitacoes")
     */
    public function solicitacoes() {
        $idUsuario = $this->get('session')->get('idUsuario');
        if ($idUsuario != null) {
            $servicos = array();
            $servicos = ServicoController::buscarServicoEmAndamento($idUsuario, $this->getDoctrine());

            return $this->render('solicitacaoAndamentoCliente.html.twig', array("solicitacao" => $servicos));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/andamento", name="andamento")
     */
    public function andamento() {
        $idUsuario = $this->get('session')->get('idUsuario');
        if ($idUsuario != null) {
            $profissional = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());
            $solicitacoesProf = ServicoController::buscarServicoEmAndamentoProfissional($profissional->getIdprofissionais(), $this->getDoctrine());
            return $this->render('solicitacoesAndamentoProfissional.html.twig', array("solicitacoesProf" => $solicitacoesProf));
        } else {
            return $this->redirectToRoute("login");
        }
    }

    static public function buscarServicoEmAndamento($idUsuario, $doctrine) {
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s,u,p, uP, e, ser, cat')
                ->from('App\Entity\Solicitacoes', 's')
                ->join('s.usuariosusuarios', 'u')
                ->join('s.profissionaisprofissionais', 'p')
                ->join('s.servicosservico', 'ser')
                ->join('ser.categoriasservicoscategoriasservicos', 'cat')
                ->leftJoin('p.usuariosusuarios', 'uP')
                ->leftJoin(
                        'App\Entity\Enderecosolicitacao', 'e', \Doctrine\ORM\Query\Expr\Join::WITH, 'e.solicitacoessolicitacoes = s.idsolicitacoes'
                )
                ->where($qb->expr()->eq('s.usuariosusuarios', $idUsuario))
                ->andWhere($qb->expr()->neq('s.statussolicitacao', 9));
        $result = $qb->getQuery()->getResult();
        if ($result != null) {
            return $result;
        } else {
            return false;
        }
    }

    static public function buscarServicoEmAndamentoProfissional($idProfissional, $doctrine) {
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s,u,p, uP, e, ser, cat')
                ->from('App\Entity\Solicitacoes', 's')
                ->join('s.usuariosusuarios', 'u')
                ->join('s.profissionaisprofissionais', 'p')
                ->join('s.servicosservico', 'ser')
                ->join('ser.categoriasservicoscategoriasservicos', 'cat')
                ->leftJoin('p.usuariosusuarios', 'uP')
                ->leftJoin(
                        'App\Entity\Enderecosolicitacao', 'e', \Doctrine\ORM\Query\Expr\Join::WITH, 'e.solicitacoessolicitacoes = s.idsolicitacoes'
                )
                ->where($qb->expr()->eq('s.profissionaisprofissionais', $idProfissional))
                ->andWhere($qb->expr()->eq('s.statussolicitacao', 2));
        $result = $qb->getQuery()->getResult();
        if ($result != null) {
            return $result;
        } else {
            return false;
        }
    }

    static public function buscarServicoEmEsperaProfissional($idProfissional, $doctrine) {
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s,u,p, uP, e, ser, cat')
                ->from('App\Entity\Solicitacoes', 's')
                ->join('s.usuariosusuarios', 'u')
                ->join('s.profissionaisprofissionais', 'p')
                ->join('s.servicosservico', 'ser')
                ->join('ser.categoriasservicoscategoriasservicos', 'cat')
                ->leftJoin('p.usuariosusuarios', 'uP')
                ->leftJoin(
                        'App\Entity\Enderecosolicitacao', 'e', \Doctrine\ORM\Query\Expr\Join::WITH, 'e.solicitacoessolicitacoes = s.idsolicitacoes'
                )
                ->where($qb->expr()->eq('s.profissionaisprofissionais', $idProfissional))
                ->andWhere($qb->expr()->eq('s.statussolicitacao', 1));
        $result = $qb->getQuery()->getResult();
        if ($result != null) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * @Route("/criarSolicitacao", name="criarSolicitacao")
     */
    public function criarSolicitacao(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $em = $this->getDoctrine()->getManager();

                $idUsuario = $this->get('session')->get('idUsuario');
                $idservicosolicitado = $this->get('session')->get('idservicosolicitado');
                $quantidade = $this->get('session')->get('quantidade');

                $endereco = $data["endereco"];
                $profissional = $data["profissional"];

                $objetoProf = ProfissionalController::buscarProfissionalPorId($profissional, $this->getDoctrine());
                $objetoServico = ServicoController::buscarServicoPorId($idservicosolicitado, $this->getDoctrine());
                $objetoUsuario = UsuarioController::buscarUsuarioPorId($idUsuario, $this->getDoctrine());

                if ($objetoProf != false && $objetoServico != false && $objetoUsuario != false) {
                    try {
                        $valorServico = $objetoServico->getValorservico();
                        $valorFinal = $valorServico * $quantidade;
                        $UTC = new DateTimeZone("UTC");
                        $newTZ = new DateTimeZone("America/Sao_Paulo");

                        $now = new DateTime('now', $UTC);
                        $now->setTimezone($newTZ);
                        $novaSolicitacao = new Solicitacoes();
                        $novaSolicitacao->setProfissionaisprofissionais($objetoProf);
                        $novaSolicitacao->setUsuariosusuarios($objetoUsuario);
                        $novaSolicitacao->setServicosservico($objetoServico);
                        $novaSolicitacao->setPrecofinal($valorFinal);
                        $novaSolicitacao->setStatussolicitacao(1);
                        $novaSolicitacao->setDtsolicitacao($now);
                        $em->persist($novaSolicitacao);
                        $this->get('session')->set('jatem', true);

                        $enderecoSolicitacao = new Enderecosolicitacao();
                        $enderecoSolicitacao->setSolicitacoessolicitacoes($novaSolicitacao);
                        $enderecoSolicitacao->setEnderecosolicitacao($endereco);
                        $em->persist($enderecoSolicitacao);
                        $em->flush();

                        return new JsonResponse(array(
                            'erro' => false,
                            'mensagem' => '',
                            'data' => null
                        ));
                    } catch (Exception $e) {
                        
                    }
                } else {
                    return new JsonResponse(array(
                        'erro' => true,
                        'mensagem' => 'Falha ao buscar dados',
                        'data' => null
                    ));
                }
            } else {
                return $this->redirectToRoute("login");
            }
        } else {
            return new JsonResponse(array(
                'erro' => true,
                'mensagem' => 'Mensagem não é um json',
                'data' => null
            ));
        }
    }

    static function buscarServicoPorId($idServico, $doctrine) {
        $objetoServico = $doctrine->getRepository(Servicos::class)
                ->findOneBy(array('idservico' => $idServico));

        if (!$objetoServico) {
            return false;
        } else {
            return $objetoServico;
        }
    }

    static function buscarSolicitacaoPorId($idSolicitacao, $doctrine) {
        $objetoSolicitacao = $doctrine->getRepository(Solicitacoes::class)
                ->findOneBy(array('idsolicitacoes' => $idSolicitacao));

        if (!$objetoSolicitacao) {
            return false;
        } else {
            return $objetoSolicitacao;
        }
    }

    static function buscarSolicitacaPorIdEProfissional($idSolicitacao, $idProfissional, $doctrine) {
        $objetoSolicitacao = $doctrine->getRepository(Solicitacoes::class)
                ->findOneBy(array('idsolicitacoes' => $idSolicitacao, "profissionaisprofissionais" => $idProfissional));

        if (!$objetoSolicitacao) {
            return false;
        } else {
            return $objetoSolicitacao;
        }
    }

    /**
     * @Route("/concordo/{solicitacao}", name="concordo")
     */
    public function concordo($solicitacao) {
        $idUsuario = $this->get('session')->get('idUsuario');

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        $solicitacao = ServicoController::buscarSolicitacaoPorId($solicitacao, $this->getDoctrine());
        if ($solicitacao != false && $idUsuario != null) {
            $result = $qb->update('App\Entity\Solicitacoes', 's')
                            ->set('s.statussolicitacao', '?1')
                            ->set('s.trocaprecoautorizada', '?4')
                            ->set('s.precofinal', '?5')
                            ->where('s.usuariosusuarios = ?2')
                            ->andWhere('s.idsolicitacoes = ?3')
                            ->setParameter(1, 2)
                            ->setParameter(2, $idUsuario)
                            ->setParameter(3, $solicitacao)
                            ->setParameter(4, 1)
                            ->setParameter(5, $solicitacao->getNovovalor())
                            ->getQuery()->getSingleScalarResult();

            return $this->redirectToRoute("solicitacoes");
        } else {
            return $this->redirectToRoute("login");
        }
    }

    /**
     * @Route("/verificarAceiteCliente", name="verificarAceiteCliente")
     */
    public function verificarAceiteCliente(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {

                $solicitacao = ServicoController::buscarSolicitacaoPorId($data['solicitacao'], $this->getDoctrine());
                if ($solicitacao->getTrocaprecoautorizada()) {
                    return new JsonResponse(array(
                        'erro' => false,
                        'mensagem' => 'Não foi possivel cancelar.',
                        'autorizada' => true,
                        'data' => null
                    ));
                } else {
                    return new JsonResponse(array(
                        'erro' => false,
                        'mensagem' => 'Não foi possivel cancelar.',
                        'autorizada' => true,
                        'data' => null
                    ));
                }
            } else {
                return $this->redirectToRoute("login");
            }
        }
    }

    /**
     * @Route("/cancelar", name="cancelar")
     */
    public function cancelar(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $idUsuario = $this->get('session')->get('idUsuario');

                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();

                $result = $qb->update('App\Entity\Solicitacoes', 's')
                                ->set('s.statussolicitacao', '?1')
                                ->where('s.usuariosusuarios = ?2')
                                ->andWhere('s.idsolicitacoes = ?3')
                                ->setParameter(1, 9) //status 9 cancelada
                                ->setParameter(2, $idUsuario)
                                ->setParameter(3, $data["solicitacao"])
                                ->getQuery()->getSingleScalarResult();

                if ($result != null) {
                    return new JsonResponse(array(
                        'erro' => false,
                        'mensagem' => 'Sucesso',
                        'status' => null,
                        'data' => null
                    ));
                } else {
                    return new JsonResponse(array(
                        'erro' => true,
                        'mensagem' => 'Não foi possivel cancelar.',
                        'status' => null,
                        'data' => null
                    ));
                }
            } else {
                return $this->redirectToRoute("login");
            }
        }
    }

    /**
     * @Route("/finalizar", name="finalizar")
     */
    public function finalizar(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $idUsuario = $this->get('session')->get('idUsuario');

                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();

                $result = $qb->update('App\Entity\Solicitacoes', 's')
                                ->set('s.statussolicitacao', '?1')
                                ->andWhere('s.idsolicitacoes = ?2')
                                ->setParameter(1, 8) //status 8 finalizada
                                ->setParameter(2, $data["solicitacao"])
                                ->getQuery()->getSingleScalarResult();

                if ($result != null) {
                    return new JsonResponse(array(
                        'erro' => false,
                        'mensagem' => 'Sucesso',
                        'status' => null,
                        'data' => null
                    ));
                } else {
                    return new JsonResponse(array(
                        'erro' => true,
                        'mensagem' => 'Não foi possivel cancelar.',
                        'status' => null,
                        'data' => null
                    ));
                }
            } else {
                return $this->redirectToRoute("login");
            }
        }
    }

    /**
     * @Route("/alterarPreco", name="alterarPreco")
     */
    public function alterarPreco(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $idUsuario = $this->get('session')->get('idUsuario');
                $profissional = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());
                $em = $this->getDoctrine()->getManager();
                $qb = $em->createQueryBuilder();

                $result = $qb->update('App\Entity\Solicitacoes', 's')
//                                ->set('s.statussolicitacao', '?1')
                                ->set('s.novovalor', '?1')
                                ->set('s.motivotrocapreco', '?4')
                                ->set('s.trocapreco', '?5')
                                ->set('s.trocaprecoautorizada', '?6')
                                ->where('s.profissionaisprofissionais = ?2')
                                ->andWhere('s.idsolicitacoes = ?3')
//                                ->setParameter(1, 3) //status 3 troca preco 
                                ->setParameter(1, $data["novoPreco"])
                                ->setParameter(2, $profissional->getIdprofissionais())
                                ->setParameter(3, $data["solicitacao"])
                                ->setParameter(4, $data["motivo"])
                                ->setParameter(5, 1)
                                ->setParameter(6, 0)
                                ->getQuery()->getSingleScalarResult();

                if ($result != null) {
                    return new JsonResponse(array(
                        'erro' => false,
                        'mensagem' => 'Sucesso',
                        'status' => null,
                        'data' => null
                    ));
                } else {
                    return new JsonResponse(array(
                        'erro' => true,
                        'mensagem' => 'Não foi possivel trocar o preco.',
                        'status' => null,
                        'data' => null
                    ));
                }
            } else {
                return $this->redirectToRoute("login");
            }
        }
    }

    /**
     * @Route("/aceitar/{tempo}/{solicitacao}", name="aceitar")
     */
    public function aceitar($tempo, $solicitacao) {
        $idUsuario = $this->get('session')->get('idUsuario');
        $profissional = ProfissionalController::buscarProfissionalPorIdUsuario($idUsuario, $this->getDoctrine());

        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();

        $result = $qb->update('App\Entity\Solicitacoes', 's')
                        ->set('s.statussolicitacao', '?1')
                        ->set('s.tempochegada', '?2')
                        ->where('s.profissionaisprofissionais = ?3')
                        ->andWhere('s.idsolicitacoes = ?4')
                        ->setParameter(1, 2)
                        ->setParameter(2, (int) $tempo)
                        ->setParameter(3, $profissional->getIdprofissionais())
                        ->setParameter(4, $solicitacao)
                        ->getQuery()->getSingleScalarResult();

        return $this->redirectToRoute('andamento');
    }

    /**
     * @Route("/verificarStatusSolicitacao", name="verificarStatusSolicitacao")
     */
    public function verificarStatusSolicitacao(Request $request) {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
            $request->request->replace(is_array($data) ? $data : array());
            if ($this->get('session')->get('idUsuario')) {
                $solicitacao = ServicoController::buscarSolicitacaPorIdEProfissional($data["solicitacao"], $data["profissional"], $this->getDoctrine());
                if ($solicitacao != null) {
                    $status = $solicitacao->getStatussolicitacao();
                    if ($status == 3 && (!$solicitacao->getTrocaprecoautorizada())) {
                        return new JsonResponse(array(
                            'erro' => FALSE,
                            'mensagem' => 'Sucesso',
                            'status' => $status,
                            'data' => array("novoValor" => $solicitacao->getNovovalor(),
                                "motivoTrocaPreco" => $solicitacao->getMotivotrocapreco(),
                                "precoFinal" => $solicitacao->getPrecofinal())
                        ));
                    } else {
                        return new JsonResponse(array(
                            'erro' => FALSE,
                            'mensagem' => 'Sucesso',
                            'status' => $status,
                            'data' => array("tempoChegada" => $solicitacao->getTempochegada())
                        ));
                    }
                } else {
                    return new JsonResponse(array(
                        'erro' => true,
                        'mensagem' => 'Não encontrado a solicitacao',
                        'status' => null,
                        'data' => null
                    ));
                }
            } else {
                return $this->redirectToRoute("login");
            }
        }
    }

}
