<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/9/16
 * Time: 15:52
 */

namespace LRX;

require_once "autoload.php";

class AlunoDAO /*extends DAO*/ {
    private $conexao;
    private $uDAO;
    private $pDAO;

    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
        $this->uDAO = new UsuarioAcademicoDAO();
        $this->pDAO = new ProfessorDAO();
    }

    public function criar(Aluno $aluno, bool $novo_usuario = true) {
        if ($this->existeDocumento($aluno->getDocumento()))
            return false;

        try {
            if ($novo_usuario)
                $aluno->setId($this->uDAO->criar($aluno));

            $this->conexao->beginTransaction();

            $sql = sprintf("insert into alunos values (:id_aluno, :id_professor, :vinculo, :id_grupo)");
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':id_aluno', $aluno->getId());
            $consulta->bindValue(':id_professor', $aluno->getProfessor()->getId());
            $valor_grupo_id = $aluno->getGrupo() != null ? $aluno->getGrupo()->getId() : null;
            $consulta->bindValue(':id_grupo', $valor_grupo_id);
            $consulta->bindValue(':vinculo', $aluno->getVinculo());
            $consulta->execute();

            print_p($consulta->errorInfo());
            $this->conexao->commit();

            return $aluno;
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
            return false;
        }
    }

    public function obter(int $id) {
        $sql = sprintf("select u.*, a.id_grupo, a.id_professor, a.vinculo
                        from usuarios u, alunos a
                        where u.id_usuario = :id_usuario
                          and a.id_aluno = u.id_usuario limit 1");

        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_usuario', $id);

        $consulta->execute();

        $tupla = $consulta->fetch(\PDO::FETCH_ASSOC);

        if ($tupla === false)
            return false;

        $a = new Aluno($tupla['nome'], $tupla['email'], $tupla['cpf'], $this->pDAO->obter($tupla['id_professor']),
            $tupla['vinculo'], (int) $tupla['limite'], $tupla['uid'], (int) $tupla['id_usuario']);
        $a->setConfirmado($tupla['confirmado'] == 1 ? true : false);
        $a->setEmailConfirmado($tupla['email_confirmado'] == 1 ? true : false);
        $a->setCidade($tupla['cidade']);
        $a->setEstado($tupla['estado']);
        $a->setSenha($tupla['senha']);
        $a->setAreaDePesquisa($tupla['area_de_pesquisa']);
        $a->setDepartamento($tupla['departamento']);
        $a->setLaboratorio($tupla['laboratorio']);
        $a->setEmailAlternativo($tupla['email_alternativo']);
        $a->setNivelAcesso((int) $tupla['nivel_acesso']);
        $a->setGenero((int) $tupla['genero']);
        $a->setTelefone($tupla['telefone']);
        $a->setTitulo((int) $tupla['titulo']);

        return $a;
    }

    public function existeDocumento($documento) {
        $sql = sprintf("select * from usuarios u, alunos a where u.cpf = :documento and a.id_aluno = u.id_usuario");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':documento', $documento);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }

    public function atualizar(Aluno $objeto) {
        // TODO: Implement atualizar() method.
    }

    public function deletar(int $id) {
        // TODO: Implement deletar() method.
    }

    public function obterTodos() {
        $sql = sprintf("select u.*, a.id_grupo, a.id_professor, a.vinculo
                        from usuarios u, alunos a
                        where a.id_aluno = u.id_usuario
                        order by u.id_usuario desc");

        $alunos = array();

        foreach ($this->conexao->query($sql) as $tupla) {
            $a = new Aluno($tupla['nome'], $tupla['email'], $tupla['cpf'], $this->pDAO->obter($tupla['id_professor']),
                $tupla['vinculo'], (int) $tupla['limite'], $tupla['uid'], (int) $tupla['id_usuario']);
            $a->setConfirmado($tupla['confirmado'] == 1 ? true : false);
            $a->setCidade($tupla['cidade']);
            $a->setEstado($tupla['estado']);
            $a->setSenha($tupla['senha']);
            $a->setAreaDePesquisa($tupla['area_de_pesquisa']);
            $a->setDepartamento($tupla['departamento']);
            $a->setLaboratorio($tupla['laboratorio']);
            $a->setEmailAlternativo($tupla['email_alternativo']);
            $a->setNivelAcesso((int) $tupla['nivel_acesso']);
            $a->setGenero((int) $tupla['genero']);
            $a->setTelefone($tupla['telefone']);
            $a->setTitulo((int) $tupla['titulo']);

            array_push($alunos, $a);
        }

        return $alunos;
    }
}