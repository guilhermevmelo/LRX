<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 4/4/16
 * Time: 14:47
 */

namespace LRX;


class SolicitacaoDAO {
    private $conexao;

    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    public function criar(Solicitacao &$solicitacao) {
//        if ($solicitacao->getIdentificacaoDaAmostra() != null && $this->existeIdentificacao
//        ($solicitacao->getIdentificacaoDaAmostra()))
//            return false;

        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("insert into solicitacoes values (null, :id_equipamento, :id_fenda, null, :data_solicitacao, null, 1, :configuracao, 
:identificacao, :composicao, :tipo, :tipo_outro, null, :inflamavel, :radioativo, :toxico, :corrosivo, :higroscopico, 
:seguranca_outro, :observacoes)");

            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':id_equipamento',$solicitacao->getEquipamento()->getId(), \PDO::PARAM_INT);
            $consulta->bindValue(':id_fenda', $solicitacao->getFenda()->getId(), \PDO::PARAM_INT);
            $consulta->bindValue(':data_solicitacao', $solicitacao->getDataSolicitacao());
            $consulta->bindValue(':configuracao', json_encode($solicitacao->getConfiguracao()));
            $consulta->bindValue(':identificacao', $solicitacao->getIdentificacaoDaAmostra());
            $consulta->bindValue(':composicao',$solicitacao->getComposicao());
            $consulta->bindValue(':tipo', $solicitacao->getTipo(), \PDO::PARAM_INT);
            $consulta->bindValue(':tipo_outro', $solicitacao->getTipoOutro());
            $consulta->bindValue(':inflamavel', $solicitacao->getInflamavel(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':radioativo', $solicitacao->getRadioativo(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':toxico', $solicitacao->getToxico(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':corrosivo', $solicitacao->getCorrosivo(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':higroscopico', $solicitacao->getHigroscopico(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':seguranca_outro', $solicitacao->getSegurancaOutro());
            $consulta->bindValue(':observacoes', $solicitacao->getObservacoes());

            $consulta->execute();
            print_p($consulta->errorInfo());

            $sql2 = sprintf("select max(id_solicitacao) from solicitacoes");
            $consulta2 = $this->conexao->query($sql2);

            $novoId = (int) $consulta2->fetchColumn(0);

            //print_p($consulta->errorInfo());
            $solicitacao->setId($novoId);

            $this->conexao->commit();
            return true;
        } catch (\Exception $pdoe) {

            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
            return null;
        }
    }
    public function obter($id) {

        // TODO: Implement obter() method.
    }

    public function obterPorIdentificacao($identificacao) {
        // TODO: Implement obter() method.
    }

    public function atualizar(Solicitacao $solicitacao) {
        // TODO: Implement atualizar() method.
    }

    public function deletar($id) {
        // TODO: Implement deletar() method.
    }

    public function obterTodos() {
        // TODO: Implement obterTodos() method.
    }

    public function existeIdentificacao($identificacao) {
        // TODO: Implement existe() method.
    }
}