<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="enderecoSolicitacao", type="string", length=45, nullable=true)
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


}
