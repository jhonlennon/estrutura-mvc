<?php

    class OptionVO extends ValueObject {

        private $Root;
        private $Ref;
        private $Title;
        private $Ordem;
        private $Status = 1;

        function getRoot() {
            return $this->Root;
        }

        function getRef() {
            return $this->Ref;
        }

        function getTitle() {
            return $this->Title;
        }

        function getOrdem() {
            return (int) $this->Ordem;
        }

        function getStatus() {
            return (int) $this->Status;
        }

        function setRoot($Root) {
            $this->Root = $Root;
        }

        function setRef($Ref) {
            $this->Ref = $Ref;
        }

        function setTitle($Title) {
            $this->Title = $Title;
        }

        function setOrdem($Ordem) {
            $this->Ordem = $Ordem;
        }

        function setStatus($Status) {
            $this->Status = $Status;
        }

    }
    