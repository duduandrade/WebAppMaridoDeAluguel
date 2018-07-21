<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Usuarios
 *
 * @ORM\Table(name="usuarios", uniqueConstraints={@ORM\UniqueConstraint(name="email_UNIQUE", columns={"email"}), @ORM\UniqueConstraint(name="cpf_UNIQUE", columns={"cpf"})})
 * @ORM\Entity
 */
class Usuarios
{
    /**
     * @var int
     *
     * @ORM\Column(name="idUsuarios", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idusuarios;

    /**
     * @var string
     *
     * @ORM\Column(name="nome", type="string", length=45, nullable=false)
     */
    private $nome;

    /**
     * @var string
     *
     * @ORM\Column(name="senha", type="string", length=45, nullable=false)
     */
    private $senha;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="cpf", type="string", length=45, nullable=false)
     */
    private $cpf;

    /**
     * @var string
     *
     * @ORM\Column(name="telefone", type="string", length=45, nullable=false)
     */
    private $telefone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tipoUsuario", type="string", length=1, nullable=true, options={"fixed"=true})
     */
    private $tipousuario;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="emailConfirmado", type="boolean", nullable=true)
     */
    private $emailconfirmado;
    function getIdusuarios() {
        return $this->idusuarios;
    }

    function getNome() {
        return $this->nome;
    }

    function getSenha() {
        return $this->senha;
}

    function getEmail() {
        return $this->email;
    }

    function getCpf() {
        return $this->cpf;
    }

    function getTelefone() {
        return $this->telefone;
    }

    function getTipousuario() {
        return $this->tipousuario;
    }

    function getEmailconfirmado() {
        return $this->emailconfirmado;
    }

    function setIdusuarios($idusuarios) {
        $this->idusuarios = $idusuarios;
    }

    function setNome($nome) {
        $this->nome = $nome;
    }

    function setSenha($senha) {
        $this->senha = $senha;
    }

    function setEmail($email) {
        $this->email = $email;
    }

    function setCpf($cpf) {
        $this->cpf = $cpf;
    }

    function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    function setTipousuario($tipousuario) {
        $this->tipousuario = $tipousuario;
    }

    function setEmailconfirmado($emailconfirmado) {
        $this->emailconfirmado = $emailconfirmado;
    }



}
