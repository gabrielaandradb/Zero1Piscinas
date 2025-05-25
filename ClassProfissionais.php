<?php

class Profissionais extends Usuarios {
    private $id;
    private $especialidades;
    private $experienciaAnos;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getEspecialidades() {
        return $this->especialidades;
    }

    public function setEspecialidades($especialidades) {
        $this->especialidades = $especialidades;
    }

    public function getExperienciaAnos() {
        return $this->experienciaAnos;
    }

    public function setExperienciaAnos($experienciaAnos) {
        $this->experienciaAnos = $experienciaAnos;
    }
}
