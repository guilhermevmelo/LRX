<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:35
 */

namespace LRX;

require_once "autoload.php";

class Solicitacao {
    protected $id;
    protected $usuario;       // \LRX\Usuario
    protected $equipamento;   // \LRX\Equipamento
    protected $fenda;         // \LRX\Fenda
    protected $resultado;     // \LRX\Resultado
    protected $data_solicitacao;
    protected $data_conclusao;
    protected $status;
    protected $configuracao;  // array(=>)
    protected $identificacao_da_amostra;
    protected $composicao;
    protected $tipo;
    protected $tipo_outro;
    protected $data_recebimento;
    protected $inflamavel;    // bool
    protected $radioativo;    // bool
    protected $toxico;        // bool
    protected $corrosivo;     // bool
    protected $higroscopico;  // bool
    protected $seguranca_outro;
    protected $observacoes;

    public function __construct() { }

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
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * @param mixed $usuario
     */
    public function setUsuario($usuario) {
        $this->usuario = $usuario;
    }

    /**
     * @return mixed
     */
    public function getEquipamento() {
        return $this->equipamento;
    }

    /**
     * @param mixed $equipamento
     */
    public function setEquipamento($equipamento) {
        $this->equipamento = $equipamento;
    }

    /**
     * @return mixed
     */
    public function getFenda() {
        return $this->fenda;
    }

    /**
     * @param mixed $fenda
     */
    public function setFenda($fenda) {
        $this->fenda = $fenda;
    }

    /**
     * @return mixed
     */
    public function getResultado() {
        return $this->resultado;
    }

    /**
     * @param mixed $resultado
     */
    public function setResultado($resultado) {
        $this->resultado = $resultado;
    }

    /**
     * @return mixed
     */
    public function getDataSolicitacao() {
        return $this->data_solicitacao;
    }

    /**
     * @param mixed $data_solicitacao
     */
    public function setDataSolicitacao($data_solicitacao) {
        $this->data_solicitacao = $data_solicitacao;
    }

    /**
     * @return mixed
     */
    public function getDataConclusao() {
        return $this->data_conclusao;
    }

    /**
     * @param mixed $data_conclusao
     */
    public function setDataConclusao($data_conclusao) {
        $this->data_conclusao = $data_conclusao;
    }

    /**
     * @return mixed
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getConfiguracao() {
        return $this->configuracao;
    }

    /**
     * @param mixed $configuracao
     */
    public function setConfiguracao($configuracao) {
        $this->configuracao = $configuracao;
    }

    /**
     * @return mixed
     */
    public function getIdentificacaoDaAmostra() {
        return $this->identificacao_da_amostra;
    }

    /**
     * @param mixed $identificacao_da_amostra
     */
    public function setIdentificacaoDaAmostra($identificacao_da_amostra) {
        $this->identificacao_da_amostra = $identificacao_da_amostra;
    }

    /**
     * @return mixed
     */
    public function getComposicao() {
        return $this->composicao;
    }

    /**
     * @param mixed $composicao
     */
    public function setComposicao($composicao) {
        $this->composicao = $composicao;
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

    /**
     * @return mixed
     */
    public function getTipoOutro() {
        return $this->tipo_outro;
    }

    /**
     * @param mixed $tipo_outro
     */
    public function setTipoOutro($tipo_outro) {
        $this->tipo_outro = $tipo_outro;
    }

    /**
     * @return mixed
     */
    public function getDataRecebimento() {
        return $this->data_recebimento;
    }

    /**
     * @param mixed $data_recebimento
     */
    public function setDataRecebimento($data_recebimento) {
        $this->data_recebimento = $data_recebimento;
    }

    /**
     * @return mixed
     */
    public function getInflamavel() {
        return $this->inflamavel;
    }

    /**
     * @param bool $inflamavel
     */
    public function setInflamavel($inflamavel) {
        $this->inflamavel = $inflamavel;
    }

    /**
     * @return mixed
     */
    public function getRadioativo() {
        return $this->radioativo;
    }

    /**
     * @param mixed $radioativo
     */
    public function setRadioativo($radioativo) {
        $this->radioativo = $radioativo;
    }

    /**
     * @return mixed
     */
    public function getToxico() {
        return $this->toxico;
    }

    /**
     * @param mixed $toxico
     */
    public function setToxico($toxico) {
        $this->toxico = $toxico;
    }

    /**
     * @return mixed
     */
    public function getCorrosivo() {
        return $this->corrosivo;
    }

    /**
     * @param mixed $corrosivo
     */
    public function setCorrosivo($corrosivo) {
        $this->corrosivo = $corrosivo;
    }

    /**
     * @return mixed
     */
    public function getHigroscopico() {
        return $this->higroscopico;
    }

    /**
     * @param mixed $higroscopico
     */
    public function setHigroscopico($higroscopico) {
        $this->higroscopico = $higroscopico;
    }

    /**
     * @return mixed
     */
    public function getSegurancaOutro() {
        return $this->seguranca_outro;
    }

    /**
     * @param mixed $seguranca_outro
     */
    public function setSegurancaOutro($seguranca_outro) {
        $this->seguranca_outro = $seguranca_outro;
    }

    /**
     * @return mixed
     */
    public function getObservacoes() {
        return $this->observacoes;
    }

    /**
     * @param mixed $observacoes
     */
    public function setObservacoes($observacoes) {
        $this->observacoes = $observacoes;
    }



    /**
     * @param bool|true $retornar    Diz ao método se deve retornar a identificação ou setar ao objeto.
     * @return null|string           A identificação gerada, caso o parâmetro $retornar seja true.
     */
    public function gerarIdentificacao($retornar = false) {
        $identificacao = obterIniciais($this->usuario->nome);

        if (strlen($identificacao) > 3)
            $identificacao = substr($identificacao, 0, 3);

        // TODO: Adicionar ao identificador os números do usuário e da amostra.

        $this->identificacao_da_amostra = $identificacao;
        if ($retornar)
            return $identificacao;
        return null;
    }


}