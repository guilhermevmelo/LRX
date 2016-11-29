<?php

/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 1/27/16
 * Time: 18:39
 */

namespace LRX\Usuarios;

require_once "../autoload.php";

class Aluno extends UsuarioAcademico {
    private $professor = null;  // LRX\Professor
    private $vinculo = 1;       // 1 - IC | 2 - Mestrado | 3 - Doutorado | 4 - Técnico | 5 - Pesquisador

    public function __construct($nome = null, $email = null, $documento = null, Professor $professor = null, $vinculo = 1, $limite = 4, $uid =
    null, $id = null) {
        $this->id           = $id;
        $this->nome         = $nome;
        $this->email        = $email;
        $this->documento    = $documento;
        $this->professor    = $professor;
        $this->limite       = $limite;
        $this->vinculo      = $vinculo;
        $this->uid          = $uid ?? $this->gerarUid();
        $this->nivel_acesso = 1;

        $this->telefone = "(00) 00000-0000";
        $this->titulo = 0;
        $this->ies = "UFC";

    }

    /**
     * @return int
     */
    public function getVinculo() {
        return $this->vinculo;
    }

    /**
     * @param int $vinculo
     */
    public function setVinculo(int $vinculo) {
        $this->vinculo = $vinculo;
    }

    /**
     * @return Professor | null
     */
    public function getProfessor() {
        return $this->professor;
    }

    /**
     * @param Professor $professor
     */
    public function setProfessor(Professor $professor) {
        $this->professor = $professor;
    }


}