<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 23/11/15
 * Time: 15:35
 */

namespace LRX;

require_once 'Util.class.php';

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
    private $reuranca_outro;
    private $observacoes;

    /**
     * @param bool|true $proprio    Diz ao método se deve retornar a identificação ou setar ao objeto.
     * @return null|string          A identificação gerada, caso o parâmetro $proprio seja false.
     */
    public function gerarIdentificacao($proprio = true) {
        $identificacao = Util::obterIniciais($this->usuario->nome);

        if (strlen($identificacao) > 3)
            $identificacao = substr($identificacao, 0, 3);

        // TODO: Adicionar ao identificador os números do usuário e da amostra.

        if (!$proprio)
            return $identificacao;

        $this->identificacao_da_amostra = $identificacao;
        return null;
    }


}