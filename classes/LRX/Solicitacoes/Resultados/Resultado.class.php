<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 18:56
 */

namespace LRX\Solicitacoes\Resultados;

use LRX\Erro;
use LRX\Solicitacoes\Solicitacao;
use LRX\Usuarios\UsuarioAcademico;

use function \LRX\obterExtensaoArquivo;

class Resultado {
    protected $id;
    protected $operador;
    protected $id_solicitacao;
    protected $data_envio;
    protected $url_arquivo; // url do arquivo no servidor
    protected $arquivo;     // O arquivo de fato, um FILE
    protected $tipo;

    public function __construct($url_arquivo = null, int $id_solicitacao = null) {
        $this->id = null;
        $this->operador = null;
        $this->data_envio = null;
        $this->id_solicitacao = $id_solicitacao;

        if ($url_arquivo === null) {
            $this->url_arquivo = null;
            $this->arquivo = null;
            $this->tipo = null;
            return;
        }

        if (!file_exists($url_arquivo)) {
            Erro::lancarErro(array("codigo" => Erro::ERRO_ARQUIVO_INVALIDO, "mensagem" => "Arquivo não existe"));
            return;
        }

        $this->url_arquivo = $url_arquivo;
        $this->arquivo = fopen($url_arquivo, "r");
        $this->tipo = strtolower(obterExtensaoArquivo($url_arquivo));
    }

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
     * @return UsuarioAcademico
     */
    public function getOperador() : UsuarioAcademico {
        return $this->operador;
    }

    /**
     * @param UsuarioAcademico $operador
     */
    public function setOperador(UsuarioAcademico $operador) {
        $this->operador = $operador;
    }

    /**
     * @return int
     */
    public function getIdSolicitacao(): int {
        return $this->id_solicitacao;
    }

    /**
     * @param int $id_solicitacao
     */
    public function setIdSolicitacao(Solicitacao $id_solicitacao) {
        $this->id_solicitacao = $id_solicitacao;
    }

    /**
     * @return mixed
     */
    public function getDataEnvio() {
        return $this->data_envio;
    }

    /**
     * @param mixed $data_envio
     */
    public function setDataEnvio($data_envio) {
        $this->data_envio = $data_envio;
    }

    /**
     * @return mixed
     */
    public function getUrlArquivo() {
        return $this->url_arquivo;
    }

    /**
     * @param mixed $url_arquivo
     */
    public function setUrlArquivo($url_arquivo) {
        $this->url_arquivo = $url_arquivo;
    }

    /**
     * @return mixed
     */
    public function getArquivo() {
        return $this->arquivo;
    }

    /**
     * @param mixed $arquivo
     */
    public function setArquivo($arquivo) {
        $this->arquivo = $arquivo;
    }

    /**
     * @return mixed
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * @param mixed $tipo
     */
    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }
}