<?php

    /**
     * Seo.class [ HELLPER ]
     * Define os valores e escreve as meta tags no html
     * 
     * @link www.lifeweb.com.br LifeWeb
     * @author Jhon Lennon S. almeida <jhonlennon21@gmail.com>
     * @copyright (c) 2014, Jhon Lennon S. Almeida LifeWeb Soluções para Web e Arte Gráfica
     */
    final class Seo {

        private static $Charset = CHARSET;
        private static $SEO = [];
        private static $Imagens = [];
        private static $Videos = [];
        private static $Sounds = [];
        private static $CSS = [];
        private static $JS = [];

        public static function reset() {
            self::$SEO = [];
            self::$Imagens = [];
            self::$Videos = [];
            self::$Sounds = [];
            self::$CSS = [];
            self::$JS = [];
        }

        /**
         * Adiciona um novo valor
         * @param string Título da tag ou do atributo
         * @param string Valor da tag ou valor do atributo
         * @param string Valor do conteúdo
         */
        public static function addValue() {
            $args = func_get_args();
            if (count($args) == 2) {
                self::$SEO[$args[0]] = $args[1];
            } else if (count($args) == 3) {
                self::$SEO[$args[0]][$args[1]] = $args[2];
            } else {
                trigger_error("SEOError:addValue: Número de argumentos inválidos.", E_USER_NOTICE);
            }
        }

        /**
         * Altera o charset da página
         * @param string $Charset
         */
        public static function setCharset($Charset) {
            self::$Charset = $Charset;
        }

        /**
         * Seta o Favicon a página
         * @param string $Source
         */
        public static function setFavicon($Source) {
            self::addValue('icon', $Source);
        }

        /**
         * Informa o título da página
         * @param string $value
         */
        public static function setTitle($value) {
            if ($value = trim($value)) {
                self::addValue('title', $value);
                self::addValue('name', 'title', $value);
                self::addValue('property', 'og:title', $value);
            }
        }

        public static function getTitle() {
            return self::getValue('title');
        }

        /**
         * Informa a descrição da página
         * @param string $value
         */
        public static function setDescription($value) {
            if ($value = trim($value)) {
                self::addValue('name', 'description', $value);
                self::addValue('property', 'og:description', $value);
            }
        }

        /**
         * Informa as palavras chave da página
         * @param string $value
         */
        public static function setKeywords($value) {
            if ($value = trim($value)) {
                self::addValue('name', 'keywords', $value);
            }
        }

        /**
         * Adiciona as Tags OpenGraph de imgem (og:image)
         * @param string $url Ex: http://www.seusite.com.br/images/img.jpg
         * @param int $width Ex: 200
         * @param int $height Ex: 200
         * @param string $type Ex: image/jpeg
         * @param string $secureUrl Ex: https://secureuri.seusite.com.br/images/img.jpg
         */
        public static function addImage($url, $width = null, $height = null, $type = null, $secureUrl = null) {
            self::$Imagens[] = array(
                'og:image' => $url,
                'og:image:secure_url' => $secureUrl,
                'og:image:type' => $type,
                'og:image:width' => $width,
                'og:image:height' => $height
            );
        }

        /**
         * Adiciona as Tags OpenGraph de vídeo (og:video)
         * @param string $url Ex: http://www.seusite.com.br/videos/video.mp4
         * @param int $width Ex: 600
         * @param int $height Ex: 350
         * @param string $type Ex: video/mp4
         * @param string $secureUrl Ex: https://secureuri.seusite.com.br/videos/video.mp4
         */
        public static function addVideo($url, $width = null, $height = null, $type = null, $secureUrl = null) {
            self::$Videos[] = array(
                'og:video' => $url,
                'og:video:secure_url' => $secureUrl,
                'og:video:type' => $type,
                'og:video:width' => $width,
                'og:video:height' => $height
            );
        }

        /**
         * Adiciona as tags OpenGraph de audio (og:audio)
         * @param string $url Ex: http://www.seusite.com.br/sounds/sound.mp3
         * @param string $type Ex: audio/mpeg
         * @param type $secureUrl Ex: https://secureuri.seusite.com.br/sounds/sound.mp3
         */
        public static function addAudio($url, $type = null, $secureUrl = null) {
            self::$Sounds[] = array(
                'og:audio' => $url,
                'og:audio:secure_url' => $secureUrl,
                'og:audio:type' => $type
            );
        }

        /**
         * Adicionar um arquivo CSS a ser incluído no html
         * @param string $src diretório do arquivo css
         * @param string $media Ex: screen and (max-device-width: 800px)
         */
        public static function addCss($src, $media = null, $type = 'text/css') {
            self::$CSS[] = array(
                'href' => (string) $src,
                'media' => (string) $media,
                'type' => (string) $type,
            );
        }

        /**
         * Adicionar um arquivo JavaScript a ser incluído no html
         * @param string $src
         */
        public static function addJs($src) {
            self::$JS[] = (string) $src;
        }

        /**
         * Recupera um valor do SEO
         * @param string Nome da Tag ou do atributo
         * @param string Nome do atribuo
         */
        public static function getValue() {
            $args = func_get_args();
            if (count($args) == 1) {
                return isset(self::$SEO[$args[0]]) ? self::$SEO[$args[0]] : null;
            } else if (count($args) == 2) {
                return isset(self::$SEO[$args[0]][$args[1]]) ? self::$SEO[$args[0]][$args[1]] : null;
            } else {
                trigger_error("SEOError:getValue:  Número de argumentos inválidos.", E_USER_NOTICE);
            }
        }

        /**
         * Escreve as meta tags 
         */
        public static function displayHeader() {

            # Charset
            print "<meta charset=\"" . self::$Charset . "\">";

            # Viewport
            print "\n\n\t<meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" >\n";

            # Html5
            print "\n\n\t<!--[if lt IE 9]>";
            print "\n\t\t<script src=\"https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js\" charset=\"" . self::$Charset . "\" ></script>";
            print "\n\t\t<script src=\"https://oss.maxcdn.com/respond/1.4.2/respond.min.js\" charset=\"" . self::$Charset . "\" ></script>";
            print "\n\t<![endif]-->\n";

            # Meta Tags
            foreach (self::$SEO as $attr => $prop) {
                # Metatags
                if (is_array($prop)) {
                    print "\n\t";
                    foreach ($prop as $content => $value) {
                        print "<meta {$attr}=\"{$content}\" content=\"" . htmlspecialchars($value) . "\" />\n\t";
                    }
                }
                # Favicon - Icone da página
                else if ($attr == 'icon' || $attr == 'shortcut' || $attr == 'shortcut icon') {
                    print "\n\t";
                    $type = strtolower(preg_replace('/^.*\.(.*?)(\?.*)?$/', '$1', $prop));
                    if ($type == 'ico') {
                        print "<link rel=\"shortcut icon\" href=\"{$prop}\" type=\"image/x-icon\" />\n\t";
                        print "<link rel=\"shortcut icon\" href=\"{$prop}\" type=\"image/vnd.microsoft.icon\" />\n\t";
                    }
                }
                # Tags
                else {
                    print "\n\t";
                    print "<{$attr}>" . htmlspecialchars($prop) . "</{$attr}>\n\t";
                }
            }

            # Imagens
            if (count(self::$Imagens) > 0) {
                print "\n\t";
                print "<!-- OG Imagens -->\n\t";
                foreach (self::$Imagens as $values) {
                    foreach ($values as $key => $value) {
                        if ($value != null) {
                            print "<meta property=\"{$key}\" content=\"" . htmlspecialchars($value) . "\" />\n\t";
                        }
                    }
                }
            }

            # Vídeos
            if (count(self::$Videos) > 0) {
                print "\n\t";
                print "<!-- OG Vídeos -->\n\t";
                foreach (self::$Videos as $values) {
                    foreach ($values as $key => $value) {
                        if ($value != null) {
                            print "<meta property=\"{$key}\" content=\"" . htmlspecialchars($value) . "\" />\n\t";
                        }
                    }
                    if (end(self::$Videos) !== $values) {
                        print "\n\t";
                    }
                }
            }

            # Sounds
            if (count(self::$Sounds) > 0) {
                print "\n\t";
                print "<!-- OG Sound -->\n\t";
                foreach (self::$Sounds as $values) {
                    foreach ($values as $key => $value) {
                        if ($value != null) {
                            print "<meta property=\"{$key}\" content=\"" . htmlspecialchars($value) . "\" />\n\t";
                        }
                    }
                }
                print "\n\t";
            }

            # CSS
            if (count(self::$CSS) > 0) {
                print "\n\t";
                print "<!-- Folhas de estilo -->\n\t";
                foreach (self::$CSS as $css) {
                    if (preg_match('/<style/i', $css['href'])) {
                        print preg_replace('/[\n\t]/', ' ', trim($css['href'])) . "\n\t";
                    } else {
                        $file = str_replace(base_url(), abs_source(), $css['href']);
                        print "<link rel=\"stylesheet\" href=\"{$css['href']}\" media=\"{$css['media']}\" type=\"{$css['type']}\" />\n\t";
                    }
                }
            }
        }

        /**
         * Display all scripts
         */
        public static function displayFooter() {
            print "\n\t";
            print "<!-- JavaScript -->\n\t";
            print "<script type='text/javascript' ><!--"
                    . "\n\t\tvar URL_APP = '" . base_url() . "';"
                    . "\n\t\tvar URL_MODULE = '" . url() . "';"
                    . "\n\t\tvar CONTROLLER = '" . APP::getControllerName() . "';"
                    . "\n\t\tvar ACTION = '" . APP::getAction() . "';"
                    . "\n\t\tvar MODULE = '" . APP::getCurrentModule() . "';"
                    . "\n\t\tvar MODULE_DEFAULT = '" . APP::getDefaultModule() . "';"
                    . "\n\t--></script>\n\t";
            if (count(self::$JS) > 0) {
                foreach (self::$JS as $src) {
                    if (!preg_match('/^<script/i', $src)) {
                        print "<script type=\"text/javascript\" language=\"javascript\" src=\"{$src}\" charset=\"" . self::$Charset . "\" ></script>\n\t";
                    } else {
                        print "{$src}\n\t";
                    }
                }
            }
            print "<!-- Tempo de execução: " . calc_execution_time(2) . " milesegundos -->\n\n\t";
        }

        /**
         * Retornar todo o conteúdo registrado no SEO
         * @return array
         */
        public static function getSeo() {
            return self::$SEO;
        }

    }
    