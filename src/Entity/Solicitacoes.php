<?php


namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Solicitacoes
 *
 * @ORM\Table(name="solicitacoes", indexes={@ORM\Index(name="fk_Solicitacoes_categoriasServicos1_idx", columns={"categoriasServicos_idcategoriasServicos"}), @ORM\Index(name="fk_Solicitacoes_Usuarios1_idx", columns={"Usuarios_idUsuarios"}), @ORM\Index(name="fk_Solicitacoes_Profissionais1_idx", columns={"Profissionais_idProfissionais"})})
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

    /**
     * @var \Categoriasservicos
     *
     * @ORM\ManyToOne(targetEntity="Categoriasservicos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoriasServicos_idcategoriasServicos", referencedColumnName="idcategoriasServicos")
     * })
     */
    private $categoriasservicoscategoriasservicos;
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

    function getProfissionaisprofissionais() {
        return $this->profissionaisprofissionais;
    }

    function getUsuariosusuarios() {
        return $this->usuariosusuarios;
    }

    function getCategoriasservicoscategoriasservicos() {
        return $this->categoriasservicoscategoriasservicos;
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

    function setProfissionaisprofissionais(\Profissionais $profissionaisprofissionais) {
        $this->profissionaisprofissionais = $profissionaisprofissionais;
    }

    function setUsuariosusuarios(\Usuarios $usuariosusuarios) {
        $this->usuariosusuarios = $usuariosusuarios;
    }

    function setCategoriasservicoscategoriasservicos(\Categoriasservicos $categoriasservicoscategoriasservicos) {
        $this->categoriasservicoscategoriasservicos = $categoriasservicoscategoriasservicos;
    }



}
