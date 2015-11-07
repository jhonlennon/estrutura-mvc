<?PHP

    /* 	
      STRING::rm_specials(VALUE->String);
     */

    $path = realpath(dirname(__FILE__)) . "/";
    STRING::$path = $path;

    class STRING {

        public static $path = "";

        //Remove caracteres especiais
        public static function rm_specials($palavra) {
            $palavranova = self::acentos($palavra);
            $palavranova = str_replace(" ", "_", $palavranova);
            $palavranova = str_replace(array("'", '"', ','), "", $palavranova);
            return strval($palavra);
        }

        public static function arquivo($arquivo_name) {
            $arquivo_name = STRING::rmAcentosIso(self::decode($arquivo_name));
            $arquivo_name = preg_replace("/[\!\@\#\$\%\¨\&\*\(\)\_\-\+\=\§\¬\¢\£\³\²\¹\'\"\°\º\ª\/\?\°^~`´]/", "", $arquivo_name);
            $arquivo_name = str_replace(" ", "_", $arquivo_name);
            return $arquivo_name;
        }

        public static function rmAcentos($string) {
            $palavra = strtr($string, "ŠŒŽšœžŸ¥µÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýÿ", "SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");
            return $palavra;
        }

        public static function rmAcentosIso($string) {
            $acentos = STRING::decodeArray(explode("/", "Š/Œ/Ž/š/œ/ž/Ÿ/¥/µ/À/Á/Â/Ã/Ä/Å/Æ/Ç/È/É/Ê/Ë/Ì/Í/Î/Ï/Ð/Ñ/Ò/Ó/Ô/Õ/Ö/Ø/Ù/Ú/Û/Ü/Ý/ß/à/á/â/ã/ä/å/æ/ç/è/é/ê/ë/ì/í/î/ï/ð/ñ/ò/ó/ô/õ/ö/ø/ù/ú/û/ü/ý/ÿ"));
            $semAcentos = explode("/", "S/O/Z/s/o/z/Y/Y/u/A/A/A/A/A/A/A/C/E/E/E/E/I/I/I/I/D/N/O/O/O/O/O/O/U/U/U/U/Y/s/a/a/a/a/a/a/a/c/e/e/e/e/i/i/i/i/o/n/o/o/o/o/o/o/u/u/u/u/y/y");
            return str_replace($acentos, $semAcentos, $string);
        }

        public static function rmAcentosUtf8($str) {
            $acentos = array("&agrave;", "&aacute;", "&acirc;", "&atilde;", "&auml;", "&egrave;", "&eacute;", "&ecirc;", "&ecirc;", "&igrave;", "&iacute;", "&icirc;", "&iuml;", "&ograve;", "&oacute;", "&ocirc;", "&otilde;", "&ouml;", "&ugrave;", "&uacute;", "&ucirc;", "&uuml;");
            $acentos = array("&Agrave;", "&Aacute;", "&Acirc;", "&Atilde;", "&Auml;", "&Egrave;", "&Eacute;", "&Ecirc;", "&Euml;", "&Igrave;", "&Iacute;", "&Icirc;", "&Ograve;", "&Oacute;", "&Ocirc;", "&Otilde;", "&Ouml;", "&Ugrave;", "&uacute;", "&Ucirc;", "&Uuml;", "&ccedil;", "&Ccedil;", "&ntilde;", "&Ntilde;");
            $normal = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "c", "C", "n", "N");
            return str_replace($acentos, $normal, utf8_decode($str));
        }

        public static function br($value) {
            return preg_replace("/(\\r)?\\n/i", "<br>", $value);
        }

        public static function acentos($string) {
            return self::rmAcentosIso(self::rmAcentos($string));
        }

        //html para flash
        public static function htmlFlash($string) {
            $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
            $string = preg_replace('~&#([0-9]+);~e', 'chr(\\1)', $string);
            $trans_tbl = get_html_translation_table(HTML_ENTITIES);
            $trans_tbl = array_flip($trans_tbl);
            $result = strtr($string, $trans_tbl);
            //$result = strip_tags($result,"<a><b><br><font><img><i><li><p><span><textformat>");
            return $result;
        }

        //Codifica uma string
        public static function encode($string) {
            $value = $string;
            //$value = htmlspecialchars($string,ENT_QUOTES,'iso-8859-1');
            $to_encoding = "UTF-8";
            $from_encoding = self::codificacao($value);
            return str_replace('&nbsp;', ' ', mb_convert_encoding($value, $to_encoding, $from_encoding));
        }

        //Decodifica uma string
        public static function decode($string) {
            $value = $string;
            //$value = htmlspecialchars($string,ENT_QUOTES,'iso-8859-1');
            $to_encoding = "ISO-8859-1";
            $from_encoding = self::codificacao($string);
            return str_replace('&nbsp;', ' ', mb_convert_encoding($value, $to_encoding, $from_encoding));
        }

        public static function codificacao($string) {
            return mb_detect_encoding($string . 'x', 'UTF-8, ISO-8859-1');
        }

        public static function rmTags($string) {
            $value = strip_tags($string, "<a><b><i><tt><p><br><div><img><hr><table><tr><td><font>");
            return $value;
        }

        //Codifica os dados de uma array e retorna uma nova array com os dados codificados
        public static function encodeArray($array) {
            $value = array();
            $keys = array_keys($array);
            for ($i = 0; $i < count($keys); $i++) {
                $key = $keys[$i];
                if (is_array($array[$key])) {
                    $value[$key] = STRING::encodeArray($array[$key]);
                } else {
                    $value[$key] = STRING::encode(strval($array[$key]));
                }
            }
            return $value;
        }

        //Decodifica os dados de uma array e retorna uma nova array com os dados decodificados
        public static function decodeArray($array) {
            $value = array();
            $keys = array_keys($array);
            for ($i = 0; $i < count($keys); $i++) {
                $key = $keys[$i];
                $value[$key] = STRING::decode(strval($array[$key]));
            }
            return $value;
        }

        public static function encodeArrayToString($array) {
            $string = "";
            $keys = array_keys($array);
            for ($i = 0; $i < count($keys); $i++) {
                $string .= ($i == 0 ? "" : "&") . $keys[$i] . "=" . urlencode($array[$keys[$i]]);
            }
            return $string;
        }

        public static function decodeStringToArray($string) {
            $array = array();
            $decode = explode("&", $string);
            if (!empty($string)) {
                for ($i = 0; $i < count($decode); $i++) {
                    $key_value = explode("=", $decode[$i]);
                    $array[$key_value[0]] = urldecode($key_value[1]);
                }
            }
            return $array;
        }

        public static function parse_dir_separator($diretorio) {
            $barra = DIRECTORY_SEPARATOR;
            $return = str_replace("\\", "/", $diretorio);

            if ($barra != "/") {
                $return = str_replace("/", $barra, $return);
            }

            return $return;
        }

        public static function arraySqlInjection($array) {
            $keys = array_keys($array);
            for ($i = 0; $i < count($keys); $i++) {
                $array[$keys[$i]] = self::sqlInjection($array[$keys[$i]]);
            }
            return $array;
        }

        public static function sqlInjection($sql) {
            if (get_magic_quotes_gpc() == 0) {
                $sql = mysql_real_escape_string($sql);
            }
            return $sql;
        }

    }

?>