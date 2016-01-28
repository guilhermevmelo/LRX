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

    public function __set($name, $value) {
        $this->$name = $value;
    }

    /**
     * @param bool|true $retornar    Diz ao método se deve retornar a identificação ou setar ao objeto.
     * @return null|string           A identificação gerada, caso o parâmetro $retornar seja true.
     */
    public function gerarIdentificacao($retornar = false) {
        $identificacao = Util::obterIniciais($this->usuario->nome);

        if (strlen($identificacao) > 3)
            $identificacao = substr($identificacao, 0, 3);

        // TODO: Adicionar ao identificador os números do usuário e da amostra.

        $this->identificacao_da_amostra = $identificacao;
        if ($retornar)
            return $identificacao;
        return null;
    }


}