<?php

    class parceirosController extends Controller {

	function indexAction() {

	    $values = [
		'ref' => inputGet('ref'),
		'refid' => inputGet('refid'),
	    ];

	    echo form_open([
		'id' => 'form-this',
		'data-adminpage' => [
		    'saveValues' => $values,
		    'searchValues' => $values,
		],
	    ]);

	    echo form_hidden(['id' => null]);

	    echo (new PanelBootstrap)
		    ->setBody((new Grid12('md'))
			    ->addColumn(formLabel('Nome', formInput('nome')))
			    ->addColumn(formLabel('Site', formInput('link')))
		    )
		    ->setFooter((new Grid12('md'))
			    ->addColumn(formButtonFile('<i class="fa fa-image" ></i> Logo Marca (220x100)', 'logo', 'image/png'), 5)
			    ->addColumn('<div class="btn-group" >'
				    . formButton('<i class="fa fa-trash" ></i> Limpar', 'reset')
				    . formButton('<i class="fa fa-save" ></i> Salvar')
				    . '</div>', 'col-md-7 text-right')
		    )
	    ;

	    echo form_close();

	    displayLayout(ob_get_clean());
	}

	function excluirAction() {
	    if ($v = $this->getModel()->getByLabel('id', inputPost('id'))) {
		$v->Save([
		    'status' => 99,
		]);
	    }
	    json('Excluído', 1);
	}

	function Save(ParceiroVO $v) {
	    try {

		$v->__setValues(inputPost());

		$v->Save();
		
		$v->imgAddCapa('logo');

		exitJson('Sucesso.', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 0);
	    }
	}

	function statusAction() {
	    if ($v = $this->getModel()->getByLabel('id', inputPost('id'))) {
		$v->Save(['status' => $v->getStatus() ? 0 : 1]);
	    }
	    json('Status alterado', 1);
	}

	function listAction() {

	    $busca = $this->getModel()->Busca(inputPost(), inputPost('page'));

	    if ($busca->getCount()) {

		$t = (new Table('', 'table table-bordered table-striped table-hover'))
			->addTSection('thead')
			->addRow()
			->addCell('Logo', ['width' => 150])
			->addCell('Nome')
			->addCell('Ações', ['width' => 90])
			->addTSection('tbody');

		/* @var $v ParceiroVO */
		foreach ($busca->getRegistros() as $v) {
		    $t->addRow()
			    ->addCell('<img src="'.$v->imgCapa()->Redimensiona(30, 30).'" />', 'text-center')
			    ->addCell($v->getNome())
			    ->addCell('<div class="btn-group btn-group-xs" >'
				    . '<div class="btn btn-default" data-status="' . $v->getId() . '" ><i class="fa fa-eye' . ($v->getStatus() == 1 ? null : '-slash') . '" ></i></div>'
				    . '<div class="btn btn-default" data-editar="' . $v . '" ><i class="fa fa-edit" ></i></div>'
				    . '<div class="btn btn-danger" data-excluir="' . $v->getId() . '" ><i class="fa fa-remove" ></i></div>'
				    . '</div>', 'text-center');
		}

		echo (new PanelBootstrap)
			->setBody($t->display())
			->setFooter($busca->display(), ['class' => 'text-right'])
		;
	    }
	}

	/** @return ParceirosModel */
	function getModel() {
	    return newModel('ParceirosModel');
	}

    }
    