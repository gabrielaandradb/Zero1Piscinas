<?php

class Servicos {
    private $id;
    private $piscinaId;
    private $profissionalId;
    private $tipoServico;
    private $descricao;
    private $estatus;
    private $dataSolicitacao;
    private $dataExecucao;
    private $preco;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getPiscinaId() {
        return $this->piscinaId;
    }

    public function setPiscinaId($piscinaId) {
        $this->piscinaId = $piscinaId;
    }

    public function getProfissionalId() {
        return $this->profissionalId;
    }

    public function setProfissionalId($profissionalId) {
        $this->profissionalId = $profissionalId;
    }

    public function getTipoServico() {
        return $this->tipoServico;
    }

    public function setTipoServico($tipoServico) {
        $this->tipoServico = $tipoServico;
    }

    public function getDescricao() {
        return $this->descricao;
    }

    public function setDescricao($descricao) {
        $this->descricao = $descricao;
    }

    public function getEstatus() {
        return $this->estatus;
    }

    public function setEstatus($estatus) {
        $this->estatus = $estatus;
    }

    public function getDataSolicitacao() {
        return $this->dataSolicitacao;
    }

    public function setDataSolicitacao($dataSolicitacao) {
        $this->dataSolicitacao = $dataSolicitacao;
    }

    public function getDataExecucao() {
        return $this->dataExecucao;
    }

    public function setDataExecucao($dataExecucao) {
        $this->dataExecucao = $dataExecucao;
    }

    public function getPreco() {
        return $this->preco;
    }

    public function setPreco($preco) {
        $this->preco = $preco;
    }
}
