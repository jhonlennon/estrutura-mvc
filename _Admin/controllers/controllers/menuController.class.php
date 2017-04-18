<?php

    class menuController extends Controller {

	private $User;

	public function __construct() {
	    $this->User = Module::getUserLoged();
	    if ($this->User->getType() != 1) {
		header('Location: ' . url('index'));
		exit;
	    }
	}

	public function indexAction(MenuVO $Pagina) {

	    echo form_open([
		'id' => 'form-menu',
		'data-adminpage' => [
		    'controller' => 'menu',
		],
	    ]);

	    echo formInput('id', null, 'hidden');

	    echo (new PanelBootstrap)
		    ->setBody((new Grid12('md'))
			    ->addColumn(formLabel('Posição', formInput('ordem', null, 'number', ['value' => 1, 'min' => 1, 'max' => 255, 'required' => true])), 2)
			    ->addColumn(formLabel('Root', formSelect('root')), 4)
			    ->addColumn(formLabel('Controller', formInput('controller')), 6)
			    ->addColumn(formLabel('Principal', formInput('principal', null, 'number', ['min' => 0, 'max' => 255, 'placeholder' => 'Não'])), 2)
			    ->addColumn(formLabel('Icone', formInput('icone')), 3)
			    ->addColumn(formLabel('Título', formInput('title', null, 'text', ['required' => true])), 3)
			    ->addColumn(formLabel('Descrição', formInput('legenda')), 4)
			    ->addColumn(formLabel('OnClick', formInput('onclick')), 4)
			    ->addColumn(formLabel('Variaveis', formInput('variaveis')), 6)
			    ->addColumn(formLabel('Status', formSelect('status', [1 => 'Ativo', 2 => 'Invisível', 0 => 'Inativo', 99 => 'Excluído'])), 2)
			    ->addColumn(formLabel('Permissões', formSelect('permissao', APP::getInstanceModel('UsersTypes')->options(Module::getUserLoged()->getType()), ['multiple' => true, 'required' => true])), 12))
		    ->setFooter((new Grid12)
			    ->addColumn('<div class="btn-group" >'
				    . formButton('<i class="fa fa-trash" ></i> Limpar', 'reset')
				    . formButton('<i class="fa fa-save" ></i> Salvar')), ['class' => 'text-right'])
	    ;

	    echo form_close();

	    displayLayout(ob_get_clean());
	}

	private function getFiles($dir = null) {
	    $path = __DIR__ . "/../views/";
	    $files = '';
	    foreach (glob("{$path}{$dir}*", GLOB_BRACE) as $file) {
		if (is_dir($file) and empty($dir)) {
		    $label = str_replace($path, null, $file);
		    $files .= '<optgroup label="' . ucfirst($label) . '" >' . $this->getFiles($label . '/') . '</optgroup>';
		} else if (preg_match('/\.phtml$/', $file)) {
		    $file = basename($file, '.phtml');
		    $files .= formOption($file, $dir . $file);
		}
	    }
	    return $files;
	}

	private function getRoots($Menu) {
	    $html = '';
	    if (!empty($Menu)) {
		foreach ($Menu as $m) {
		    /* @var $menu MenuVO */
		    $menu = $m['menu'];
		    $html .= formOption(trim(str_pad('', (int) $m['nivel'] - 1, '-', STR_PAD_LEFT) . ' ' . $menu->getTitle()), $menu->getId());
		    $html .= $this->getRoots($m['submenu']);
		}
	    }
	    return $html;
	}

	public function saveData($id = null) {

	    try {

		if ($id !== null) {
		    if (!$v = $this->getModel()->getByLabel('id', inputPost('id'))) {
			throw new Exception('Menu inválido.');
		    }
		} else {
		    $v = $this->getModel()->newValueObject();
		}

		$v
			->input('ordem')
			->input('root')
			->input('arquivo')
			->input('controller')
			->input('principal')
			->input('icone')
			->input('title')
			->input('legenda')
			->input('onclick')
			->input('variaveis')
			->input('status')
			->input('permissao');

		$this->getModel()->Save($v);

		exitJson('Registro atualizado com sucesso!', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 0);
	    }
	}

	public function updateAction() {
	    $this->saveData((int) inputPost('id'));
	}

	public function insertAction() {
	    $this->saveData();
	}

	function excluirAction() {
	    try {
		if (!$v = $this->getModel()->getByLabel('id', inputPost('id'))) {
		    throw new Exception('Menu inválido.');
		} else if ($this->getModel()->getByLabel('root', $v->getId())) {
		    throw new Exception('É necessário que exclua os sub-menus.');
		}
		$this->getModel()->Excluir($v);
		exitJson('Excluído', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 0);
	    }
	}

	public function statusAction() {
	    $v = $this->getModel()->getByLabel('id', inputPost('id'));
	    if ($v) {
		$this->getModel()->Save(['status' => $v->getStatus() ? 0 : 1, 'id' => inputPost('id')]);
	    }
	    exitJson('OK', 1);
	}

	public function listAction() {

	    $t = (new Table(null, 'table table-bordered table-striped'))
		    ->addTSection('thead')
		    ->addRow()
		    ->addCell('#', ['width' => 50])
		    ->addCell('Posição', ['width' => 50])
		    ->addCell('Título')
		    ->addCell('Permissões')
		    ->addCell('Ações', ['width' => 120])
		    ->addTSection('tbody');

	    $this->trMenu($t, $this->getModel()->getMenu(0, 'all'));

	    echo (new PanelBootstrap)
		    ->setBody($t->display() . "<script>"
			    . "$('#form-menu').find('[name=root]').html(\"" . addslashes(formOption('-- Selecione --', 0) . $this->getRoots(APP::getInstance('MenuModel')->getMenu())) . "\");"
			    . "$('#form-menu').find('[name=arquivo]').html(\"" . addslashes(formOption('-- Selecione --', '') . $this->getFiles()) . "\");"
			    . "</script>");
	}

	public function trMenu(Table &$t, $Menu) {
	    foreach ($Menu as $key => $menu) {
		$submenu = $menu['submenu'];
		/* @var $menu MenuVO */
		$menu = $menu['menu'];

		$t->addRow('nivel-' . $Menu[$key]['nivel'])
			->addCell($menu->getId(), 'text-center')
			->addCell($menu->getOrdem(), 'text-center')
			->addCell(($menu->getIcone() ? "<i class=\"{$menu->getIcone()}\" ></i> " : null ) . $menu->getTitle())
			->addCell(call_user_func(function($Permissoes) {
				    $html = '';
				    foreach ($Permissoes as $permissao) {
					$html .= '<div style="font-weight: ' . (Module::getUserLoged()->getType() == $permissao ? 'bold' : 'normal') . ';" >'
						. APP::getInstanceModel('UsersTypes')->getByLabel('id', $permissao)->getTitle()
						. '</div>';
				    }
				    return $html;
				}, $menu->getPermissao(true)))
			->addCell('<div class="btn-group btn-group-xs" >'
				. '<div class="btn btn-default" data-status="' . $menu->getId() . '" ><i class="fa fa-eye' . ($menu->getStatus() == 1 ? '' : '-slash') . '" ></i></div>'
				. '<div class="btn btn-default" data-editar="' . $menu->toHtml() . '" ><i class="fa fa-edit" ></i></div>'
				. '<div class="btn btn-danger" data-excluir="' . $menu->getId() . '" ><i class="fa fa-remove" ></i></div>'
				. '</div>'
				, 'text-center');
		if (!empty($submenu)) {
		    $this->trMenu($t, $submenu);
		}
	    }
	}

	/** @return MenuModel */
	static function getModel() {
	    return APP::getInstance('MenuModel');
	}

    }
    