<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:26
 */

namespace LRX;

require_once "autoload.php";

abstract class Usuario {
    protected $id;
    protected $documento;   // CPF ou CNPJ
    protected $nome;
    protected $email;
    protected $senha;       // Hash SHA1
    protected $telefone;
    protected $nivel_acesso;
    protected $confirmado;
    protected $uid;
    protected $mensagens;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getDocumento() {
        return $this->documento;
    }

    /**
     * @param mixed $documento
     */
    public function setDocumento($documento) {
        $this->documento = $documento;
    }

    /**
     * @return mixed
     */
    public function getNome() {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome) {
        $this->nome = $nome;
    }

    /**
     * @return mixed
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getSenha() {
        return $this->senha;
    }

    /**
     * @param mixed $senha
     */
    public function setSenha($senha) {
        $this->senha = $senha;
    }

    public function setSenhaAberta($senha) {
        $this->senha = sha1($senha);
    }

    /**
     * @return mixed
     */
    public function getTelefone() {
        return $this->telefone;
    }

    /**
     * @param mixed $telefone
     */
    public function setTelefone($telefone) {
        $this->telefone = $telefone;
    }

    /**
     * @return mixed
     */
    public function getNivelAcesso() {
        return $this->nivel_acesso;
    }

    /**
     * @param mixed $nivel_acesso
     */
    public function setNivelAcesso($nivel_acesso) {
        $this->nivel_acesso = $nivel_acesso;
    }

    /**
     * @return mixed
     */
    public function getConfirmado() {
        return $this->confirmado;
    }

    /**
     * @param mixed $confirmado
     */
    public function setConfirmado($confirmado) {
        $this->confirmado = $confirmado;
    }

    /**
     * @return mixed
     */
    public function getUid() {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid) {
        $this->uid = $uid;
    }

    /**
     * @return mixed
     */
    public function getMensagens() {
        return $this->mensagens;
    }

    /**
     * @param mixed $mensagens
     */
    public function setMensagens($mensagens) {
        $this->mensagens = $mensagens;
    }
}