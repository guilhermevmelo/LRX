<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 2/15/16
 * Time: 14:51
 */

namespace LRX\Equipamentos;

use LRX\Erro;



/**
 * Class EquipamentoDAO
 * @package LRX
 */
class EquipamentoDAO /*extends DAO*/ {
    /**
     * @var \PDO
     */
    private $conexao;

    /**
     * EquipamentoDAO constructor.
     */
    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    /**
     * @param Equipamento $equipamento
     * @return bool|null
     */
    public function criar(Equipamento $equipamento) {
        if ($this->existe($equipamento->getId()))
            return false;

        try {
            $this->conexao->beginTransaction();
            
            $sql = sprintf("insert into equipamentos values(null, :nome, :tipo, :tubo, :disponivel)");
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(':nome', $equipamento->getNome());
            $consulta->bindValue(':tipo', $equipamento->getTipo());
            $consulta->bindValue(':tubo', $equipamento->getTubo());
            $consulta->bindValue(':disponivel', $equipamento->disponivel(), \PDO::PARAM_BOOL);
            $consulta->execute();
            \LRX\print_p($consulta->errorInfo());

            $this->conexao->commit();
            return true;
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
            return false;
        }

    }

    /**
     * @param $id_equipamento
     * @return bool|Equipamento
     */
    public function obter($id_equipamento) {
        $sql = sprintf("select * from equipamentos where id_equipamento = :id_equipamento limit 1");
        $consulta = $this->conexao->prepare($sql);

        $consulta->bindValue(':id_equipamento', $id_equipamento);
        $consulta->execute();
        
        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);
        
        if ($tupla === false)
            return false;

        // TODO: Adicionar os serviços direto no construtor

        $e = new Equipamento($tupla['id_equipamento'], $tupla['nome'], $tupla['tipo'], $tupla['tubo']);
        if ($tupla['disponivel'] == 1)
            $e->disponibilizar();

        return $e;
    }

    /**
     * @param Equipamento $equipamento
     * @return bool
     */
    public function atualizar(Equipamento $equipamento) {
        $equipamento_antigo = $this->obter($equipamento->getId());
        if ($equipamento_antigo === false)
            return false;

        try {
            $this->conexao->beginTransaction();

            $sql = sprintf("update equipamentos set nome = :nome, tipo = :tipo, tubo = :tubo, disponivel = :disponivel where id_equipamento = :id");
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue(':nome', $equipamento->getNome());
            $consulta->bindValue(':tipo', $equipamento->getTipo());
            $consulta->bindValue(':tubo', $equipamento->getTubo());
            $consulta->bindValue(':disponivel', $equipamento->disponivel(), \PDO::PARAM_BOOL);

            $consulta->execute();
            \LRX\print_p($consulta->errorInfo());

            $this->conexao->commit();
            return true;
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
            return false;
        }
    }

    /**
     * @param $id
     */
    public function deletar($id) {
        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("DELETE FROM equipamentos WHERE id_equipamento = :id LIMIT 1");
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindParam(':id', $id);
            $consulta->execute();
            $this->conexao->commit();
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
        }
    }

    /**
     * @param bool $apenas_disponiveis
     * @param bool $em_array
     * @return array
     */
    public function obterTodos($apenas_disponiveis = false, $em_array = false) {
        $sql = sprintf("select * from equipamentos");
        if ($apenas_disponiveis)
            $sql .= sprintf(" where disponivel = 1");
        $sql .= sprintf(" order by id_equipamento asc");
        $equipamentos = array();

        foreach ($this->conexao->query($sql) as $tupla) {
            if (!$em_array) {
                $e = new Equipamento((int)$tupla['id_equipamento'], $tupla['nome'], $tupla['tipo'],
                    $tupla['tubo'], $tupla['disponivel'] == 1 ? true : false);

                // TODO: Adicionar os serviços
            } else {
                $e = array(
                    'id_equipamento'    => intval($tupla['id_equipamento']),
                    'nome'              => $tupla['nome'],
                    'tubo'              => $tupla['tubo'],
                    'tipo'              => $tupla['tipo'],
                    'disponivel'        =>$tupla['disponivel'] == 1 ? true : false
                );
            }
            array_push($equipamentos, $e);

        }

        return $equipamentos;
    }

    /**
     * @param $id
     * @return bool
     */
    public function existe($id) {
        $sql = sprintf("select * from equipamentos where id_equipamento = :id");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindParam(':id', $id);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }
}