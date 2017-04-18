<?php

    class userstypesController extends Controller {

	/** @var UserVO */
	private $User;

	public function __construct() {
	    $this->User = Module::getUserLoged();
	}

	function indexAction() {
	    if (!IS_AJAX and $this->User->getType() === 1) {

		echo form_open([
		    'id' => 'form-this',
		    'onSubmit' => 'return false;',
		    'data-adminpage' => [
			'controller' => 'userstypes',
		    ]
		]);

		echo form_hidden(['id' => null]);

		echo (new PanelBootstrap)
			->setBody((new Grid12('md'))
				->addColumn(formLabel('Título', formInput('title')))
				->addColumn(formLabel('Permissões', formSelect('permissoes', $this->getModel()->options($this->User->getType()), ['multiple'])))
			)
			->setFooter('<div class="btn-group" >'
				. formButton('<i class="fa fa-trash" ></i> Limpar', 'reset')
				. formButton('<i class="fa fa-save" ></i> Salvar')
				. '</div>', ['class' => 'text-right']);
		;

		displayLayout(ob_get_clean());
	    }
	}

	function insertAction() {
	    $this->Save();
	}

	function updateAction() {
	    $this->Save((int) inputPost('id'));
	}

	function excluirAction() {
	    try {
		if (!$v = $this->getModel()->getByLabel('id', inputPost('id'))) {
		    throw new Exception('Tipo inválido.');
		} else if ($v->getId() == 1) {
		    throw new Exception("`{$v->getTitle()}` não pode ser excluído.");
		}
		$this->getModel()->Excluir($v);
		exitJson('Excluído com sucesso.', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage());
	    }
	}

	function Save($id = null) {
	    try {

		if ($id !== null) {
		    if (!$v = $this->getModel()->getByLabel('id', $id)) {
			throw new Exception('Tipo inválido.');
		    }
		} else {
		    $v = $this->getModel()->newValueObject();
		}

		$v
			->input('title')
			->input('permissoes');

		$this->getModel()->Save($v);

		exitJson('Sucesso', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 0);
	    }
	}

	function listAction() {
	    /* @var $v UserTypeVO */
	    $t = (new Table(null, 'table table-bordered table-striped'))
		    ->addTSection('thead')
		    ->addRow()
		    ->addCell('Título')
		    ->addCell('Permissões')
		    ->addCell('Ações', ['width' => 120])
		    ->addTSection('tbody');

	    foreach ($this->getModel()->Lista('WHERE a.status != 99 AND (:type = 1 OR a.permissoes LIKE CONCAT("%[",:type,"]%"))', [
		'type' => $this->User->getType(),
	    ]) as $v) {
		$t->addRow()
			->addCell($v->gettitle())
			->addCell(call_user_func(function(array $Permissoes) {
				    $html = '';
				    foreach ($Permissoes as $permissao) {
					$html .= '<div>' . APP::getInstanceModel('UsersTypesModel')->getByLabel('id', $permissao)->getTitle() . '</div>';
				    }
				    return $html;
				}, $v->getPermissoes(true)))
			->addCell('<div class="btn-group btn-group-xs" >'
				. '<div class="btn btn-default" data-editar="' . $v . '" ><i class="fa fa-edit" ></i></div>'
				. '<div class="btn btn-danger" data-excluir="' . $v->getid() . '" ><i class="fa fa-remove" ></i></div>'
				. '</div>', 'text-center');
	    }

	    echo (new PanelBootstrap)->setBody($t);
	}

	/** @return UsersTypesModel */
	function getModel() {
	    return APP::getInstance('UsersTypesModel');
	}

    }
    