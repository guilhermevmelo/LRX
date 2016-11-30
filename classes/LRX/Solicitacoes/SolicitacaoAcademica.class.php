<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 4/13/16
 * Time: 16:36
 */

namespace LRX\Solicitacoes;

use LRX\Equipamentos\Equipamento;
use LRX\Usuarios\UsuarioAcademico;




class SolicitacaoAcademica extends Solicitacao {
    private $solicitante;   // \LRX\Usuario

    public function __construct(UsuarioAcademico $solicitante = null, Equipamento $equipamento = null) {
        $this->solicitante = $solicitante;
        $this->equipamento = $equipamento;
        $this->data_solicitacao = date('Y-m-d H:i:s');
    }

    /**
     * @return mixed
     */
    public function getSolicitante() {
        return $this->solicitante;
    }

    /**
     * @param mixed $solicitante
     */
    public function setSolicitante($solicitante) {
        $this->solicitante = $solicitante;
    }

    /**
     * @param bool|true $retornar    Diz ao método se deve retornar a identificação ou setar ao objeto.
     * @return null|string           A identificação gerada, caso o parâmetro $retornar seja true.
     */
    public function gerarIdentificacao($numeroDaAmostra = 0, $retornar = false) {
        /**
         * Adiciona as iniciais do Solicitante à identificação da Amostra
         */
        $identificacao = \LRX\obterIniciais($this->solicitante->getNome());
        /**
         * Capea em 3 a quantidade de letras das iniciais
         */
        if (strlen($identificacao) > 3)
            $identificacao = substr($identificacao, 0, 3);
        /**
         * Adiciona o id do solicitante
         */
        $identificacao .= sprintf("%03d", $this->solicitante->getId());
        /**
         * Adiciona o tipo de análise: F para Fluorescência | D para difração
         */
        $identificacao .= substr($this->equipamento->getTipo(), 0, 1);
        /**
         * Desabilitado:
         * Adiciona três caracteres aleatórios geradas a partir de um identificador único
         */
        //$identificacao .= strtoupper(substr(uniqid(), -3));
        /**
         * Habilitado: Substring adicionada pelo banco
         * Adiciona o número da Amostra passado por parâmetro
         */
        $identificacao .= sprintf("%03d", $numeroDaAmostra);

        $this->identificacao_da_amostra = $identificacao;
        if ($retornar)
            return $identificacao;
        return null;
    }
}