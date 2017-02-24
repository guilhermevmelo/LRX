<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 3/9/16
 * Time: 16:48
 */

namespace LRX\Usuarios;

use LRX\Erro;
use function LRX\print_p;
use const LRX\DSN;
use const LRX\USUARIO;
use const LRX\SENHA;

class UsuarioAcademicoDAO {

    /**
     * @var \PDO
     */
    private $conexao;

    /**
     * UsuarioDAO constructor.
     */
    public function __construct() {
        $this->conexao = new \PDO(DSN, USUARIO, SENHA);
    }

    /**
     * @param UsuarioAcademico $usuario
     * @return bool|null
     */
    public function criar(UsuarioAcademico &$usuario) {

        if ($this->existeDocumento($usuario->getDocumento()))
            return false;

        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("insert into usuarios values (null, :cpf, :nome, :email, :senha, :estado, :cidade, :departamento, :laboratorio,
:area_de_pesquisa, :telefone, :email_alternativo, :nivel_acesso, :uid, :confirmado, 0, :titulo, :genero,
:limite, :ies, :saudacao)");
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':cpf', $usuario->getDocumento());
            $consulta->bindValue(':nome', $usuario->getNome());
            $consulta->bindValue(':email', $usuario->getEmail());
            $consulta->bindValue(':senha', $usuario->getSenha());
            $consulta->bindValue(':estado', $usuario->getEstado());
            $consulta->bindValue(':cidade', $usuario->getCidade());
            $consulta->bindValue(':departamento', $usuario->getDepartamento());
            $consulta->bindValue(':laboratorio', $usuario->getLaboratorio());
            $consulta->bindValue(':area_de_pesquisa', $usuario->getAreaDePesquisa());
            $consulta->bindValue(':telefone', $usuario->getTelefone());
            $consulta->bindValue(':email_alternativo', $usuario->getEmailAlternativo());
            $consulta->bindValue(':nivel_acesso', $usuario->getNivelAcesso(), \PDO::PARAM_INT);
            $consulta->bindValue(':uid', $usuario->getUid());
            $consulta->bindValue(':confirmado', $usuario->confirmado(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':titulo', $usuario->getTitulo(), \PDO::PARAM_INT);
            $consulta->bindValue(':genero', $usuario->getGenero(), \PDO::PARAM_INT);
            $consulta->bindValue(':limite', $usuario->getLimite(), \PDO::PARAM_INT);
            $consulta->bindValue(':ies', $usuario->getIes());
            $consulta->bindValue(':saudacao', $usuario->getSaudacao(), \PDO::PARAM_INT);

            $consulta->execute();

            //print_p($consulta->errorInfo());

            $sql2 = sprintf("select max(id_usuario) from usuarios");
            $consulta2 = $this->conexao->query($sql2);

            $novoId = (int) $consulta2->fetchColumn(0);

            //print_p($consulta->errorInfo());
            $usuario->setId($novoId);

            $this->conexao->commit();
            return true;
        } catch (\Exception $pdoe) {
            $this->conexao->rollBack();
            Erro::lancarErro(array('codigo'    =>  $pdoe->getCode(),
                                   'mensagem'  =>  $pdoe->getMessage()));
            return null;
        }
    }

    /**
     * @param int $id
     * @return UsuarioAcademico
     */
//    public function obter(int $id) : UsuarioAcademico {
//        $sql = sprintf("select * from usuarios where id_usuario = :id_usuario limit 1");
//
//        $consulta = $this->conexao->prepare($sql);
//        $consulta->bindValue(':id_usuario', $id);
//
//        $consulta->execute();
//
//        $row = $consulta->fetch(\PDO::FETCH_ASSOC);
//
//        if ($row === false)
//            return false;
//
//        $ua = new UsuarioAcademico();
//        $ua->setNome($row['nome']);
//        $ua->setEmail($row['email']);
//        $ua->setDocumento($row['cpf']);
//        $ua->setConfirmado($row['confirmado'] == 1 ? true : false);
//        $ua->setCidade($row['cidade']);
//        $ua->setEstado($row['estado']);
//        $ua->setSenha($row['senha']);
//        $ua->setAreaDePesquisa($row['area_de_pesquisa']);
//        $ua->setDepartamento($row['departamento']);
//        $ua->setLaboratorio($row['laboratorio']);
//        $ua->setEmailAlternativo($row['email_alternativo']);
//        $ua->setNivelAcesso((int) $row['nivel_acesso']);
//        $ua->setGenero((int) $row['genero']);
//        $ua->setTelefone($row['telefone']);
//        $ua->setTitulo((int) $row['titulo']);
//
//
//
//    }

    /**
     * @param int $id_usuario
     * @return bool
     */
    public function existeId(int $id_usuario) : bool {
        $sql = sprintf("select * from usuarios where id_usuario = :id_usuario");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_usuario', $id_usuario);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }

    /**
     * @param string $documento
     * @return bool
     */
    public function existeDocumento($documento) : bool {
        $sql = sprintf("select * from usuarios where cpf = :documento");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':documento', $documento);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }

    /**
     * @param UsuarioAcademico $usuario
     * @return bool
     */
    public function atualizar(UsuarioAcademico $usuario) : bool {
        $usuario_antigo = $this->existeId($usuario->getId());
        if ($usuario_antigo === false)
            return false;

        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("update usuarios set cpf = :cpf, nome = :nome, email = :email, senha = :senha, estado = :estado, cidade = :cidade,
            departamento = :departamento, laboratorio = :laboratorio, area_de_pesquisa = :area_de_pesquisa, telefone
            = :telefone, email_alternativo = :email_alternativo, nivel_acesso = :nivel_acesso, uid = :uid, confirmado
             = :confirmado, email_confirmado = :email_confirmado, titulo = :titulo, genero = :genero, limite =
             :limite, ies = :ies, saudacao = :saudacao where id_usuario = :id_usuario limit 1");
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':id_usuario', $usuario->getId(), \PDO::PARAM_INT);
            $consulta->bindValue(':cpf', $usuario->getDocumento());
            $consulta->bindValue(':nome', $usuario->getNome());
            $consulta->bindValue(':email', $usuario->getEmail());
            $consulta->bindValue(':senha', $usuario->getSenha());
            $consulta->bindValue(':estado', $usuario->getEstado());
            $consulta->bindValue(':cidade', $usuario->getCidade());
            $consulta->bindValue(':departamento', $usuario->getDepartamento());
            $consulta->bindValue(':laboratorio', $usuario->getLaboratorio());
            $consulta->bindValue(':area_de_pesquisa', $usuario->getAreaDePesquisa());
            $consulta->bindValue(':telefone', $usuario->getTelefone());
            $consulta->bindValue(':email_alternativo', $usuario->getEmailAlternativo());
            $consulta->bindValue(':nivel_acesso', $usuario->getNivelAcesso(), \PDO::PARAM_INT);
            $consulta->bindValue(':uid', $usuario->getUid());
            $consulta->bindValue(':confirmado', $usuario->confirmado(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':email_confirmado', $usuario->emailConfirmado(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':titulo', $usuario->getTitulo(), \PDO::PARAM_INT);
            $consulta->bindValue(':genero', $usuario->getGenero() == "M" ? 1 : 2, \PDO::PARAM_INT);
            $consulta->bindValue(':limite', $usuario->getLimite(), \PDO::PARAM_INT);
            $consulta->bindValue(':ies', $usuario->getIes());
            $consulta->bindValue(':saudacao', $usuario->getSaudacao(), \PDO::PARAM_INT);

            $consulta->execute();

            //print_p($consulta->errorInfo());

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
     * @param int $id
     */
    public function deletar(int $id) {
        // TODO: Implement deletar() method.
    }

    /**
     * @return array
     */
    public function obterTodos() : array {
        return array();
    }
}