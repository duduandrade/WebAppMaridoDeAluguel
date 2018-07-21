<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Solicitacoes;
/**
 * Enderecosolicitacao
 *
 * @ORM\Table(name="enderecosolicitacao", indexes={@ORM\Index(name="fk_enderecoSolicitacao_Solicitacoes1_idx", columns={"Solicitacoes_idSolicitacoes"})})
 * @ORM\Entity
 */
class Enderecosolicitacao
{
    /**
     * @var int
     *
     * @ORM\Column(name="idEnderecoSolicitacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idenderecosolicitacao;

    /**
     * @var string|null
     *
     * @ORM\Column(name="latitude", type="string", length=45, nullable=true)
     */
    private $latitude;

    /**
     * @var string|null
     *
     * @ORM\Column(name="longitude", type="string", length=45, nullable=true)
     */
    private $longitude;

    /**
     * @var string|null
     *
     * @ORM\Column(name="enderecoSolicitacao", type="string", length=200, nullable=true)
     */
    private $enderecosolicitacao;

    /**
     * @var \Solicitacoes
     *
     * @ORM\ManyToOne(targetEntity="Solicitacoes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Solicitacoes_idSolicitacoes", referencedColumnName="idSolicitacoes")
     * })
     */
    private $solicitacoessolicitacoes;

    function getIdenderecosolicitacao() {
        return $this->idenderecosolicitacao;
    }

    function getLatitude() {
        return $this->latitude;
}

    function getLongitude() {
        return $this->longitude;
    }

    function getEnderecosolicitacao() {
        return $this->enderecosolicitacao;
    }

    function getSolicitacoessolicitacoes() {
        return $this->solicitacoessolicitacoes;
    }

    function setIdenderecosolicitacao($idenderecosolicitacao) {
        $this->idenderecosolicitacao = $idenderecosolicitacao;
    }

    function setLatitude($latitude) {
        $this->latitude = $latitude;
    }

    function setLongitude($longitude) {
        $this->longitude = $longitude;
    }

    function setEnderecosolicitacao($enderecosolicitacao) {
        $this->enderecosolicitacao = $enderecosolicitacao;
    }

    function setSolicitacoessolicitacoes(Solicitacoes $solicitacoessolicitacoes) {
        $this->solicitacoessolicitacoes = $solicitacoessolicitacoes;
    }


}
