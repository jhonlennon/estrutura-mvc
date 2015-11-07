<?php

    class EstadoVO extends ValueObject {

        private $Codigo;
        private $Title;
        private $Uf;
        private $Pais = 1;

        function getCodigo() {
            return $this->Codigo;
        }

        function getTitle() {
            return $this->Title;
        }

        function getUf() {
            return $this->Uf;
        }

        function getPais() {
            return $this->Pais;
        }

        function setCodigo($Codigo) {
            $this->Codigo = $Codigo;
        }

        function setTitle($Title) {
            $this->Title = $Title;
        }

        function setUf($Uf) {
            $this->Uf = $Uf;
        }

        function setPais($Pais) {
            $this->Pais = $Pais;
        }

    }
    