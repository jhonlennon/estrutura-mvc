<?php

    class GaleriaVO extends ValueObject {

        private $Ref;
        private $Destaque;
        private $Data;
        private $Title;
        private $Texto;
        private $Descricao;
        private $Keywords;
        private $Status = 1;

        function getRef() {
            return $this->Ref;
        }

        function getDestaque() {
            return (int) $this->Destaque;
        }

        function getData($format = false) {
            return $format ? Date::formatData($this->Data) : Date::data($this->Data);
        }

        function getTitle() {
            return $this->Title;
        }

        function getTexto() {
            return $this->Texto;
        }

        function getDescricao() {
            return $this->Descricao;
        }

        function getKeywords() {
            return $this->Keywords;
        }

        function getStatus() {
            return (int) $this->Status;
        }

        function setRef($Ref) {
            $this->Ref = $Ref;
        }

        function setDestaque($Destaque) {
            $this->Destaque = $Destaque;
        }

        function setData($Data) {
            $this->Data = $Data;
        }

        function setTitle($Title) {
            $this->Title = $Title;
        }

        function setTexto($Texto) {
            $this->Texto = $Texto;
        }

        function setDescricao($Descricao) {
            $this->Descricao = $Descricao;
        }

        function setKeywords($Keywords) {
            $this->Keywords = $Keywords;
        }

        function setStatus($Status) {
            $this->Status = $Status;
        }

    }
    