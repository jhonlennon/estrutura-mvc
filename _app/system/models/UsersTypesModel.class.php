<?php

    class UsersTypesModel extends Model {

        protected $Table = 'users_types';
        protected $ValueObject = 'UserTypeVO';

        /**
         * Retorna os tipos de conta
         * @param int $UserType
         * @return array
         */
        function getUserTypes($UserType = null) {
            return $this->Lista("WHERE (:type IS NULL OR :type = 0 OR a.permissoes LIKE CONCAT('%[',:type,']%')) ORDER BY a.title ASC", [
                        'type' => $UserType,
            ]);
        }

        /**
         * Retorna a lista de tipos de conta
         * @param int $UserType
         * @return string|select.options
         */
        function options($UserType = null) {
            ob_start();
            foreach ($this->Lista('WHERE a.status != 99 AND (:type IS NULL OR :type = 1 OR a.permissoes LIKE CONCAT("%[",:type,"]%")) ORDER BY a.title ASC', [
                'type' => $UserType,
            ]) as $v) {
                echo formOption($v->getTitle(), $v->getId());
            }
            return ob_get_clean();
        }

    }
    