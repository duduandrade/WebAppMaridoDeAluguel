<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Profissionais
 *
 * @ORM\Table(name="profissionais", indexes={@ORM\Index(name="fk_Profissionais_Usuarios_idx", columns={"Usuarios_idUsuarios"})})
 * @ORM\Entity
 */
class Profissionais
{
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
     * @var \Usuarios
     *
     * @ORM\ManyToOne(targetEntity="Usuarios")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="Usuarios_idUsuarios", referencedColumnName="idUsuarios")
     * })
     */
    private $usuariosusuarios;


}
