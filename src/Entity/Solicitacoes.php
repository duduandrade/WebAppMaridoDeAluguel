<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Solicitacoes
 *
 * @ORM\Table(name="solicitacoes", indexes={@ORM\Index(name="fk_Solicitacoes_Usuarios1_idx", columns={"Usuarios_idUsuarios"}), @ORM\Index(name="fk_Solicitacoes_Profissionais1_idx", columns={"Profissionais_idProfissionais"}), @ORM\Index(name="fk_solicitacoes_servicos1_idx", columns={"servicos_idServico"})})
 * @ORM\Entity
 */
class Solicitacoes
{
    /**
     * @var int
     *
     * @ORM\Column(name="idSolicitacoes", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idsolicitacoes;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="statusSolicitacao", type="boolean", nullable=true)
     */
    private $statussolicitacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descricaoSolicitacao", type="string", length=45, nullable=true)
     */
    private $descricaosolicitacao;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="dtSolicitacao", type="datetime", nullable=true)
     */
    private $dtsolicitacao;

    /**
     * @var int
     *
     * @ORM\Column(name="servicos_idServico", type="integer", nullable=false)
     */
    private $servicosIdservico;

    /**
     * @var string|null
     *
     * @ORM\Column(name="precoFinal", type="string", length=45, nullable=true)
     */
    private $precofinal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="tempoChegada", type="integer", nullable=true)
     */
    private $tempochegada;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="trocaPreco", type="boolean", nullable=true)
     */
    private $trocapreco;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="trocaPrecoAutorizada", type="boolean", nullable=true)
     */
    private $trocaprecoautorizada;

    /**
     * @var \Profissionais
     *
     * @ORM\ManyToOne(targetEntity="Profissionais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Profissionais_idProfissionais", referencedColumnName="idProfissionais")
     * })
     */
    private $profissionaisprofissionais;

    /**
     * @var \Usuarios
     *
     * @ORM\ManyToOne(targetEntity="Usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Usuarios_idUsuarios", referencedColumnName="idUsuarios")
     * })
     */
    private $usuariosusuarios;

    function getIdsolicitacoes() {
        return $this->idsolicitacoes;
    }

    function getStatussolicitacao() {
        return $this->statussolicitacao;
}

    function getDescricaosolicitacao() {
        return $this->descricaosolicitacao;
    }

    function getDtsolicitacao() {
        return $this->dtsolicitacao;
    }

    function getServicosIdservico() {
        return $this->servicosIdservico;
    }

    function getProfissionaisprofissionais() {
        return $this->profissionaisprofissionais;
    }

    function getUsuariosusuarios() {
        return $this->usuariosusuarios;
    }

    function setIdsolicitacoes($idsolicitacoes) {
        $this->idsolicitacoes = $idsolicitacoes;
    }

    function setStatussolicitacao($statussolicitacao) {
        $this->statussolicitacao = $statussolicitacao;
    }

    function setDescricaosolicitacao($descricaosolicitacao) {
        $this->descricaosolicitacao = $descricaosolicitacao;
    }

    function setDtsolicitacao(\DateTime $dtsolicitacao) {
        $this->dtsolicitacao = $dtsolicitacao;
    }

    function setServicosIdservico($servicosIdservico) {
        $this->servicosIdservico = $servicosIdservico;
    }

    function setProfissionaisprofissionais(\Profissionais $profissionaisprofissionais) {
        $this->profissionaisprofissionais = $profissionaisprofissionais;
    }

    function setUsuariosusuarios(\Usuarios $usuariosusuarios) {
        $this->usuariosusuarios = $usuariosusuarios;
    }


}
