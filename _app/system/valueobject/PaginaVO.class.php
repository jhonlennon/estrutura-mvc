<?php

    class PaginaVO extends ValueObject {

        protected $Ref;
        protected $Link;
        protected $Title;
        protected $Texto;
        protected $Descricao;
        protected $Keywords;
        protected $Ordem;
        protected $Status = 1;

        function getRef() {
            return $this->Ref;
        }

        function getLink() {
            return $this->Link;
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

        function getOrdem() {
            return (int) $this->Ordem;
        }

        function getStatus() {
            return $this->Status;
        }

        function setRef($Ref) {
            $this->Ref = $Ref;
        }

        function setLink($Link) {
            $this->Link = $Link;
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

        function setOrdem($Ordem) {
            $this->Ordem = $Ordem;
        }

        function setStatus($Status) {
            $this->Status = $Status;
        }

    }
    