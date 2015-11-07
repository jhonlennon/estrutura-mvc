<?php

    class UserTypeVO extends ValueObject {

        private $Title;
        private $Permissoes;
        private $Status = 1;

        function getTitle() {
            return $this->Title;
        }

        function getPermissoes($inArray = false) {
            return $inArray ? stringToArray($this->Permissoes) : arrayToString($this->Permissoes);
        }

        function getStatus() {
            return $this->Status;
        }

        function setTitle($Title) {
            $this->Title = $Title;
        }

        function setPermissoes($Permissoes) {
            $this->Permissoes = $Permissoes;
        }

        function setStatus($Status) {
            $this->Status = $Status;
        }

    }
    