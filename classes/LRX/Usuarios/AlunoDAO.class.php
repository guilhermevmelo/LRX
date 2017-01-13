<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/9/16
 * Time: 15:52
 */

namespace LRX\Usuarios;

use LRX\Erro;
use LRX\Solicitacoes\SolicitacaoAcademicaDAO;
use const LRX\DSN;
use const LRX\USUARIO;
use const LRX\SENHA;


class AlunoDAO {
    private $conexao;
    private $uaDAO;
    private $pDAO;

    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
        $this->uaDAO = new UsuarioAcademicoDAO();
        $this->pDAO = new ProfessorDAO();
    }

    public function criar(Aluno $aluno, bool $novo_usuario = true) {
        if ($this->existeDocumento($aluno->getDocumento()))
            return false;

        try {
            if ($novo_usuario)
                $this->uaDAO->criar($aluno);

            $this->conexao->beginTransaction();

            $sql = sprintf("INSERT INTO alunos VALUES (:id_aluno, :id_professor, :vinculo, :id_grupo)");
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':id_aluno', $aluno->getId());
            $id_professor = $aluno->getProfessor() != null ? $aluno->getProfessor()->getId() : null;
            $consulta->bindValue(':id_professor', $id_professor);
            $id_grupo = $aluno->getGrupo() != null ? $aluno->getGrupo()->getId() : null;
            $consulta->bindValue(':id_grupo', $id_grupo);
            $consulta->bindValue(':vinculo', $aluno->getVinculo());
            $consulta->execute();

            \LRX\print_p($consulta->errorInfo());
            $this->conexao->commit();

            return $aluno;
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo' => $pdoe->getCode(),
                                   'mensagem' => $pdoe->getMessage()));
            return false;
        }
    }

    /**
     * @param int $id
     * @param bool $em_array
     * @return Aluno|array|bool
     */
    public function obter(int $id, $em_array = false) {
        $sql = sprintf("SELECT u.*, a.id_grupo, a.id_professor, a.vinculo
                        FROM usuarios u, alunos a
                        WHERE u.id_usuario = :id_usuario
                          AND a.id_aluno = u.id_usuario LIMIT 1");

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_usuario', $id);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return false;

        $saDAO = new SolicitacaoAcademicaDAO();

        if ($em_array) {
            $a = array();
            $a['id_aluno'] = intval($tupla['id_usuario']);
            $a['nome'] = $tupla['nome'];
            $a['email'] = $tupla['email'];
            $a['cpf'] = $tupla['cpf'];
            $a['vinculo'] = intval($tupla['vinculo']);
            $a['limite'] = intval($tupla['limite']);
            $a['numero_solicitacoes_abertas'] = $saDAO->obterNumeroSolicitacoesEmAndamento($id)["aprovadas"];
            $a['area_de_pesquisa'] = $tupla['area_de_pesquisa'];
            $a['confirmado'] = intval($tupla['confirmado']) === 1 ? true : false;
        } else {

            $a = new Aluno($tupla['nome'], $tupla['email'], $tupla['cpf'], $this->pDAO->obter($tupla['id_professor']),
                intval($tupla['vinculo']), intval($tupla['limite']), $tupla['uid'], intval($tupla['id_usuario']));
            $a->setConfirmado($tupla['confirmado'] == 1 ? true : false);
            $a->setEmailConfirmado($tupla['email_confirmado'] == 1 ? true : false);
            $a->setCidade($tupla['cidade']);
            $a->setEstado($tupla['estado']);
            $a->setSenha($tupla['senha']);
            $a->setAreaDePesquisa($tupla['area_de_pesquisa']);
            $a->setDepartamento($tupla['departamento']);
            $a->setLaboratorio($tupla['laboratorio']);
            $a->setEmailAlternativo($tupla['email_alternativo']);
            $a->setNivelAcesso((int)$tupla['nivel_acesso']);
            $a->setGenero((int)$tupla['genero']);
            $a->setTelefone($tupla['telefone']);
            $a->setTitulo((int)$tupla['titulo']);
        }
        return $a;
    }

    /**
     * @param $documento
     * @param bool $em_array
     * @return Aluno|array|bool
     */
    public function obterPorDocumento($documento, $em_array = false) {
        $sql = sprintf("SELECT u.*, a.id_grupo, a.id_professor, a.vinculo
                        FROM usuarios u, alunos a
                        WHERE u.cpf = :documento
                          AND a.id_aluno = u.id_usuario LIMIT 1");

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':documento', $documento);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return false;

        $saDAO = new SolicitacaoAcademicaDAO();

        if ($em_array) {
            $a = array();
            $a['id_aluno'] = intval($tupla['id_usuario']);
            $a['nome'] = $tupla['nome'];
            $a['email'] = $tupla['email'];
            $a['cpf'] = $tupla['cpf'];
            $a['vinculo'] = intval($tupla['vinculo']);
            $a['limite'] = intval($tupla['limite']);
            $a['numero_solicitacoes_abertas'] = $saDAO->obterNumeroSolicitacoesEmAndamento(intval($tupla['id_usuario']))["aprovadas"];
            $a['area_de_pesquisa'] = $tupla['area_de_pesquisa'];
            $a['confirmado'] = intval($tupla['confirmado']) === 1 ? true : false;
        } else {
            $professor = $tupla['id_professor'] != null ? $this->pDAO->obter($tupla['id_professor']) : null;

            $a = new Aluno($tupla['nome'], $tupla['email'], $tupla['cpf'], $professor,
                intval($tupla['vinculo']), intval($tupla['limite']), $tupla['uid'], intval($tupla['id_usuario']));
            $a->setConfirmado($tupla['confirmado'] == 1 ? true : false);
            $a->setEmailConfirmado($tupla['email_confirmado'] == 1 ? true : false);
            $a->setCidade($tupla['cidade']);
            $a->setEstado($tupla['estado']);
            $a->setSenha($tupla['senha']);
            $a->setAreaDePesquisa($tupla['area_de_pesquisa']);
            $a->setDepartamento($tupla['departamento']);
            $a->setLaboratorio($tupla['laboratorio']);
            $a->setEmailAlternativo($tupla['email_alternativo']);
            $a->setNivelAcesso((int)$tupla['nivel_acesso']);
            $a->setGenero((int)$tupla['genero']);
            $a->setTelefone($tupla['telefone']);
            $a->setTitulo((int)$tupla['titulo']);
        }
        return $a;
    }

    /**
     * @param $uid
     * @param bool $em_array
     * @return Aluno|array|bool
     */
    public function obterPorUid($uid, $em_array = false) {
        $sql = sprintf("SELECT u.*, a.id_grupo, a.id_professor, a.vinculo
                        FROM usuarios u, alunos a
                        WHERE u.uid = :uid
                          AND a.id_aluno = u.id_usuario LIMIT 1");

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':uid', $uid);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return false;

        $saDAO = new SolicitacaoAcademicaDAO();

        if ($em_array) {
            $a = array();
            $a['id_aluno'] = intval($tupla['id_usuario']);
            $a['nome'] = $tupla['nome'];
            $a['email'] = $tupla['email'];
            $a['cpf'] = $tupla['cpf'];
            $a['vinculo'] = intval($tupla['vinculo']);
            $a['limite'] = intval($tupla['limite']);
            $a['numero_solicitacoes_abertas'] = $saDAO->obterNumeroSolicitacoesEmAndamento(intval($tupla['id_usuario']))["aprovadas"];
            $a['area_de_pesquisa'] = $tupla['area_de_pesquisa'];
            $a['confirmado'] = intval($tupla['confirmado']) === 1 ? true : false;
        } else {
            $professor = $tupla['id_professor'] != null ? $this->pDAO->obter($tupla['id_professor']) : null;

            $a = new Aluno($tupla['nome'], $tupla['email'], $tupla['cpf'], $professor,
                intval($tupla['vinculo']), intval($tupla['limite']), $tupla['uid'], intval($tupla['id_usuario']));
            $a->setConfirmado($tupla['confirmado'] == 1 ? true : false);
            $a->setEmailConfirmado($tupla['email_confirmado'] == 1 ? true : false);
            $a->setCidade($tupla['cidade']);
            $a->setEstado($tupla['estado']);
            $a->setSenha($tupla['senha']);
            $a->setAreaDePesquisa($tupla['area_de_pesquisa']);
            $a->setDepartamento($tupla['departamento']);
            $a->setLaboratorio($tupla['laboratorio']);
            $a->setEmailAlternativo($tupla['email_alternativo']);
            $a->setNivelAcesso((int)$tupla['nivel_acesso']);
            $a->setGenero((int)$tupla['genero']);
            $a->setTelefone($tupla['telefone']);
            $a->setTitulo((int)$tupla['titulo']);
        }
        return $a;
    }

    public function atualizar(Aluno $aluno) {
        $aluno_antigo = $this->obter($aluno->getId());
        if ($aluno_antigo === false)
            return false;

        try {
            $this->uaDAO->atualizar($aluno);

            $this->conexao->beginTransaction();

            $sql = sprintf("UPDATE alunos SET id_grupo = :id_grupo, vinculo = :vinculo WHERE id_aluno = :id_aluno");
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':id_aluno', $aluno->getId());
            $valor_id_grupo = $aluno->getGrupo() != null ? $aluno->getGrupo()->getId() : null;
            $consulta->bindValue(':id_grupo', $valor_id_grupo);
            $consulta->bindValue(':vinculo', $aluno->getVinculo());

            $consulta->execute();

            \LRX\print_p($consulta->errorInfo());

            $this->conexao->commit();
            return true;
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo' => $pdoe->getCode(),
                                   'mensagem' => $pdoe->getMessage()));
            return false;
        }
    }

    public function deletar(int $id) {
        // TODO: Implement deletar() method.
    }

    public function obterTodos($em_array = false, $apenas_nao_confirmados = false) {
        $sql = $apenas_nao_confirmados ?
            sprintf("SELECT u.*, a.id_grupo, a.id_professor, a.vinculo
                        FROM usuarios u, alunos a
                        WHERE a.id_aluno = u.id_usuario AND confirmado = 0
                        ORDER BY u.id_usuario DESC") :
            sprintf("SELECT u.*, a.id_grupo, a.id_professor, a.vinculo
                        FROM usuarios u, alunos a
                        WHERE a.id_aluno = u.id_usuario
                        ORDER BY u.id_usuario DESC");

        $alunos = array();
        $saDAO = new SolicitacaoAcademicaDAO();
        foreach ($this->conexao->query($sql) as $tupla) {
            if ($em_array) {
                $a = array();
                $a["id_usuario"] = intval($tupla["id_usuario"]);
                $a["nome"] = $tupla["nome"];
                $a["email"] = $tupla["email"];
                $a["cpf"] = $tupla["cpf"];
                $a["professor"] = $this->pDAO->obter(intval($tupla['id_professor']))->getNome();
                $a["cidade"] = $tupla["cidade"];
                $a["estado"] = $tupla["estado"];
                $a["area_de_pesquisa"] = $tupla["area_de_pesquisa"];
                $a["laboratorio"] = $tupla["laboratorio"];
                $a["ies"] = $tupla["ies"];
                $a["genero"] = intval($tupla["genero"]);
                $a["telefone"] = $tupla["telefone"];
                $a["uid"] = $tupla["uid"];
                $a["limite"] = intval($tupla["limite"]);
                $a["departamento"] = $tupla["departamento"];
                $a["vinculo"] = intval($tupla["vinculo"]);
                $a["nivel_acesso"] = intval($tupla["nivel_acesso"]);
                $a["em_andamento"] = $saDAO->obterNumeroSolicitacoesEmAndamento(intval($tupla["id_usuario"]))["aprovadas"];
                $a["confirmado"] = $tupla["confirmado"] == 1 ? true : false;
            } else {
                $a = new Aluno($tupla['nome'],
                    $tupla['email'],
                    $tupla['cpf'],
                    $this->pDAO->obter(intval($tupla['id_professor'])),
                    $tupla['vinculo'],
                    (int)$tupla['limite'],
                    $tupla['uid'],
                    (int)$tupla['id_usuario']);
                $a->setConfirmado($tupla['confirmado'] == 1 ? true : false);
                $a->setCidade($tupla['cidade']);
                $a->setEstado($tupla['estado']);
                $a->setSenha($tupla['senha']);
                $a->setAreaDePesquisa($tupla['area_de_pesquisa']);
                $a->setDepartamento($tupla['departamento']);
                $a->setLaboratorio($tupla['laboratorio']);
                $a->setEmailAlternativo($tupla['email_alternativo']);
                $a->setNivelAcesso((int)$tupla['nivel_acesso']);
                $a->setGenero((int)$tupla['genero']);
                $a->setTelefone($tupla['telefone']);
                $a->setTitulo((int)$tupla['titulo']);
            }


            array_push($alunos, $a);
        }

        return $alunos;
    }

    public function obterTodosPorProfessor(int $id, $em_array = false) {
        $sql = sprintf("SELECT u.*, a.id_grupo, a.id_professor, a.vinculo
                        FROM usuarios u, alunos a
                        WHERE a.id_aluno = u.id_usuario AND a.id_professor = :id_professor
                        ORDER BY u.id_usuario DESC");

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_professor', $id);
        $consulta->execute();

        $alunos = array();
        $saDAO = new SolicitacaoAcademicaDAO();

        foreach ($consulta->fetchAll(\PDO::FETCH_ASSOC) as $tupla) {
            if ($em_array) {
                $a = array();
                $a['id_aluno'] = intval($tupla['id_usuario']);
                $a['nome'] = $tupla['nome'];
                $a['email'] = $tupla['email'];
                $a['cpf'] = $tupla['cpf'];
                $a['vinculo'] = intval($tupla['vinculo']);
                $a['limite'] = intval($tupla['limite']);
                $a['numero_solicitacoes_abertas'] = $saDAO->obterNumeroSolicitacoesEmAndamento($a['id_aluno'])["aprovadas"];
                $a['confirmado'] = intval($tupla['confirmado']) === 1 ? true : false;
            } else {
                $professor = $this->pDAO->obter($id);
                $a = new Aluno($tupla['nome'], $tupla['email'], $tupla['cpf'], $professor,
                    intval($tupla['vinculo']), intval($tupla['limite']), $tupla['uid'], intval($tupla['id_usuario']));
                $a->setConfirmado($tupla['confirmado'] == 1 ? true : false);
                $a->setCidade($tupla['cidade']);
                $a->setEstado($tupla['estado']);
                $a->setSenha($tupla['senha']);
                $a->setAreaDePesquisa($tupla['area_de_pesquisa']);
                $a->setDepartamento($tupla['departamento']);
                $a->setLaboratorio($tupla['laboratorio']);
                $a->setEmailAlternativo($tupla['email_alternativo']);
                $a->setNivelAcesso((int)$tupla['nivel_acesso']);
                $a->setGenero((int)$tupla['genero']);
                $a->setTelefone($tupla['telefone']);
                $a->setTitulo((int)$tupla['titulo']);
            }
            array_push($alunos, $a);
        }

        return $alunos;
    }

    public static function existeDocumento($documento) {
        $conexao = new \PDO(DSN, USUARIO, SENHA);

        $sql = sprintf("SELECT * FROM usuarios u, alunos a WHERE u.cpf = :documento AND a.id_aluno = u.id_usuario");
        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(':documento', $documento);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }

    public static function existeVinculo($id) {
        $conexao = new \PDO(DSN, USUARIO, SENHA);
        $sql = sprintf("SELECT id_aluno, id_professor FROM alunos WHERE id_aluno = :id_aluno");
        $consulta = $conexao->prepare($sql);
        $consulta->bindValue(":id_aluno", $id);
        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);
        if ($tupla === false)
            return false;

        if ($tupla["id_professor"] === null)
            return false;
        return true;

    }
}