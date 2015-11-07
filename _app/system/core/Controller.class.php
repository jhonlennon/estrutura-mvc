<?php

    abstract class Controller {

        public function notFoundPage() {
            try {
                if (IS_AJAX) {
                    exitJson([
                        'message' => 'Ação inválida.',
                        'html' => 'Página não encontrada.',
                    ], 0);
                } else {
                    view_load('layout/404');
                }
            } catch (Exception $ex) {
                echo 'Página inexistente!';
            }
        }

        /**
         * Insere um novo Registro
         */
        function insertAction() {
            try {
                if (!method_exists($this, 'Save')) {
                    throw new Exception('Método Save não foi criado.');
                } else {
                    $this->Save($this->voById());
                }
            } catch (Exception $ex) {
                exitJson($ex->getMessage(), 0);
            }
        }

        /**
         * Atualiza os dados do Registro
         * Envia para a função Save o id ($_POST['id'])
         */
        function updateAction() {
            try {
                if (!method_exists($this, 'Save')) {
                    throw new Exception('Método Save não foi criado.');
                } else {
                    $this->Save($this->voById((int) inputPost('id')));
                }
            } catch (Exception $ex) {
                exitJson($ex->getMessage(), 0);
            }
        }

        /**
         * Retorna a ValueObject
         * @param int $id
         * @return ValueObject
         * @throws Exception
         */
        function voById($id = null) {
            try {
                if (!method_exists($this, 'getModel')) {
                    throw new Exception('O método getModel não foi criado.');
                } else if (!is_a($this->getModel(), 'Model')) {
                    throw new Exception('O método getModel deve retorna uma Model.');
                } else if ($id !== null) {
                    if (!$v = $this->getModel()->getByLabel('id', $id)) {
                        throw new Exception('Registro inválido.');
                    }
                } else {
                    $v = $this->getModel()->newValueObject();
                }
                return $v;
            } catch (Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }

    }
    