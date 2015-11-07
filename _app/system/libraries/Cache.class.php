<?php

    final class Cache {

        private $KeyPath = '';
        private $Key = null;
        private $KeyName = null;
        private $ExpireDate;
        private $Content = null;

        /** @var array Armazendo na memória caches carregados */
        private static $CacheLoads = [];

        /** @var string Tempo padrão de um Cache */
        private $Time;

        /** @var string Pasta onde os arquivos de cache serão gravados */
        private static $Path = 'temp/cache';

        /**
         * Inicia a leitura do cache
         * @param string $Key
         * @param int $CacheTime Minutos
         * @param mixed $Content Quando informado o arquivo de cache é criado
         */
        function __construct($Key = null, $CacheTime = 1, $Content = null) {
            if ($Key !== null) {
                $this->setKey($Key, $Content, $CacheTime);
            }
        }

        function getExpireDate() {
            return $this->ExpireDate;
        }

        /**
         * Inicia o gerenciamento de uma nova chave
         * @param string $Key
         * @param mixed $Content
         */
        public function setKey($Key, $Content = null, $CacheTime = null) {
            # Tempo de cache
            if ($CacheTime > 0) {
                $this->Time = $CacheTime;
            }

            # Extraindo valores
            $this->extractInfos($Key);

            # Setando conteúdo
            if ($Content !== null) {
                $this->setContent($Content);
            }

            # Recuperando conteúdo atual
            else {
                $this->loadCache();
            }
        }

        /**
         * Extrai informações da chave
         * @param string $Key
         */
        private function extractInfos($Key) {

            $this->KeyName = $Key;
            $infos = explode('.', $Key);
            $this->Key = end($infos);
            array_pop($infos);
            $infos = array_values($infos);

            /**
             * Pastas
             */
            if (count($infos)) {
                foreach ($infos as $path) {
                    if (!file_exists(self::getPath() . $this->KeyPath . $path)) {
                        mkdir(self::getPath() . $this->KeyPath . $path, 0777);
                        chmod(self::getPath() . $this->KeyPath . $path, 0777);
                    }
                    $this->KeyPath .= $path . DIRECTORY_SEPARATOR;
                }
            }
        }

        /**
         * Retorna o diretório completo até o arquivo
         * @return string
         */
        private function getPathFile() {
            return $this->getPath() . $this->KeyPath . sha1($this->Key) . '.tmp';
        }

        /**
         * Retorna o diretório
         * @return string
         */
        public static function getPath() {
            return ABSPATH . DIRECTORY_SEPARATOR . self::$Path . DIRECTORY_SEPARATOR;
        }

        /**
         * Lê o conteúdo do Cache
         * @return \System\Helpers\Cache
         */
        private function loadCache() {
            # Verificando se o valor já foi carregado
            if (isset(self::$CacheLoads[$this->KeyName])) {
                $this->Content = self::$CacheLoads[$this->KeyName]['content'];
                $this->ExpireDate = self::$CacheLoads[$this->KeyName]['expire'];
            }
            # Verificando existencia do cache
            else if (file_exists($this->getPathFile())) {
                # Extraindo valores do arquivo
                $content = @unserialize(file_get_contents($this->getPathFile()));
                if ($content and $content['key'] == $this->KeyName) {
                    # Verificando se expirou
                    if (time() <= $content['expire']) {
                        self::$CacheLoads[$this->KeyName] = [
                            'content' => $this->Content = $content['content'],
                            'expire' => $this->ExpireDate = date('Y-m-d H:i:s', $content['expire']),
                        ];
                    }
                }
            }
            # Retorna o próprio objeto
            return $this;
        }

        /**
         * Seta o conteúdo
         * @param mixed $Content
         * @return \System\Helpers\Cache
         */
        function setContent($Content = null, $Time = null) {
            $this->Content = $Content;
            $this->save($Time);
            return $this;
        }

        /**
         * Retorna o conteúdo do cache
         * @return mixed
         */
        public function getContent() {
            return $this->Content;
        }

        /**
         * Limpa o cache
         * @return \System\Helpers\Cache
         */
        public function clear() {
            if (file_exists($this->getPathFile())) {
                unlink($this->getPathFile());
            }
            # Limpando da Array
            if (isset(self::$CacheLoads[$this->KeyName])) {
                unset(self::$CacheLoads[$this->KeyName]);
            }
            return $this;
        }

        public static function ClearAll($path = null) {
            $files = self::getAllFiles(preg_replace('/[\/\\\]$/', null, self::getPath() . $path));
            foreach ($files as $file) {
                if (is_dir($file)) {
                    @rmdir($file);
                } else {
                    @unlink($file);
                }
            }
            return true;
        }

        private static function getAllFiles($Path) {
            $files = [];
            foreach (glob("{$Path}/*", GLOB_BRACE) as $file) {
                $files[] = $file;
                if (is_dir($file)) {
                    $files = array_merge(self::getAllFiles($file), $files);
                }
            }
            return $files;
        }

        /**
         * Salva os dados no cache
         * @param int $Minutes Tempo de cache em minutos 
         * @return \System\Helpers\Cache
         */
        private function save($Minutes = null) {
            if ($this->Content === null) {
                $this->clear();
            } else {
                file_put_contents($this->getPathFile(), serialize([
                    'key' => $this->KeyName,
                    'create' => time(),
                    'expire' => $expire = strtotime('+' . ($Minutes === null ? $this->Time : $Minutes) . ' minutes'),
                    'content' => $this->Content,
                ]));
                chmod($this->getPathFile(), 0777);
                self::$CacheLoads[$this->KeyName] = [
                    'content' => $this->Content,
                    'expire' => $this->ExpireDate = date('Y-m-d H:i:s', $expire),
                ];
            }
            return $this;
        }

        /**
         * Liberando a memória
         */
        public function __destruct() {
            self::$CacheLoads = [];
        }

    }
    