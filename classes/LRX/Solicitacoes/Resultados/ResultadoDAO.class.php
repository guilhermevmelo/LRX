<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 13/01/17
 * Time: 12:47
 */

namespace LRX\Solicitacoes\Resultados;

use const LRX\DSN;
use const LRX\USUARIO;
use const LRX\SENHA;

class ResultadoDAO {
    private $conexao;

    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    public function criar(Resultado $resultado) {
        $this->conexao->beginTransaction();
        $sql = sprintf("insert into resultados values (null, :id_operador, :id_solicitacao, :arquivo, :data_adicao, :tipo)");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_operador', $resultado->getOperador()->getId(), \PDO::PARAM_INT);
        $consulta->bindValue(':id_solicitacao', $resultado->getIdSolicitacao(), \PDO::PARAM_INT);
        $consulta->bindValue(':arquivo', $resultado->getUrlArquivo());
        $consulta->bindValue(':data_adicao', $resultado->getDataEnvio());
        $consulta->bindValue(':tipo', $resultado->getTipo());
        $consulta->execute();
        $this->conexao->commit();
    }

    public function obter(int $id, $em_array = false) {
        $sql = sprintf("select * from resultados where id_resultado = :id_resultado");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id_resultado", $id, \PDO::PARAM_INT);
        $consulta->execute();
        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);
        if ($tupla === false)
            return false;

        if ($em_array) {
            $resultado = array(
                "id"            => intval($tupla["id_resultado"]),
                //"operador" => ?,
                "url_arquivo"   => $tupla["arquivo"],
                "data_envio"    => date_create($tupla['data_solicitacao'])->format(\DateTime::W3C),
                "tipo"          => $tupla["tipo"]
            );
        } else {
            $resultado = new Resultado();
            $resultado->setId(intval($tupla["id_resultado"]));
            $resultado->setArquivo($tupla["arquivo"]);
            $resultado->setDataEnvio(date_create($tupla['data_solicitacao'])->format(\DateTime::W3C));
            $resultado->setTipo($tupla["tipo"]);
        }

        return $resultado;
    }

    public function obterTodosPorSolicitacao(int $id_solicitacao, $em_array = false) {
        $sql = sprintf("select * from resultados where id_solicitacao = :id_solicitacao");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(":id_solicitacao", $id_solicitacao, \PDO::PARAM_INT);
        $consulta->execute();

        $resultados = array();
        foreach($consulta->fetchAll(\PDO::FETCH_ASSOC) as $tupla) {
            if ($em_array) {
                $resultado = array(
                    "id"            => intval($tupla["id_resultado"]),
                    //"operador" => ?,
                    "url_arquivo"   => $tupla["arquivo"],
                    "data_envio"    => date_create($tupla['data_adicao'])->format(\DateTime::W3C),
                    "tipo"          => $tupla["tipo"]
                );
            } else {
                $resultado = new Resultado();
                $resultado->setId(intval($tupla["id_resultado"]));
                $resultado->setArquivo($tupla["arquivo"]);
                $resultado->setDataEnvio(date_create($tupla['data_solicitacao'])->format(\DateTime::W3C));
                $resultado->setTipo($tupla["tipo"]);
            }

            array_push($resultados, $resultado);
        }

        return $resultados;
    }

    public function atualizar(Resultado $resultado) {
        // TODO: Implement atualizar() method.
    }

    public function deletar(int $id) {
        // TODO: Implement deletar() method.
    }

    public function obterTodos($em_array = false) {
        // TODO: Implement obterTodos() method.
    }

    public function existe(Resultado $resultado) : bool {
        // TODO: Implement existe() method.
    }
}