<?php
/**
 * Created by PhpStorm.
 * User: romulo
 * Date: 27/03/17
 * Time: 15:54
 */

namespace LRX\Usuarios;
use LRX\Erro;
use LRX\Solicitacoes\SolicitacaoAcademicaDAO;
use const LRX\DSN;
use const LRX\USUARIO;
use const LRX\SENHA;


class PesquisadorDAO {

    private $conexao;
    private $ucDAO;

    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
        $this->ucDAO = new UsuarioComercialDAO();
    }

    public function criar(Pesquisador $pesquisador, bool $novo_usuario = true) {
        if ($this->existeDocumento($pesquisador->getDocumento()))
            return false;

        try {
            if ($novo_usuario)
                $this->ucDAO->criar($pesquisador);

            $this->conexao->beginTransaction();

            $sql = sprintf("INSERT INTO pesquisadores VALUES (:id_pesquisador, :id_empresa)");
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':id_pesquisador', $pesquisador->getId());
            $consulta->bindValue(':id_empresa', $pesquisador->getEmpresa()->getId());
            $consulta->execute();

            \LRX\print_p($consulta->errorInfo());
            $this->conexao->commit();

            return $pesquisador;
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo' => $pdoe->getCode(),
                'mensagem' => $pdoe->getMessage()));
            return false;
        }
    }

    public function existeDocumento($documento) {
        $sql = sprintf("select * from usuarios u, pesquisadores p where u.cpf = :documento and p.id_pesquisador = u.id_usuario");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':documento', $documento);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }

    public function obter(int $id, $em_array = false) : Pesquisador {
        $sql = sprintf("select u.*, p.id_empresa from usuarios u, pesquisadores p where u.id_usuario = :id_usuario and p.id_pesquisador = u.id_usuario limit 1");

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_usuario', $id);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return false;

        if ($em_array) {
            $p = array();
            // TODO: Terminar essa implementação
        } else {
            $p = new Pesquisador($tupla['nome'], $tupla['email'], $tupla['cpf'], (int) $tupla['id_usuario'], $tupla['uid'],
                (int)$tupla['limite']);
            $p->setConfirmado(intval($tupla['confirmado']) == 1 ? true : false);
            $p->setEmailConfirmado(intval($tupla['email_confirmado']) == 1 ? true : false);
            $p->setCidade($tupla['cidade']);
            $p->setEstado($tupla['estado']);
            $p->setSenha($tupla['senha']);
            $p->setEmailAlternativo($tupla['email_alternativo']);
            $p->setNivelAcesso((int) $tupla['nivel_acesso']);
            $p->setGenero((int) $tupla['genero']);
            $p->setTelefone($tupla['telefone']);
            $p->setSaudacao((int) $tupla['saudacao']);

            $eDAO = new EmpresaDAO();
            $e = $eDAO->obter($tupla['id_empresa']);
            $p->setEmpresa($e);

        }

        return $p;
    }

    public function atualizar(Pesquisador $pesquisador) {
        $pesquisador_antigo = $this->obter($pesquisador->getId());
        if ($pesquisador_antigo === false)
            return false;

        try {
            $this->ucDAO->atualizar($pesquisador);

            return true;
        } catch (\Exception $pdoe) {
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                'mensagem'  =>  $pdoe->getMessage()));
            return false;
        }
    }

    public function obterTodos($em_array = false, $apenas_nao_confirmados = false) : array {
        $sql = $apenas_nao_confirmados ?
            sprintf("select u.*, p.*, e.id_empresa, e.tipo, e.documento, e.nome AS nome_empresa, e.sigla, e.endereco, e.inscricao_estadual, e.inscricao_municipal, e.site from usuarios u, pesquisadores p, empresas e where p.id_pesquisador = u.id_usuario AND p.id_empresa = e.id_empresa AND u.confirmado = 0 order by id_usuario desc") :
            sprintf("select u.*, p.*, e.id_empresa, e.tipo, e.documento, e.nome AS nome_empresa, e.sigla, e.endereco, e.inscricao_estadual, e.inscricao_municipal, e.site from usuarios u, pesquisadores p, empresas e where p.id_pesquisador = u.id_usuario AND p.id_empresa = e.id_empresa order by id_usuario desc");
        $pesquisadores = array();
        $saDAO = new SolicitacaoAcademicaDAO();
        $eDAO = new EmpresaDAO();
        foreach ($this->conexao->query($sql) as $tupla) {
            if ($em_array) {
                $p = array();
                $p["id_usuario"] = intval($tupla["id_usuario"]);
                $p["nome"] = $tupla["nome"];
                $p["email"] = $tupla["email"];
                $p["cpf"] = $tupla["cpf"];
                $p["uid"] = $tupla["uid"];
                $p["confirmado"] = $tupla["confirmado"] == 1 ? true : false;
                $p["cidade"] = $tupla["cidade"];
                $p["estado"] = $tupla["estado"];
                $p["limite"] = intval($tupla["limite"]);
                $p["genero"] = intval($tupla["genero"]);
                $p["telefone"] = $tupla["telefone"];
                $p["nivel_acesso"] = intval($tupla["nivel_acesso"]);
                $p["em_andamento"] = $saDAO->obterNumeroSolicitacoesEmAndamento(intval($tupla["id_usuario"]))["aprovadas"];
                $p["id_empresa"] = $tupla["id_empresa"];
                $p["nome_empresa"] = $tupla["nome_empresa"];
                $p["cnpj"] = $tupla["documento"]; 
            } else {
                $p = new Pesquisador($tupla['nome'], $tupla['email'], $tupla['cpf'], (int) $tupla['id_usuario'], $tupla['uid'],
                    (int)$tupla['limite']);
                $p->setConfirmado($tupla['confirmado'] == 1 ? true : false);
                $p->setCidade($tupla['cidade']);
                $p->setEstado($tupla['estado']);
                $p->setSenha($tupla['senha']);
                $p->setEmailAlternativo($tupla['email_alternativo']);
                $p->setNivelAcesso((int) $tupla['nivel_acesso']);
                $p->setGenero((int) $tupla['genero']);
                $p->setTelefone($tupla['telefone']);
                $p->setSaudacao((int) $tupla['saudacao']);
                $p->setEmpresa($eDAO->obter(intval($tupla["id_empresa"])));
            }

            array_push($pesquisadores, $p);
        }

        return $pesquisadores;
    }

    public function deletar(int $id, bool $deletar_usuario = false) {

        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("delete from pesquisadores where id_pesquisador = :id_pesquisador limit 1");
            $consulta = $this->conexao->prepare($sql);
            $consulta->bindValue("id_pesquisador", $id);
            $consulta->execute();

            if ($deletar_usuario) {
                $sql2 = sprintf("delete from usuarios where id_usuario = :id_pesquisador limit 1");
                $consulta = $this->conexao->prepare($sql2);
                $consulta->bindValue("id_pesquisador", $id);
                $consulta->execute();
            }

            $this->conexao->commit();
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
        }
    }

}