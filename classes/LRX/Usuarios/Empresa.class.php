<?php
/**
 * Created by PhpStorm.
 * User: romulo
 * Date: 27/03/17
 * Time: 12:55
 */

namespace LRX\Usuarios;


class Empresa {

    private $id;
    private $cnpj;
    private $razao_nome;
    private $sigla;
    private $endereco = array();
    private $inscricao_estadual;
    private $inscricao_municipal;
    private $site;

    public function __construct($cnpj, $razao_nome, $sigla = null, $endereco, $inscricao_estadual = null, $inscricao_municipal = null, $site = null) {
        $this->cnpj = $cnpj;
        $this->razao_nome = $razao_nome;
        $this->sigla = $sigla;
        $this->endereco = $endereco;
        $this->inscricao_estadual = $inscricao_estadual;
        $this->inscricao_municipal = $inscricao_estadual;
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }



    /**
     * @return mixed
     */
    public function getCnpj()
    {
        return $this->cnpj;
    }

    /**
     * @param mixed $cnpj
     */
    public function setCnpj($cnpj)
    {
        $this->cnpj = $cnpj;
    }

    /**
     * @return mixed
     */
    public function getRazaoNome()
    {
        return $this->razao_nome;
    }

    /**
     * @param mixed $razao_nome
     */
    public function setRazaoNome($razao_nome)
    {
        $this->razao_nome = $razao_nome;
    }

    /**
     * @return null
     */
    public function getSigla()
    {
        return $this->sigla;
    }

    /**
     * @param null $sigla
     */
    public function setSigla($sigla)
    {
        $this->sigla = $sigla;
    }

    /**
     * @return array
     */
    public function getEndereco(): array
    {
        return $this->endereco;
    }

    /**
     * @param array $endereco
     */
    public function setEndereco(array $endereco)
    {
        $this->endereco = $endereco;
    }


    /**
     * @return null
     */
    public function getInscricaoEstadual()
    {
        return $this->inscricao_estadual;
    }

    /**
     * @param null $inscricao_estadual
     */
    public function setInscricaoEstadual($inscricao_estadual)
    {
        $this->inscricao_estadual = $inscricao_estadual;
    }

    /**
     * @return null
     */
    public function getInscricaoMunicipal()
    {
        return $this->inscricao_municipal;
    }

    /**
     * @param null $inscricao_municipal
     */
    public function setInscricaoMunicipal($inscricao_municipal)
    {
        $this->inscricao_municipal = $inscricao_municipal;
    }

    /**
     * @return null
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param null $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }



}