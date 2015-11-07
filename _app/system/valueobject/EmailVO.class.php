<?php

    class EmailVO extends ValueObject {

        private $Ref;
        private $Nome;
        private $Email;
        private $Codigo;
        private $Status = 1;

        function getRef() {
            return $this->Ref;
        }

        function getNome() {
            return $this->Nome;
        }

        function getEmail() {
            return VALIDAR::email($this->Email) ? strtolower($this->Email) : null;
        }

        function getCodigo() {
            return $this->Codigo;
        }

        function getStatus() {
            return $this->Status;
        }

        function setRef($Ref) {
            $this->Ref = $Ref;
        }

        function setNome($Nome) {
            $this->Nome = $Nome;
        }

        function setEmail($Email) {
            $this->Email = $Email;
        }

        function setCodigo($Codigo) {
            $this->Codigo = $Codigo;
        }

        function setStatus($Status) {
            $this->Status = $Status;
        }

        function check() {
            if (!$this->Email) {
                throw new Exception('Informe o e-mail.');
            } else if (!$this->getEmail()) {
                throw new Exception('E-mail inválido.');
            } else if ($busca = APP::getInstanceModel('Emails')->Lista('WHERE a.id != :id AND a.email = :email AND a.ref = :ref AND a.status != 99 LIMIT 1', [
                'id' => $this->getId(),
                'email' => $this->getEmail(),
                'ref' => $this->getRef(),
                    ])) {
                if ($busca[0]->getStatus() == 99) {
                    $this->getId($busca[0]->getId());
                    $this->setStatus(1);
                } else {
                    throw new Exception('E-mail já está cadastrado.');
                }
            }
        }

    }
    