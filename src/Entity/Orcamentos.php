<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orcamentos
 *
 * @ORM\Table(name="orcamentos", indexes={@ORM\Index(name="fk_Orcamentos_Solicitacoes1_idx", columns={"Solicitacoes_idSolicitacoes"})})
 * @ORM\Entity
 */
class Orcamentos
{
    /**
     * @var int
     *
     * @ORM\Column(name="idOrcamentos", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idorcamentos;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="statusOrcamento", type="boolean", nullable=true)
     */
    private $statusorcamento;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dtOrcamento", type="datetime", nullable=true)
     */
    private $dtorcamento;

    /**
     * @var \Solicitacoes
     *
     * @ORM\ManyToOne(targetEntity="Solicitacoes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Solicitacoes_idSolicitacoes", referencedColumnName="idSolicitacoes")
     * })
     */
    private $solicitacoessolicitacoes;
    function getIdorcamentos() {
        return $this->idorcamentos;
    }

    function getStatusorcamento() {
        return $this->statusorcamento;
    }

    function getDtorcamento() {
        return $this->dtorcamento;
}

    function getSolicitacoessolicitacoes() {
        return $this->solicitacoessolicitacoes;
    }

    function setIdorcamentos($idorcamentos) {
        $this->idorcamentos = $idorcamentos;
    }

    function setStatusorcamento($statusorcamento) {
        $this->statusorcamento = $statusorcamento;
    }

    function setDtorcamento(\DateTime $dtorcamento) {
        $this->dtorcamento = $dtorcamento;
    }

    function setSolicitacoessolicitacoes(\Solicitacoes $solicitacoessolicitacoes) {
        $this->solicitacoessolicitacoes = $solicitacoessolicitacoes;
    }



}
