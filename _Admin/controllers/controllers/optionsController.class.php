<?php

    class optionsController extends Controller {

        function indexAction(MenuVO $Pagina = null) {
            if ($Pagina) {
                
                parse_str($Pagina->getVariaveis(), $values);
                
                ob_start();
                
                echo form([
                    'id' => 'form-this',
                    'data-adminpage' => [
                        'saveValues' => ['ref' => $values['ref']],
                        'searchValues' => ['label' => !empty($values['label']) ? $values['label'] : 'Título', 'ref' => $values['ref']],
                        'controller' => 'options',
                    ]
                                ], LayoutHelper::Module(inputGet('title'), formInput('id', null, 'hidden')
                                        . (new Grid12('md'))
                                                ->addColumn(formLabel(inputGet('label') ? inputGet('label') : 'Título', formInput('title'))), '<div class="btn-group" >'
                                        . formButton('Limpar', 'reset')
                                        . formButton('Salvar')
                                        . '</div>')
                );
                
                APP::getInstance('indexController')->Layout(ob_get_clean());
            }
        }

        /**
         * Lista os registros
         */
        function listAction() {

            $registros = $this->getModel()->listByRef(inputPost('ref'));
            if (count($registros)) {
                
                echo LayoutHelper::Module('Lista', call_user_func(function($registros) {
                    /* @var $v OptionVO */

                    # Table
                    $t = new Table(null, 'table table-bordered table-striped sortable');

                    # Header
                    $t->addTSection('thead');
                    $t->addRow();
                    $t->addCell(inputPost('label'));
                    $t->addCell('Ações', ['width' => '100']);

                    # Registros
                    $t->addTSection('tbody');
                    foreach ($registros as $v) {
                        $t->addRow('', ['data-id' => $v->getId(), 'data-ordem' => $v->getOrdem()]);
                        $t->addCell($v->getTitle());
                        $t->addCell('
                    <i class="act fa fa-edit" data-editar="' . $v->toHtml(true) . '" ></i>
                    <i class="act fa fa-remove" data-excluir="' . $v->getId() . '" ></i>
                    ', 'text-center');
                    }

                    # Display
                    return $t->display();
                }, $registros));
            }
        }

        function Save(OptionVO $v) {
            try {

                if (!$v->getId()) {
                    $v = $this->getModel()->newValueObject();
                    $v->setRef(inputPost('ref'));
                    $v->setOrdem($this->getModel()->getLastOrdem($v->getRef()));
                }

                $v
                        ->input('title')
                        ->Save();

                exitJson('Sucesso!', 1);
            } catch (Exception $ex) {
                exitJson($ex->getMessage());
            }
        }

        function excluirAction() {
            $this->getModel()->Save(['id' => inputPost('id'), 'status' => 99]);
            exitJson('Excluído com sucesso.', 1);
        }

        function sortableAction() {
            $ids = stringToArray(inputPost('id'));
            $ordens = stringToArray(inputPost('ordem'));
            if (!empty($ids) and count($ids) == count($ordens)) {
                foreach ($ids as $key => $id) {
                    if (isset($ordens[$key])) {
                        $this->getModel()->Save(['id' => $id, 'ordem' => (int) $ordens[$key]]);
                    }
                }
            }
        }

        /** @return OptionsModel */
        function getModel() {
            return APP::getInstance('OptionsModel');
        }

    }
    