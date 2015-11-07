<?php

    class ImageVO extends ValueObject {

        private $Insert = __NOW__;
        private $Ref;
        private $RefId;
        private $Title;
        private $Legenda;
        private $Source = 'default.jpg';
        private $Position;
        private $Status = 1;

        /**
         * 
         * @param int $Width
         * @param int $Height
         * @param string $ResizeType preenchimento | crop | proporcional | normal
         * @param int $Quality
         * @param string|int $BGColor
         * @return string
         */
        function Redimensiona($Width, $Height, $ResizeType = 'crop', $Quality = 100, $BGColor = '#FFFFFF') {
            return Utils::redimensionaImg($Width, $Height, abs_source_images(), $this->getSource(), $ResizeType, $Quality, $BGColor);
        }

        function getInsert() {
            return $this->Insert;
        }

        function getRef() {
            return $this->Ref;
        }

        function getRefId() {
            return $this->RefId;
        }

        function getTitle() {
            return $this->Title;
        }

        function getLegenda() {
            return $this->Legenda;
        }

        function getSource($Host = false) {
            return $Host ? base_url_images($this->Source) : $this->Source;
        }

        function getSourceInfo() {
            if (file_exists($filename = ImagensModel::getImagePath() . DIRECTORY_SEPARATOR . $this->getSource())) {
                return getimagesize($filename);
            } else {
                return null;
            }
        }

        function getPosition() {
            return $this->Position;
        }

        function getStatus() {
            return $this->Status;
        }

        function setInsert($Insert) {
            $this->Insert = $Insert;
        }

        function setRef($Ref) {
            $this->Ref = $Ref;
        }

        function setRefId($RefId) {
            $this->RefId = $RefId;
        }

        function setTitle($Title) {
            $this->Title = $Title;
        }

        function setLegenda($Legenda) {
            $this->Legenda = $Legenda;
        }

        function setSource($Source) {
            $this->Source = $Source;
        }

        function setPosition($Position) {
            $this->Position = $Position;
        }

        function setStatus($Status) {
            $this->Status = $Status;
        }

    }
    