<?php

    class MenuVO extends ValueObject {

        private $Root;
        private $Ordem;
        private $Title;
        private $Legenda;
        private $Arquivo;
        private $Controller;
        private $Variaveis;
        private $OnClick;
        private $Class;
        private $Icone;
        private $Manual;
        private $Permissao;
        private $Principal;
        private $Status = 1;

        function getRoot() {
            return (int) $this->Root;
        }

        function getOrdem() {
            return $this->Ordem ? (int) $this->Ordem : null;
        }

        function getTitle() {
            return $this->Title;
        }

        function getLegenda() {
            return $this->Legenda;
        }

        function getArquivo() {
            return $this->Arquivo;
        }

        function getController() {
            return $this->Controller;
        }

        function getVariaveis() {
            return $this->Variaveis;
        }

        function getOnClick() {
            return $this->OnClick;
        }

        function getClass() {
            return $this->Class;
        }

        function getIcone() {
            return $this->Icone;
        }

        function getManual() {
            return $this->Manual;
        }

        function getPermissao($toArray = false) {
            return $toArray ? stringToArray($this->Permissao) : arrayToString($this->Permissao);
        }

        function getPrincipal() {
            return ($this->Principal > 0 ? (int) $this->Principal : null);
        }

        function getStatus() {
            return (int) $this->Status;
        }

        function setRoot($Root) {
            $this->Root = $Root;
        }

        function setOrdem($Ordem) {
            $this->Ordem = $Ordem;
        }

        function setTitle($Title) {
            $this->Title = $Title;
        }

        function setLegenda($Legenda) {
            $this->Legenda = $Legenda;
        }

        function setArquivo($Arquivo) {
            $this->Arquivo = $Arquivo;
        }

        function setController($Controller) {
            $this->Controller = $Controller;
        }

        function setVariaveis($Variaveis) {
            $this->Variaveis = $Variaveis;
        }

        function setOnClick($OnClick) {
            $this->OnClick = $OnClick;
        }

        function setClass($Class) {
            $this->Class = $Class;
        }

        function setIcone($Icone) {
            $this->Icone = $Icone;
        }

        function setManual($Manual) {
            $this->Manual = $Manual;
        }

        function setPermissao($Permissao) {
            $this->Permissao = stringToArray($Permissao);
        }

        function setPrincipal($Principal) {
            $this->Principal = $Principal;
        }

        function setStatus($Status) {
            $this->Status = $Status;
        }

        function check() {

            /** @var menuModel */
            $model = APP::getInstance('MenuModel');

            if ($this->getId() && $this->getId() == $this->getRoot()) {
                throw new Exception('O menu não pode ser root dele mesmo.');
            }
        }

    }
    