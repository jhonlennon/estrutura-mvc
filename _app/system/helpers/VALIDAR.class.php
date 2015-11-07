<?php

    class VALIDAR {

        public static function cep($cep) {
            $cep = trim($cep);
            if (preg_match("/^[0-9]{2}\.?[0-9]{3}\-?[0-9]{3}$/", $cep)) {
                return true;
            } else {
                return false;
            }
        }

        public static function username($value) {
            if (strlen($value) < 5) {
                return "O login precisa conter no mínimo 5 caracteres.";
            } else if (strlen($value) > 20) {
                return "O login precisa conter no máximo 20 caracteres.";
            } else if (preg_match("/[^0-9a-z]/i", $value) == 1) {
                return "O login só pode conter letras e números.";
            } else if (preg_match("/^[0-9]/", $value) == 1) {
                return "O login não pode começar com um número.";
            } else {
                return true;
            }
        }

        public static function password($value) {
            if (strlen($value) < 5) {
                return "A senha precisa conter no mínimo 5 caracteres.";
            } else if (strlen($value) > 20) {
                return "A senha precisa conter no máximo 20 caracteres.";
            } else if (preg_match("/[^0-9a-z\!\@\#\$\%\*\-\(\)\+]/i", $value) == 1) {
                return "A senha só pode conter letras e números e símbolos(!@#$%*-+).";
            } else {
                return true;
            }
        }

        public static function hora($hora) {
            if (preg_match("/^([0-1][0-9]|[2][0-3])(:([0-5][0-9])){1,2}$/", $hora) || preg_match("/^([0-1][0-9]|[2][0-3])(:([0-5][0-9])){1,2}(:([0-5][0-9])){1,2}$/", $hora)) {
                return true;
            }
            return false;
        }

        public static function email($email) {
            if (preg_match("/^([[:alnum:]_.-]){3,}@([[:lower:][:digit:]_.-]{3,})(\.[[:lower:]]{2,3})(\.[[:lower:]]{2})?$/i", $email)) {
                return true;
            } else {
                return false;
            }
        }

        function validaemail($email) {
            if (!ereg('^([a-zA-Z0-9.-])*([@])([a-z0-9]).([a-z]{2,3})', $email)) {
                return false;
            } else {
                $dominio = explode('@', $email);
                if (!checkdnsrr($dominio[1], 'A')) {
                    $mensagem = 'E-mail Inv&aacute;lido!';
                    return false;
                } else {
                    return true;
                }
            }
        }

        public static function data($value) {
            return Date::data($value) ? true : false;
        }

        public static function link($value) {
            return preg_match("/^(http[s]?://|ftp://)?(www\.)?[a-zA-Z0-9-\.]+\.(com|org|net|mil|edu|ca|co.uk|com.au|gov|br)$/", $value) ? true : false;
        }

        public static function ip($value) {
            return preg_match("/^((25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})\.){3}(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[0-9]{1,2})$/", $value) ? true : false;
        }

        public static function cpf($cpf) {
            // Verifiva se o número digitado contém todos os digitos
            $cpf = str_pad(preg_replace('/[^0-9]/', '', $cpf), 11, '0', STR_PAD_LEFT);
            // Verifica se nenhuma das sequências abaixo foi digitada, caso seja, retorna falso
            if (strlen($cpf) != 11 || $cpf == '00000000000' || $cpf == '11111111111' || $cpf == '22222222222' || $cpf == '33333333333' || $cpf == '44444444444' || $cpf == '55555555555' || $cpf == '66666666666' || $cpf == '77777777777' || $cpf == '88888888888' || $cpf == '99999999999') {
                return false;
            } else {
                // Calcula os números para verificar se o CPF é verdadeiro
                for ($t = 9; $t < 11; $t++) {
                    for ($d = 0, $c = 0; $c < $t; $c++) {
                        $d += $cpf{$c} * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($cpf{$c} != $d) {
                        return false;
                    }
                }
                return true;
            }
        }

        public static function cnpj($cnpj) {
            $cnpj = str_pad(preg_replace('/[^0-9]/', '', $cnpj), 14, '0', STR_PAD_RIGHT);
            //Etapa 1: Cria um array com apenas os digitos numéricos, isso permite receber o cnpj em diferentes formatos como "00.000.000/0000-00", "00000000000000", "00 000 000 0000 00" etc...
            $j = 0;
            for ($i = 0; $i < (strlen($cnpj)); $i++) {
                if (is_numeric($cnpj[$i])) {
                    $num[$j] = $cnpj[$i];
                    $j++;
                }
            }
            //Etapa 2: Conta os dígitos, um Cnpj válido possui 14 dígitos numéricos.
            if (count($num) != 14) {
                $isCnpjValid = false;
            }
            //Etapa 3: O número 00000000000 embora não seja um cnpj real resultaria um cnpj válido após o calculo dos dígitos verificares e por isso precisa ser filtradas nesta etapa.
            if ($num[0] == 0 && $num[1] == 0 && $num[2] == 0 && $num[3] == 0 && $num[4] == 0 && $num[5] == 0 && $num[6] == 0 && $num[7] == 0 && $num[8] == 0 && $num[9] == 0 && $num[10] == 0 && $num[11] == 0) {
                $isCnpjValid = false;
            }
            //Etapa 4: Calcula e compara o primeiro dígito verificador.
            else {
                $j = 5;
                for ($i = 0; $i < 4; $i++) {
                    $multiplica[$i] = $num[$i] * $j;
                    $j--;
                }

                $soma = array_sum($multiplica);
                $j = 9;

                for ($i = 4; $i < 12; $i++) {
                    $multiplica[$i] = $num[$i] * $j;
                    $j--;
                }
                $soma = array_sum($multiplica);
                $resto = $soma % 11;

                if ($resto < 2) {
                    $dg = 0;
                } else {
                    $dg = 11 - $resto;
                }

                if ($dg != $num[12]) {
                    $isCnpjValid = false;
                }
            }

            //Etapa 5: Calcula e compara o segundo dígito verificador.
            if (!isset($isCnpjValid)) {
                $j = 6;
                for ($i = 0; $i < 5; $i++) {
                    $multiplica[$i] = $num[$i] * $j;
                    $j--;
                }
                $soma = array_sum($multiplica);
                $j = 9;

                for ($i = 5; $i < 13; $i++) {
                    $multiplica[$i] = $num[$i] * $j;
                    $j--;
                }

                $soma = array_sum($multiplica);
                $resto = $soma % 11;

                if ($resto < 2) {
                    $dg = 0;
                } else {
                    $dg = 11 - $resto;
                }

                if ($dg != $num[13]) {
                    $isCnpjValid = false;
                } else {
                    $isCnpjValid = true;
                }
            }

            return $isCnpjValid;
        }

    }

?>