<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Servicos
 *
 * @ORM\Table(name="servicos", indexes={@ORM\Index(name="fk_servicos_categoriasservicos1_idx", columns={"categoriasservicos_idcategoriasServicos"})})
 * @ORM\Entity
 */
class Servicos
{
    /**
     * @var int
     *
     * @ORM\Column(name="idServico", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idservico;

    /**
     * @var string
     *
     * @ORM\Column(name="nomeServico", type="string", length=45, nullable=false)
     */
    private $nomeservico;

    /**
     * @var string
     *
     * @ORM\Column(name="valorServico", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $valorservico;

    /**
     * @var \Categoriasservicos
     *
     * @ORM\ManyToOne(targetEntity="Categoriasservicos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoriasservicos_idcategoriasServicos", referencedColumnName="idcategoriasServicos")
     * })
     */
    private $categoriasservicoscategoriasservicos;

    function getIdservico() {
        return $this->idservico;
    }

    function getNomeservico() {
        return $this->nomeservico;
    }

    function getValorservico() {
        return $this->valorservico;
    }

    function getCategoriasservicoscategoriasservicos() {
        return $this->categoriasservicoscategoriasservicos;
    }

    function setIdservico($idservico) {
        $this->idservico = $idservico;
    }

    function setNomeservico($nomeservico) {
        $this->nomeservico = $nomeservico;
    }

    function setValorservico($valorservico) {
        $this->valorservico = $valorservico;
    }

    function setCategoriasservicoscategoriasservicos(\Categoriasservicos $categoriasservicoscategoriasservicos) {
        $this->categoriasservicoscategoriasservicos = $categoriasservicoscategoriasservicos;
    }


}
