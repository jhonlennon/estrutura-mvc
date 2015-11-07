<?php

    class BannerVO extends ValueObject {

        private $Ref;
        private $Title;
        private $Subtitle;
        private $Legenda;
        private $Link;
        private $Target;
        private $Inicio;
        private $Fim;
        private $Dias;
        private $Destaque;
        private $Status = 1;

        function getRef() {
            return $this->Ref;
        }

        function getTitle() {
            return $this->Title;
        }

        function getSubtitle() {
            return $this->Subtitle;
        }

        function getLegenda() {
            return $this->Legenda;
        }

        function getLink() {
            return $this->Link;
        }

        function getTarget() {
            return $this->Target;
        }

        function getInicio($format = false) {
            return $format ? Date::formatData($this->Inicio) : Date::data($this->Inicio);
        }

        function getFim($format = false) {
            return $format ? Date::formatData($this->Fim) : Date::data($this->Fim);
        }

        function getDias($toArray = false) {
            return $toArray ? stringToArray($this->Dias) : arrayToString($this->Dias);
        }

        function getDestaque() {
            return $this->Destaque;
        }

        function getStatus() {
            return $this->Status;
        }

        function setRef($Ref) {
            $this->Ref = $Ref;
        }

        function setTitle($Title) {
            $this->Title = $Title;
        }

        function setSubtitle($Subtitle) {
            $this->Subtitle = $Subtitle;
        }

        function setLegenda($Legenda) {
            $this->Legenda = $Legenda;
        }

        function setLink($Link) {
            $this->Link = $Link;
        }

        function setTarget($Target) {
            $this->Target = $Target;
        }

        function setInicio($Inicio) {
            $this->Inicio = $Inicio;
        }

        function setFim($Fim) {
            $this->Fim = $Fim;
        }

        function setDias($Dias) {
            $this->Dias = $Dias;
        }

        function setDestaque($Destaque) {
            $this->Destaque = $Destaque;
        }

        function setStatus($Status) {
            $this->Status = $Status;
        }

    }
    