<?php

    /**
     * SMail.class [  ]
     * 
     * 
     * @link www.lifeweb.com.br LifeWeb
     * @author Jhon Lennon S. almeida <jhonlennon21@gmail.com>
     * @copyright (c) 2014, Jhon Lennon S. Almeida LifeWeb Soluções para Web e Arte Gráfica
     */
    class SMail {

        /** @var PHPMailer */
        private $SMTP;

        /** @var SmtpVO */
        private $Config;

        public function __construct() {
            $this->SMTP = new PHPMailer;
            $this->getConfig();
        }

        /**
         * Remetente
         * @param string $nome
         * @param string $email
         */
        public function setFrom($nome, $email) {
            $this->SMTP->SetFrom($this->SMTP->Username, $nome);
            $this->SMTP->clearReplyTos();
            $this->SMTP->AddReplyTo($email, $nome);
        }

        /**
         * Destinatário
         * @param string $name
         * @param string $email
         */
        public function addAddress($name, $email) {
            $this->SMTP->AddAddress($email, $name);
        }

        /**
         * Anexando arquivo
         * @param string $file Diretório completo do arquivo
         * @param type $name Nome do arquivo para anexar
         */
        public function addAttachment($file, $name = null) {
            if (file_exists($file)) {
                $this->SMTP->addAttachment($file, !$name ? basename($file) : $name);
            }
        }

        /**
         * Informa o conteúdo da mensagem
         * @param type $assunto
         * @param type $mensagem
         */
        public function setContent($assunto = null, $mensagem = null) {
            if (!empty($assunto)) {
                $this->SMTP->Subject = $assunto;
            }
            $this->SMTP->Body = "<!doctype html><html><head><title>{$assunto}</title><meta charset='UTF-8' /></head><body>{$mensagem}</body></html>";
            $this->SMTP->msgHTML($mensagem);
        }

        /**
         * Informa o corpo da mensagem
         * @param type $mensagem
         */
        public function setBody($mensagem) {
            $this->setContent(null, $mensagem);
        }

        /**
         * Envia a mensagem
         * @return boolean
         * @throws Exception
         */
        public function Send() {
            if (!$this->SMTP->Send()) {
                throw new Exception($this->SMTP->ErrorInfo);
            }
            return true;
        }

        /**
         * Retorna as configurações de envio
         * @return SmtpVO
         * @throws Exception
         */
        function getConfig() {
            if (!$this->Config) {
                $this->Config = APP::getInstanceModel('Smtp')->getConfig();
                if (empty($this->Config)) {
                    throw new Exception('Não foi possível carregar as configurações SMTP.');
                }
                $this->SMTP->IsSMTP();
                $this->SMTP->IsHTML();
                $this->SMTP->CharSet = 'UTF-8';
                $this->SMTP->Host = $this->Config->getHost();
                $this->SMTP->Username = $this->Config->getLogin();
                $this->SMTP->Password = $this->Config->getSenha();
                $this->SMTP->Port = $this->Config->getPorta();
                $this->SMTP->SMTPAuth = $this->Config->getAutenticar() ? true : false;
                $this->SMTP->SMTPSecure = $this->Config->getProtocolo();
                $this->SMTP->Subject = $this->Config->getAssunto();
                $this->setFrom($this->Config->getNome(), $this->Config->getEmail());
            }
            return $this->Config;
        }

    }
    