<?php

    class SmtpVO extends ValueObject {

        private $Nome;
        private $Email;
        private $Host;
        private $Login;
        private $Senha;
        private $Porta;
        private $Autenticar;
        private $Protocolo;
        private $Assunto;

        function getNome() {
            return $this->Nome;
        }

        function getEmail() {
            return $this->Email;
        }

        function getHost() {
            return $this->Host;
        }

        function getLogin() {
            return $this->Login;
        }

        function getSenha($hidden = false) {
            return $hidden ? null : $this->Senha;
        }

        function getPorta() {
            return $this->Porta;
        }

        function getAutenticar() {
            return $this->Autenticar;
        }

        function getProtocolo() {
            return $this->Protocolo;
        }

        function getAssunto() {
            return $this->Assunto;
        }

        function setNome($Nome) {
            $this->Nome = $Nome;
        }

        function setEmail($Email) {
            $this->Email = $Email;
        }

        function setHost($Host) {
            $this->Host = $Host;
        }

        function setLogin($Login) {
            $this->Login = $Login;
        }

        function setSenha($Senha) {
            $this->Senha = $Senha;
        }

        function setPorta($Porta) {
            $this->Porta = $Porta;
        }

        function setAutenticar($Autenticar) {
            $this->Autenticar = $Autenticar;
        }

        function setProtocolo($Protocolo) {
            $this->Protocolo = $Protocolo;
        }

        function setAssunto($Assunto) {
            $this->Assunto = $Assunto;
        }

    }
    