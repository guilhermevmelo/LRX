<?php
/**
 * Created by PhpStorm.
 * User: romulo
 * Date: 27/03/17
 * Time: 15:56
 */

namespace LRX\Usuarios;

use LRX\Erro;
use function LRX\print_p;
use const LRX\DSN;
use const LRX\USUARIO;
use const LRX\SENHA;


class UsuarioComercialDAO {

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
    public function criar(UsuarioComercial &$usuario) {

        if ($this->existeDocumento($usuario->getDocumento()))
            return false;

        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("insert into usuarios values (null, :cpf, :nome, :email, :senha, :estado, :cidade, null, null, null, :telefone, :email_alternativo, :nivel_acesso, :uid, :confirmado, 0, null, :genero,
:limite, null, 0)");
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':cpf', $usuario->getDocumento());
            $consulta->bindValue(':nome', $usuario->getNome());
            $consulta->bindValue(':email', $usuario->getEmail());
            $consulta->bindValue(':senha', $usuario->getSenha());
            $consulta->bindValue(':estado', $usuario->getEstado());
            $consulta->bindValue(':cidade', $usuario->getCidade());
            $consulta->bindValue(':telefone', $usuario->getTelefone());
            $consulta->bindValue(':email_alternativo', $usuario->getEmailAlternativo());
            $consulta->bindValue(':email_alternativo', $usuario->getEmailAlternativo());
            $consulta->bindValue(':nivel_acesso', $usuario->getNivelAcesso(), \PDO::PARAM_INT);
            $consulta->bindValue(':uid', $usuario->getUid());
            $consulta->bindValue(':confirmado', $usuario->confirmado(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':genero', $usuario->getGenero(), \PDO::PARAM_INT);
            $consulta->bindValue(':limite', $usuario->getLimite(), \PDO::PARAM_INT);

            $consulta->execute();

            print_p($consulta->errorInfo());

            $sql2 = sprintf("select max(id_usuario) from usuarios");
            $consulta2 = $this->conexao->query($sql2);

            $novoId = (int) $consulta2->fetchColumn(0);

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

    public function existeDocumento($documento) : bool {
        $sql = sprintf("select * from usuarios where cpf = :documento");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':documento', $documento);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }

    public function atualizar(UsuarioComercial $usuario) : bool {
        $usuario_antigo = $this->existeId($usuario->getId());
        if ($usuario_antigo === false)
            return false;

        try {
            $this->conexao->beginTransaction();
            $sql = sprintf("update usuarios set cpf = :cpf, nome = :nome, email = :email, senha = :senha, estado = :estado, cidade = :cidade, telefone
            = :telefone, email_alternativo = :email_alternativo, nivel_acesso = :nivel_acesso, uid = :uid, confirmado
             = :confirmado, email_confirmado = :email_confirmado, genero = :genero, limite =
             :limite, saudacao = :saudacao where id_usuario = :id_usuario limit 1");
            $consulta = $this->conexao->prepare($sql);

            $consulta->bindValue(':id_usuario', $usuario->getId(), \PDO::PARAM_INT);
            $consulta->bindValue(':cpf', $usuario->getDocumento());
            $consulta->bindValue(':nome', $usuario->getNome());
            $consulta->bindValue(':email', $usuario->getEmail());
            $consulta->bindValue(':senha', $usuario->getSenha());
            $consulta->bindValue(':estado', $usuario->getEstado());
            $consulta->bindValue(':cidade', $usuario->getCidade());
            $consulta->bindValue(':telefone', $usuario->getTelefone());
            $consulta->bindValue(':email_alternativo', $usuario->getEmailAlternativo());
            $consulta->bindValue(':nivel_acesso', $usuario->getNivelAcesso(), \PDO::PARAM_INT);
            $consulta->bindValue(':uid', $usuario->getUid());
            $consulta->bindValue(':confirmado', $usuario->confirmado(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':email_confirmado', $usuario->emailConfirmado(), \PDO::PARAM_BOOL);
            $consulta->bindValue(':genero', $usuario->getGenero(), \PDO::PARAM_INT);
            $consulta->bindValue(':limite', $usuario->getLimite(), \PDO::PARAM_INT);
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

    public function existeId(int $id_usuario) : bool {
        $sql = sprintf("select * from usuarios where id_usuario = :id_usuario");
        $consulta = $this->conexao->prepare($sql);
        $consulta->bindValue(':id_usuario', $id_usuario);

        $consulta->execute();

        if ($consulta->fetch() === false)
            return false;
        return true;
    }

}