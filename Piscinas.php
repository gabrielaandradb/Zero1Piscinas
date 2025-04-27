<?php
class Piscinas {
    private $id;
    private $clienteId;
    private $tamanho;
    private $tipo;
    private $profundidade;
    private $dataInstalacao;
    private $observacoes;
    private $enderecoCliente;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getClienteId() {
        return $this->clienteId;
    }

    public function setClienteId($clienteId) {
        $this->clienteId = $clienteId;
    }

    public function getTamanho() {
        return $this->tamanho;
    }

    public function setTamanho($tamanho) {
        $this->tamanho = $tamanho;
    }

    public function getTipo() {
        return $this->tipo;
    }

    public function setTipo($tipo) {
        $this->tipo = $tipo;
    }

    public function getProfundidade() {
        return $this->profundidade;
    }

    public function setProfundidade($profundidade) {
        $this->profundidade = $profundidade;
    }

    public function getDataInstalacao() {
        return $this->dataInstalacao;
    }

    public function setDataInstalacao($dataInstalacao) {
        $this->dataInstalacao = $dataInstalacao;
    }

    public function getObservacoes() {
        return $this->observacoes;
    }

    public function setObservacoes($observacoes) {
        $this->observacoes = $observacoes;
    }

    public function getEnderecoCliente() {
        return $this->enderecoCliente;
    }

    public function setEnderecoCliente($enderecoCliente) {
        $this->enderecoCliente = $enderecoCliente;
    }
}
