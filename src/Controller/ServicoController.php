<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Servicos;
use App\Entity\Categoriasservicos;
use Doctrine\ORM\QueryBuilder;

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

}
