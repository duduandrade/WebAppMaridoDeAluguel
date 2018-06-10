<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categoriasservicos
 *
 * @ORM\Table(name="categoriasservicos")
 * @ORM\Entity
 */
class Categoriasservicos
{
    /**
     * @var int
     *
     * @ORM\Column(name="idcategoriasServicos", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idcategoriasservicos;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nomeCategoria", type="string", length=45, nullable=true)
     */
    private $nomecategoria;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descricaoCategoria", type="string", length=45, nullable=true)
     */
    private $descricaocategoria;

    function getIdcategoriasservicos() {
        return $this->idcategoriasservicos;
    }

    function getNomecategoria() {
        return $this->nomecategoria;
    }

    function getDescricaocategoria() {
        return $this->descricaocategoria;
    }

    function setIdcategoriasservicos($idcategoriasservicos) {
        $this->idcategoriasservicos = $idcategoriasservicos;
    }

    function setNomecategoria($nomecategoria) {
        $this->nomecategoria = $nomecategoria;
    }

    function setDescricaocategoria($descricaocategoria) {
        $this->descricaocategoria = $descricaocategoria;
    }


}
