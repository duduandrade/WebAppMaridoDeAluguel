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

class ServicoController extends Controller {

    public $em;

    /**
     * @Route("/solicitar", name="solicitarProfissional")
     */
    public function solicitarProfissional() {
        //query builder
        $em = $this->getDoctrine()->getManager();
        $qb = $em->createQueryBuilder();
        //s e c sao alias 
        $qb->select('s,c')
                ->from('App\Entity\Servicos', 's')
                ->join('s.categoriasservicoscategoriasservicos', 'c');

        // to get just one result:
        // $product = $qb->setMaxResults(1)->getOneOrNullResult();
        $servicos = $qb->getQuery()->getResult();

        $categorias = $this->getDoctrine()->getRepository(Categoriasservicos::class)
                ->findAll();
        return $this->render('solicitarServico.html.twig', array('servicos' => $servicos, 'categorias' => $categorias));
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
        return $this->render('orcamento.html.twig');
    }

    /**
     * @Route("/solicitacoes", name="solicitacoes")
     */
    public function solicitacoes() {
        $idUsuario = $this->get('session')->get('idUsuario');
        $servicos = array();
        $servicos = ServicoController::buscarServicoEmAndamento($idUsuario, $this->getDoctrine());

        return $this->render('solicitacaoAndamentoCliente.html.twig', array("solicitacao" => $servicos));
    }

    static public function buscarServicoEmAndamento($idUsuario, $doctrine) {
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s,u,p, uP, e, ser, cat')
                ->from('App\Entity\Solicitacoes', 's')
                ->join('s.usuariosusuarios', 'u')
                ->join('s.profissionaisprofissionais', 'p')
                ->join('s.servicosIdservico', 'ser')
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
                ->join('s.servicosIdservico', 'ser')
                ->join('ser.categoriasservicoscategoriasservicos', 'cat')
                ->leftJoin('p.usuariosusuarios', 'uP')
                ->leftJoin(
                        'App\Entity\Enderecosolicitacao', 'e', \Doctrine\ORM\Query\Expr\Join::WITH, 'e.solicitacoessolicitacoes = s.idsolicitacoes'
                )
                ->where($qb->expr()->eq('s.profissionaisprofissionais', $idProfissional))
                ->andWhere($qb->expr()->neq('s.statussolicitacao', 9));
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

                        $novaSolicitacao = new Solicitacoes();
                        $novaSolicitacao->setProfissionaisprofissionais($objetoProf);
                        $novaSolicitacao->setUsuariosusuarios($objetoUsuario);
                        $novaSolicitacao->setServicosIdservico($objetoServico);
                        $novaSolicitacao->setPrecofinal($valorFinal);
                        $novaSolicitacao->setStatussolicitacao(1);
                        $em->persist($novaSolicitacao);

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
                $this->redirectToRoute("login");
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

        return $this->redirectToRoute('home');
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
                    if ($status == 3) {
                        return new JsonResponse(array(
                            'erro' => FALSE,
                            'mensagem' => 'Sucesso',
                            'status' => $status,
                            'data' => array("trocaPreco"=>$solicitacao->getTrocapreco())
                        ));
                    } else {
                        return new JsonResponse(array(
                            'erro' => FALSE,
                            'mensagem' => 'Sucesso',
                            'status' => $status,
                            'data' => null
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
            }
        }
    }

}
