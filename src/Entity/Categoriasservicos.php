<?php



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

    /**
     * @var string|null
     *
     * @ORM\Column(name="valorServico", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $valorservico;


}
