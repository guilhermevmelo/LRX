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
    private $id_solicitacao;
    private $usuario;       // \LRX\Usuario
    private $equipamento;   // \LRX\Equipamento
    private $fenda;         // \LRX\Fenda
    private $resultado;     // \LRX\Resultado
    private $data_solicitacao;
    private $data_conclusao;
    private $status;
    private $configuracao;  // array(=>)
    private $identificacao_da_amostra;
    private $composicao;
    private $tipo;
    private $tipo_outro;
    private $data_recebimento;
    private $inflamavel;    // bool
    private $radioativo;    // bool
    private $toxico;        // bool
    private $corrosivo;     // bool
    private $higroscopico;   // bool
    private $seguranca_outro;
    private $observacoes;

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