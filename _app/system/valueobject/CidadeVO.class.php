<?php

    class CidadeVO extends ValueObject {

        private $Title;
        private $Codigo;
        private $UF;
        private $Estado;
        private $EstadoTitle;

        function getTitle() {
            return $this->Title;
        }

        function getCodigo() {
            return $this->Codigo;
        }

        function getUF() {
            return $this->UF;
        }

        function getEstado() {
            return $this->Estado;
        }

        function getEstadoTitle() {
            return $this->EstadoTitle;
        }

        function setTitle($Title) {
            $this->Title = $Title;
        }

        function setCodigo($Codigo) {
            $this->Codigo = $Codigo;
        }

        function setUF($UF) {
            $this->UF = $UF;
        }

        function setEstado($Estado) {
            $this->Estado = $Estado;
        }

        function setEstadoTitle($EstadoTitle) {
            $this->EstadoTitle = $EstadoTitle;
        }

    }
    