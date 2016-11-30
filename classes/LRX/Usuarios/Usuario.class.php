<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:26
 */

namespace LRX\Usuarios;



abstract class Usuario {
    protected $id;
    protected $documento;   // CPF ou CNPJ
    protected $nome;
    protected $email;
    protected $email_alternativo;
    protected $titulo;
    protected $genero;      // Masculino | Feminino
    protected $senha;       // Hash SHA1
    protected $telefone;
    protected $nivel_acesso = 1; // 1 - Aluno | 2 - Professor | 3 - Responsável por empresa | 4 - Financeiro | 5 -
    // Operador | 6 - Administrador
    protected $confirmado = false;
    protected $email_confirmado = false;
    protected $uid;
    protected $mensagens = array();   // array de mensagens
    protected $estado;
    protected $cidade;
    protected $saudacao = 0;


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
    public function confirmado() {
        return $this->confirmado;
    }

    /**
     * @param mixed $confirmado
     */
    public function setConfirmado($confirmado) {
        $this->confirmado = $confirmado;
    }

    public function confirmar() {
        $this->confirmado = true;
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

    public function gerarUid() {
        $uid = md5(uniqid(rand(), true));
        $this->uid = $uid;
        return $uid;
    }

    /**
     * @return mixed
     */
    public function getMensagens() {
        return $this->mensagens;
    }

    public function setMensagens($mensagens) {
        $this->mensagens = $mensagens;
    }

    /**
     * @param mixed $mensagens
     */
    public function addMensagem($mensagem) {
        array_push($this->mensagens, $mensagem);
    }

    /**
     * @return mixed
     */
    public function getEmailAlternativo() {
        return $this->email_alternativo;
    }

    /**
     * @param mixed $email_alternativo
     */
    public function setEmailAlternativo($email_alternativo) {
        $this->email_alternativo = $email_alternativo;
    }

    /**
     * @return mixed
     */
    public function getTitulo() {
        return $this->titulo;
    }

    /**
     * @param mixed $titulo
     */
    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    /**
     * @return mixed
     */
    public function getGenero() {
        return $this->genero;
    }

    /**
     * @param mixed $genero
     */
    public function setGenero($genero) {
        $this->genero = $genero;
    }

    /**
     * @return mixed
     */
    public function getEstado() {
        return $this->estado;
    }

    /**
     * @param mixed $estado
     */
    public function setEstado($estado) {
        $this->estado = $estado;
    }

    /**
     * @return mixed
     */
    public function getCidade() {
        return $this->cidade;
    }

    /**
     * @param mixed $cidade
     */
    public function setCidade($cidade) {
        $this->cidade = $cidade;
    }

    /**
     * @return boolean
     */
    public function emailConfirmado() {
        return $this->email_confirmado;
    }


    /**
     * @param boolean $email_confirmado
     */
    public function setEmailConfirmado($email_confirmado) {
        $this->email_confirmado = $email_confirmado;
    }

    public function confirmarEmail() {
        $this->email_confirmado = true;
    }

    /**
     * @return mixed
     */
    public function getSaudacao() {
        return $this->saudacao;
    }

    /**
     * @param mixed $saudacao
     */
    public function setSaudacao($saudacao) {
        $this->saudacao = $saudacao;
    }
}