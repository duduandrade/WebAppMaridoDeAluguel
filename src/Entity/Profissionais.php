<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Usuarios;

/**
 * Profissionais
 *
 * @ORM\Table(name="profissionais", indexes={@ORM\Index(name="fk_Profissionais_Usuarios_idx", columns={"Usuarios_idUsuarios"})})
 * @ORM\Entity
 */
class Profissionais {

    /**
     * @var int
     *
     * @ORM\Column(name="idProfissionais", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idprofissionais;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="statusAprovado", type="boolean", nullable=true)
     */
    private $statusaprovado;

    /**
     * @var int|null
     *
     * @ORM\Column(name="somaAvaliacoes", type="integer", nullable=true)
     */
    private $somaavaliacoes;

    /**
     * @var string|null
     *
     * @ORM\Column(name="enderecoResidencia", type="string", length=45, nullable=true)
     */
    private $enderecoresidencia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cep", type="string", length=45, nullable=true)
     */
    private $cep;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numero", type="string", length=45, nullable=true)
     */
    private $numero;

    /**
     * @var string|null
     *
     * @ORM\Column(name="bairro", type="string", length=45, nullable=true)
     */
    private $bairro;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="mostrarCasa", type="boolean", nullable=true)
     */
    private $mostrarcasa;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="mostrarAtual", type="boolean", nullable=true)
     */
    private $mostraratual;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="statusDisponivel", type="boolean", nullable=true)
     */
    private $statusdisponivel;

    function getStatusdisponivel() {
        return $this->statusdisponivel;
    }

    function setStatusdisponivel($statusdisponivel) {
        $this->statusdisponivel = $statusdisponivel;
    }

    /**
     * @var \Usuarios
     *
     * @ORM\ManyToOne(targetEntity="Usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Usuarios_idUsuarios", referencedColumnName="idUsuarios")
     * })
     */
    private $usuariosusuarios;

    function getIdprofissionais() {
        return $this->idprofissionais;
    }

    function getStatusaprovado() {
        return $this->statusaprovado;
    }

    function getSomaavaliacoes() {
        return $this->somaavaliacoes;
    }

    function getEnderecoresidencia() {
        return $this->enderecoresidencia;
    }

    function getCep() {
        return $this->cep;
    }

    function getNumero() {
        return $this->numero;
    }

    function getBairro() {
        return $this->bairro;
    }

    function getUsuariosusuarios() {
        return $this->usuariosusuarios;
    }

    function setIdprofissionais($idprofissionais) {
        $this->idprofissionais = $idprofissionais;
    }

    function setStatusaprovado($statusaprovado) {
        $this->statusaprovado = $statusaprovado;
    }

    function setSomaavaliacoes($somaavaliacoes) {
        $this->somaavaliacoes = $somaavaliacoes;
    }

    function setUsuariosusuarios(Usuarios $usuariosusuarios) {
        $this->usuariosusuarios = $usuariosusuarios;
    }

    function setCep($cep) {
        $this->cep = $cep;
    }

    function setNumero($numero) {
        $this->numero = $numero;
    }

    function setBairro($bairro) {
        $this->bairro = $bairro;
    }

    function getMostrarcasa() {
        return $this->mostrarcasa;
    }

    function getMostraratual() {
        return $this->mostraratual;
    }

    function setMostrarcasa($mostrarcasa) {
        $this->mostrarcasa = $mostrarcasa;
    }

    function setMostraratual($mostraratual) {
        $this->mostraratual = $mostraratual;
    }

}
