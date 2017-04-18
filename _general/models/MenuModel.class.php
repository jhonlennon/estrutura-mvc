<?php

    class MenuModel extends Model {

        /** @var UserVO */
        private $User;
        protected $ValueObject = 'MenuVO';
        protected $Table = 'adm_menu';

        public function __construct() {
            $this->User = Module::getUserLoged();
        }

        /**
         * 
         * @param type $Type
         * @param type $Status
         * @return type
         */
        public function getMenu($Type = 0, $Status = 1) {
            return $this->listMenu(0, $Type, $Status);
        }

        /**
         * 
         * @param int $id
         * @return MenuVO
         */
        public function getById($id) {
            return $this->getByLabel('id', $id, true);
        }

        /**
         * Retorna a página principal que o tipo de usuário tem acesso
         * @return MenuVO
         */
        public function getPaginaPrincipal() {
            $sql = $this->Lista("
                WHERE a.status = 1 AND a.principal > 0
                AND (a.controller != '' OR a.arquivo != '') 
                AND (:type = 0 OR a.permissao LIKE CONCAT('%[',:type,']%') OR :permissoes LIKE CONCAT('%[',a.id,']%')) 
                ORDER BY a.principal ASC", [
                'type' => $this->User->getType(),
                'permissoes' => $this->User->getPermissoes(),
            ]);
            if (count($sql)) {
                return $sql[0];
            } else {
                $menu = $this->listMenu(0, $this->User->getType());
                /* @var $m MenuVO */
                foreach ($menu as $m) {
                    if ($m['menu']->getController()) {
                        return $m['menu'];
                    } else if (count($m['submenu'])) {
                        foreach ($m['submenu'] as $m) {
                            if ($m['menu']->getController()) {
                                return $m['menu'];
                            } else {
                                foreach ($m['submenu'] as $m) {
                                    if ($m['menu']->getController()) {
                                        return $m['menu'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        private function listMenu($Root, $Type, $Status = 'all', $Nivel = 0) {
            $menus = [];

            $sql = $this->Lista("
                WHERE ((:status = 'all' AND a.status != 99) OR a.status = :status) 
                AND a.root = :root AND (:type = 0 OR a.permissao LIKE CONCAT('%[',:type,']%') OR :permissoes LIKE CONCAT('%[',a.id,']%')) 
                ORDER BY a.ordem", [
                'type' => (int) $Type,
                'root' => (int) $Root,
                'status' => $Status,
                'permissoes' => $this->User->getPermissoes(),
                    ], false);

            foreach ($sql as $menu) {
                $menus[] = [
                    'menu' => $menu,
                    'submenu' => $this->listMenu($menu->getId(), $Type, $Status, $Nivel + 1),
                    'nivel' => $Nivel + 1,
                ];
            }
            return $menus;
        }

        /**
         * Retorna o menu
         * @param int $Id
         * @param int $UserType
         * @return MenuVO
         */
        public function getPage($Id, $UserType = NULL) {
            $sql = $this->Lista("WHERE 
                a.id = :id AND 
                (
                    :type = 0 OR :type IS NULL 
                    OR a.permissao LIKE CONCAT('%[',:type,']%')
                    OR :permissoes LIKE CONCAT('%[',a.id,']%')
                )", [
                'type' => $this->User->getType(),
                'permissoes' => $this->User->getPermissoes(),
                'id' => (int) $Id,
            ]);
            return count($sql) ? $sql[0] : null;
        }

    }
    