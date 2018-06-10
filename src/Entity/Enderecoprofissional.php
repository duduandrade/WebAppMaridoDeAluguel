<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Enderecoprofissional
 *
 * @ORM\Table(name="enderecoprofissional", indexes={@ORM\Index(name="fk_EnderecoAtualProfissional_Profissionais1_idx", columns={"Profissionais_idProfissionais"})})
 * @ORM\Entity
 */
class Enderecoprofissional
{
    /**
     * @var int
     *
     * @ORM\Column(name="idEnderecoProfissional", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idenderecoprofissional;

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
     * @ORM\Column(name="endereco", type="string", length=45, nullable=true)
     */
    private $endereco;

    /**
     * @var \Profissionais
     *
     * @ORM\ManyToOne(targetEntity="Profissionais")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Profissionais_idProfissionais", referencedColumnName="idProfissionais")
     * })
     */
    private $profissionaisprofissionais;


}
