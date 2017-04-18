<?php

    class imagensController extends Controller {

	/**
	 * Abre a página de imagens
	 */
	function indexAction() {
	    if (!IS_AJAX) {
		APP::getInstanceController('index')->Layout('<div class="module" >'
			. '<header>'
			. '<h3>' . (inputGet('title') ? inputGet('title') : 'Imagens') . '</h3>'
			. '</header>'
			. '<div class="module-content" style="margin: 0 0 -5px 0; padding: 10px;" >'
			. '<iframe id="frame-imagens" src="' . url('imagens/iframe', null, null, inputGet()) . '" class="iframe-autosize" style="height: 600px; padding: 0; margin: 0; box-sizing: content-box; border: none; width: 100%;" >'
			. '</iframe>'
			. '</div>'
			. '<footer>'
			. formButton('Nova imagem', 'button', 'btn-primary', null, null, ['onClick' => '_log($("#frame-imagens").contents()[0]);'])
			. '</footer>'
			. '</div>');
	    }
	}

	function iframeAction() {
	    
	    Seo::reset();
	    
	    Seo::setTitle('Galeria de imagens');
	    
	    Seo::addCss(source('_cdn/bootstrap/css/bootstrap.min.css'));
	    Seo::addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
	    
	    Seo::addJs(source('_cdn/js/jquery.min.js'));
	    Seo::addJs(source('_cdn/js/jquery.serializeObject.js'));
	    Seo::addJs(source('_cdn/js/jquery.form.js'));
	    Seo::addJs(source('_cdn/jquery.ui/jquery-ui.min.js'));
	    Seo::addJs(source('_cdn/bootstrap/js/bootstrap.min.js'));
	    Seo::addJs(source('_cdn/js/modernizr.min.js'));
	    Seo::addJs(source('_cdn/js/fastclick.js'));
	    Seo::addJs(source('_cdn/js/admin.js'));
	    
	    view_load('iframe_imagens');
	    
	}

	/**
	 * Lista as imagens
	 */
	function listAction() {

	    /* @var $img ImageVO */
	    $imagens = $this->getModel()->Lista('WHERE a.ref = :ref AND a.refid = :refid AND a.status != 99 ORDER BY a.position ASC', ['ref' => inputPost('ref'), 'refid' => inputPost('refid')]);
	    foreach ($imagens as $img) {
		echo '<li class="imagem ' . ($img->getStatus() == 1 ? '' : 'inativo') . '" data-id="' . $img->getId() . '" >'
		. '<div class="content" >'
		. '<div class="box-image" data-url="' . $img->getSource(true) . '" style="background-image: url(\'' . $img->Redimensiona(250, 250, 'proporcional') . '\');" ></div>'
		. '<div class="btn-group" >'
		. '<div class="btn btn-default btn-xs" data-action="status" ><i class="fa fa-eye' . ($img->getStatus() != 1 ? '-slash' : null) . '" ></i></div>'
		. '<div class="btn btn-primary btn-xs" data-action="editar" data-values="' . $img . '" ><i class="fa fa-edit" ></i></div>'
		. '<div class="btn btn-danger btn-xs" data-action="excluir" ><i class="fa fa-remove" ></i></div>'
		. '</div>'
		. '</div>'
		. '</li>';
	    }
	    exit;
	}

	function insertAction() {
	    $this->Save();
	}

	function updateAction() {
	    $this->Save((int) inputPost('id'));
	}

	/**
	 * Salva uma imagem
	 */
	function Save($id = null) {
	    try {
		if ($id > 0) {
		    $img = $this->getModel()->Lista('WHERE a.id = :id AND a.ref = :ref AND a.refid = :refid LIMIT 1', [
			'id' => $id,
			'ref' => inputPost('ref'),
			'refid' => inputPost('refid')
		    ]);
		    if (!count($img)) {
			throw new Exception('Imagem inválida.');
		    } else {
			$img = $img[0];
		    }
		} else {
		    $img = $this->getModel()->newValueObject();
		    $img->input('ref')
			    ->input('refid');
		}
		$img
			->input('title')
			->input('legenda');

		$this->getModel()->salvaImage($img, 'imagem');

		exitJson('Imagem salva com sucesso.', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 0);
	    }
	}

	/**
	 * Alterna o status entre ativo/inativo
	 */
	function statusAction() {
	    try {
		$img = $this->getModel()->Lista('WHERE a.id = :id AND a.ref = :ref AND a.refid = :refid LIMIT 1', [
		    'id' => inputPost('id'),
		    'ref' => inputPost('ref'),
		    'refid' => inputPost('refid')
		]);
		if (count($img)) {
		    $img = $img[0];
		    $img->setStatus(inputPost('status') == 0 ? 0 : 1);
		    $this->getModel()->Save($img);
		} else {
		    throw new Exception('Imagem inválida.');
		}
	    } catch (Exception $ex) {
		exitJson($ex->getMessage());
	    }
	}

	/**
	 * Ordena as imagens na base de dados
	 */
	function ordenarAction() {
	    $imagens = inputPost()['imagens'];
	    if (!empty($imagens) and is_array($imagens)) {
		foreach ($imagens as $pos => $id) {
		    $img = $this->getModel()->Lista('WHERE a.id = :id AND a.ref = :ref AND a.refid = :refid LIMIT 1', ['id' => $id, 'ref' => inputPost('ref'), 'refid' => inputPost('refid')]);
		    if (count($img)) {
			$img[0]->setPosition($pos + 1);
			if ($img[0]->getStatus() == 99) {
			    $img[0]->setStatus(0);
			}
			$this->getModel()->Save($img[0]);
		    }
		}
	    }
	}

	/**
	 * Excluí uma imagem da galeria
	 */
	function excluirAction() {
	    try {
		if (!$img = $this->getModel()->getByLabel('id', inputPost('id'))) {
		    throw new Exception('Imagem inválida.');
		}
		$this->getModel()->excluirImage($img);
		exitJson('Imagem excluída com sucesso.', 1);
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 0);
	    }
	}

	/** @return ImagensModel */
	function getModel() {
	    return APP::getInstanceModel('ImagensModel');
	}

    }
    