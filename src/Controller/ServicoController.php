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

    static public function buscarServioEmAndamento($idUsuario, $doctrine) {
        $em = $doctrine->getManager();
        $qb = $em->createQueryBuilder();
        $qb->select('s,u')
                ->from('App\Entity\Solicitacoes', 's')
                ->join('s.usuariosusuarios', 'u')
                ->where($qb->expr()->eq('s.usuariosusuarios', $idUsuario))
                ->andWhere($qb->expr()->neq('s.statussolicitacao', 9));
        $result = $qb->getQuery()->getOneOrNullResult();
        if ($result != null) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * @Route("/criarSolicitacao", name="criarSolicitacao")
     */
    public function criarSolicitacao() {
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
                    } catch (Exception $e) {
                        
                    }
                }
            }
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

}
