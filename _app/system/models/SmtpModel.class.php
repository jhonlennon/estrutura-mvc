<?php

    class SmtpModel extends Model {

        protected $Table = 'smtp_config';
        protected $ValueObject = 'SmtpVO';

        /** @return SmtpVO */
        public function getConfig() {
            return $this->Lista('LIMIT 1')[0];
        }

    }
    