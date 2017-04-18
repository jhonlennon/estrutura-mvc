<?php

    class servicosController extends Controller {

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
			    ->addColumn(formLabel('Serviço', formInput('title', null, 'text', ['required'])), 12)
			    ->addColumn(formLabel('Texto', formTextarea('texto', null, ['required', 'data-ckeditor'])), 12)
		    )
		    ->setFooter((new Grid12('md'))
			    ->addColumn(formButtonFile('<i class="fa fa-image" ></i> Foto (300x250)', 'foto', 'image/*'), 5)
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

	function Save(ServicoVO $v) {
	    try {
		$v->Save(inputPost());
		$v->imgAddCapa('foto');
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
			->addCell('Foto', ['width' => 80])
			->addCell('Serviço')
			->addCell('Ações', ['width' => 90])
			->addTSection('tbody')
		;

		foreach ($busca->getRegistros() as $v) {
		    $t->addRow()
			    ->addCell('<img src="' . $v->imgCapa()->Redimensiona(30, 30) . '" />', 'text-center')
			    ->addCell($v->getTitle())
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

	/** @return ServicosModel */
	function getModel() {
	    return newModel('Servicos');
	}

    }
    