<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 4/4/16
 * Time: 14:47
 */

namespace LRX\Solicitacoes;

use LRX\Erro;
use const LRX\DSN;
use const LRX\USUARIO;
use const LRX\SENHA;


class SolicitacaoDAO {
    private $conexao;

    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    public function criar(Solicitacao &$solicitacao) {
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

            $sql2 = sprintf("select max(id_solicitacao) from solicitacoes");
            $consulta2 = $this->conexao->query($sql2);

            $novoId = (int) $consulta2->fetchColumn(0);
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
        $solicitacao_antiga = $this->existe($solicitacao->getId());

        if ($solicitacao_antiga === false)
            return false;

        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("update solicitacoes set id_solicitacao = :id_solicitacao, id_equipamento = 
            :id_equipamento, 
            id_fenda = :id_fenda,  
            data_solicitacao = :data_solicitacao, 
            data_conclusao = :data_conclusao, status = :_status, configuracao = :configuracao, 
            identificacao_da_amostra = :identificacao, 
            composicao = :composicao, tipo = :tipo, 
            tipo_outro = :tipo_outro, 
            data_recebimento = :data_recebimento, 
            inflamavel = :inflamavel, 
            radioativo = :radioativo, 
            toxico = :toxico, corrosivo = :corrosivo, higroscopico = :higroscopico, seguranca_outro = :seguranca_outro,
            observacoes = :observacoes
            where 
            id_solicitacao = :id_solicitacao");

            $consulta = $this->conexao->prepare($sql);


            $consulta->bindValue(':id_solicitacao',$solicitacao->getId(), \PDO::PARAM_INT);
            $consulta->bindValue(':id_equipamento',$solicitacao->getEquipamento(), \PDO::PARAM_INT);
            $consulta->bindValue(':id_fenda', $solicitacao->getFenda(), \PDO::PARAM_INT);
            //$consulta->bindValue(':id_resultado', $solicitacao->getResultado()->getId(), \PDO::PARAM_INT);
            $consulta->bindValue(':data_solicitacao', $solicitacao->getDataSolicitacao()->format("Y-m-d H:i:s"));
            $consulta->bindValue(':data_conclusao', $solicitacao->getDataConclusao() != null ? $solicitacao->getDataConclusao()->format("Y-m-d H:i:s") : null);
            $consulta->bindValue(':_status', $solicitacao->getStatus());
            $consulta->bindValue(':configuracao', json_encode($solicitacao->getConfiguracao()));
            $consulta->bindValue(':identificacao', $solicitacao->getIdentificacaoDaAmostra());
            $consulta->bindValue(':composicao',$solicitacao->getComposicao());
            $consulta->bindValue(':tipo', $solicitacao->getTipo(), \PDO::PARAM_INT);
            $consulta->bindValue(':tipo_outro', $solicitacao->getTipoOutro());
            $consulta->bindValue(':data_recebimento', $solicitacao->getDataRecebimento() != null ? $solicitacao->getDataRecebimento()->format("Y-m-d H:i:s") : null);
            $consulta->bindValue(':inflamavel', $solicitacao->getInflamavel(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':radioativo', $solicitacao->getRadioativo(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':toxico', $solicitacao->getToxico(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':corrosivo', $solicitacao->getCorrosivo(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':higroscopico', $solicitacao->getHigroscopico(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':seguranca_outro', $solicitacao->getSegurancaOutro());
            $consulta->bindValue(':observacoes', $solicitacao->getObservacoes());

            $consulta->execute();

            $this->conexao->commit();
            return true;
        } catch (\Exception $pdoe) {

            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage() , $pdoe->getLine() ));
            return null;
        }
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

    public function existe($id) {
        $sql = sprintf("select * from solicitacoes where id_solicitacao = :id");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id', $id);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }
}