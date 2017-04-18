<?php

    class bannersController extends Controller {

	function indexAction(MenuVO $Pagina = null) {

	    $vars = stringToArray($Pagina->getVariaveis());

	    echo form_open([
		'id' => 'form-this',
		'data-adminpage' => [
		    'controller' => 'banners',
		    'saveValues' => ['ref' => $vars['ref']],
		    'searchValues' => ['ref' => $vars['ref']],
		],
	    ]);

	    echo formInput('id', null, 'hidden');

	    echo (new PanelBootstrap)
		    ->setHeader('<h3 class="panel-title" >' . $vars['title'] . '</h3>')
		    ->setBody((new Grid12('lg'))
			    ->addColumn(formLabel('Título', formInput('title')), 4)
			    ->addColumn(formLabel('Subtítulo', formInput('subtitle')), 8)
			    ->addColumn(formLabel('Legenda', formInput('legenda')), 12)
			    ->addColumn(formLabel('Data início', formInput('inicio', null, 'text', ['class' => 'mask-data'])), 2)
			    ->addColumn(formLabel('Data fim', formInput('fim', null, 'text', ['class' => 'mask-data'])), 2)
			    ->addColumn(formLabel('Link', formInput('link')), 5)
			    ->addColumn(formLabel('Target', formSelect('target', ['_self' => 'Mesma página', '_blank' => 'Nova página',])), 3)
			    ->addColumn(formBox('Dias de exibição', formInputCheckBox('dias[]', 'Todos os dias', '*')
					    . formInputCheckBox('dias[]', 'Domingo', '1')
					    . formInputCheckBox('dias[]', 'Segunda', '2')
					    . formInputCheckBox('dias[]', 'Terça', '3')
					    . formInputCheckBox('dias[]', 'Quarta', '4')
					    . formInputCheckBox('dias[]', 'Quinta', '5')
					    . formInputCheckBox('dias[]', 'Sexta', '6')
					    . formInputCheckBox('dias[]', 'Sábado', '7')
			    ))
		    )//Body
		    ->setFooter((new Grid12('md'))
			    ->addColumn(formButtonFile('<i class="fa fa-image" ></i> Imagem ' . @$vars['medida'], 'imagem', 'image/*'), 'col-xs-6 text-left')
			    ->addColumn('<div class="btn-group" >'
				    . formButton('Limpar', 'reset', 'btn-danger')
				    . formButton('Salvar')
				    . '</div>', 'col-xs-6 text-right')
		    )//footer
	    ;

	    displayLayout(ob_get_clean());
	}

	function insertAction() {
	    $this->Save();
	}

	function updateAction() {
	    $this->Save((int) inputPost('id'));
	}

	function excluirAction() {
	    try {
		$this->getModel()->Excluir($this->getModel()->getByLabel('id', inputPost('id')));
		exitJson('Excluído!', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage());
	    }
	}

	function Save($id = null) {
	    try {
		if ($id !== null) {
		    if (!$v = $this->getModel()->getByLabel('id', $id)) {
			throw new Exception('Banner inválido.');
		    }
		} else {
		    $v = $this->getModel()->newValueObject();
		}

		$v
			->input('dias')
			->input('ref')
			->input('destaque')
			->input('title')
			->input('subtitle')
			->input('legenda')
			->input('inicio')
			->input('fim')
			->input('link')
			->input('target');

		# Salvando
		$this->getModel()->Save($v);

		# Enviando imagem
		$v->imgAddCapa('imagem');

		exitJson('Sucesso', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 0);
	    }
	}

	function statusAction() {
	    if ($v = $this->getModel()->getByLabel('id', inputPost('id'))) {
		$v->setStatus($v->getStatus() == 1 ? 0 : 1);
		$v->Save();
	    }
	    json('Status alterado', 1);
	}

	function listAction() {
	    $busca = $this->getModel()->Busca(inputPost(), inputPost('page'), 10, 'a.inicio ASC');
	    if ($busca->getCount()) {

		$t = (new Table(null, 'table table-bordered table-striped'));
		$t->addTSection('thead')
			->addRow()
			->addCell('IMG', ['width' => 50])
			->addCell('Início', ['width' => 120])
			->addCell('Fim', ['width' => 120])
			->addCell('Título')
			->addCell('Ações', ['width' => 120])
			->addTSection('tbody');

		/* @var $v BannerVO */
		foreach ($busca->getRegistros() as $v) {
		    $t->addRow()
			    ->addCell('<img src="' . $v->imgCapa()->Redimensiona(30, 30) . '" />', 'text-center')
			    ->addCell($v->getInicio(true), 'text-center')
			    ->addCell($v->getFim(true), 'text-center')
			    ->addCell($v->getTitle())
			    ->addCell('<div class="btn-group btn-group-xs" >'
				    . '<div class="btn btn-default" data-status="' . $v->getId() . '" ><i class="fa fa-eye' . ($v->getstatus() != 1 ? '-slash' : null) . '" ></i></div>'
				    . '<div class="btn btn-default" data-editar="' . $v . '" ><i class="fa fa-edit" ></i></div>'
				    . '<div class="btn btn-danger" data-excluir="' . $v->getId() . '" ><i class="fa fa-remove" ></i></div>'
				    . '</div>', 'text-center');
		}

		echo (new PanelBootstrap)
			->setHeader('<h3 class="panel-title" >Lista</h3>')
			->setBody($t->display())
			->setFooter($busca->display(), ['class' => 'text-right'])
		;
	    }
	}

	/** @return BannersModel */
	function getModel() {
	    return APP::getInstanceModel('Banners');
	}

    }
    