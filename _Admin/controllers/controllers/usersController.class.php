<?php

    class usersController extends Controller {

	public function indexAction() {
	    if (!IS_AJAX) {
		$user = APP::getModule()->getUserLoged();

		APP::getInstanceController('index')->Layout(form([
		    'data-adminpage' => [
			'controller' => str_replace('Controller', null, get_class($this)),
		    ]
				], (new PanelBootstrap)
					->setBody(formInput('id', null, 'hidden')
						. (new Grid12('md'))
						->addColumn(formLabel('Nome', formInput('nome', null, 'text', ['required' => true])), 4)
						->addColumn(formLabel('E-mail', formInput('email', null, 'email')), 8)
						->addColumn(formLabel('Login', formInput('login', null, 'text', ['pattern' => '.{5,20}', 'required' => true])), 4)
						->addColumn(formLabel('Senha', formInput('senha', null, 'password', ['placeholder' => 'Manter senha atual', 'pattern' => '[A-Za-z0-9]{5,20}', 'maxlength' => '20'])), 3)
						->addColumn(formLabel('Status', formSelect('status', [
						    1 => 'Ativo',
						    0 => 'Inativo',
						    2 => 'Bloqueado',
						    99 => 'Excluído',
							])), 2)
						->addColumn(formLabel('Tipo', formSelect('type', APP::getInstanceModel('UsersTypes')->options(Module::getUserLoged()->getType()))), 3))
					->setFooter((new Grid12)
						->addColumn(formButtonFile('<i class="fa fa-photo" ></i> Foto', 'upload_foto', 'image/*'), 4)
						->addColumn('<div class="btn-group" >'
							. formButton('<i class="fa fa-trash" ></i> Limpar', 'reset')
							. formButton('<i class="fa fa-save" ></i> Salvar')
							. '</div>', 'col-md-8 text-right'))
		));
	    }
	}

	function updatePositionAction() {
	    if ($user = Module::getUserLoged()) {
		$user->setPositionUpdate(__NOW__);
		$user->setLatitude(inputPost('latitude'));
		$user->setLongitude(inputPost('longitude'));
		$user->Save();
	    }
	}

	public function posicoesAction() {
	    displayLayout(view_load('posicoes', null, true));
	}

	public function getPositionsAction() {
	    $values = [];
	    $data = date('Y-m-d H:i:s', strtotime('-30 minutes'));
	    /* @var $v UserVO */
	    foreach (newModel('Users')->Lista('WHERE a.status = 1 AND a.positionupdate >= :date', ['date' => $data]) as $v) {
		$img = $v->imgCapa(false);
		$values[] = [
		    'foto' => $img ? $img->Redimensiona(60, 60) : null,
		    'id' => $v->getId(),
		    'latitude' => $v->getLatitude(),
		    'longitude' => $v->getLongitude(),
		    'nome' => $v->getNome(),
		    'html' => '<h4>' . $v->getNome() . '</h4><p>Atualizado às ' . Date::time($v->getPositionUpdate()) . '</p>',
		];
	    }
	    exit(json_encode($values));
	}

	public function save(UserVO $user) {
	    try {

		$user->setNome(inputPost('nome'));
		$user->setEmail(inputPost('email'));
		$user->setLogin(inputPost('login'));

		// Não pode alterar o próprio status
		if ($user->getId() != Module::getUserLoged()->getId()) {
		    $user->setStatus(inputPost('status'));
		    $user->setType(inputPost('type'));
		}

		// Criptografando a senha
		if (inputPost('senha') or ! $user->getId()) {
		    $user->setSenha(inputPost('senha'));
		}

		if (!$this->getModel()->getType($user->getType(), Module::getUserLoged()->getType())) {
		    exitJson('Você não tem permissão para gerenciar esse tipo de conta.');
		}

		$user->Save();

		$user->imgAddCapa('upload_foto');

		json('Registro atualizado com sucesso!', 1);
		
	    } catch (Exception $ex) {
		exitJson($ex->getMessage(), 0);
	    }
	}

	public function excluirAction() {

	    $user = $this->getModel()->getById(inputPost('id'));

	    if (!$user) {
		exitJson('Usuário inválido.');
	    } else if (Module::getUserLoged()->getId() == $user->getId()) {
		exitJson('Você não pode se excluir do sistema.');
	    } else if (!$this->getModel()->getType($user->getType(), Module::getUserLoged()->getType())) {
		exitJson('Você não tem permissão para excluir esse tipo de conta.');
	    }

	    try {
		$this->getModel()->Excluir(inputPost('id'));
		exitJson('Excluído com sucesso!', 1);
	    } catch (Exception $e) {
		exitJson($e->getMessage());
	    }
	}

	public function statusAction() {
	    $user = $this->getModel()->getById(inputPost('id'));
	    if ($user) {
		if ($user->getId() == Module::getUserLoged()->getId()) {
		    exitJson('Você não pode alterar seu próprio status.');
		} else {
		    $user->setStatus($user->getStatus() == 1 ? 0 : 1);
		    $this->getModel()->Save(['id' => $user->getId(), 'status' => $user->getStatus()]);
		    exitJson('Status atualizado!', 1);
		}
	    } else {
		exitJson('Registro inválido!');
	    }
	}

	function listAction() {

	    $t = new Table(null, 'table table-bordered table-striped');
	    $t->addTSection('thead');
	    $t->addRow();
	    $t->addCell('Foto', ['width' => '80']);
	    $t->addCell('Permissão', ['width' => '120']);
	    $t->addCell('Nome');
	    $t->addCell('Ações', ['width' => '120']);

	    $t->addTSection('tbody');
	    foreach (self::getModel()->getUsers(Module::getUserLoged()->getType()) as $v) {

		$t->addRow();
		$t->addCell('<img src="' . $v->imgCapa()->Redimensiona(30,30) . '" />', 'text-center');
		$t->addCell($v->getTypeTitle());
		$t->addCell($v->getNome());
		$t->addCell('<div class="btn-group btn-group-xs" >'
			. '<div class="btn btn-default" data-status="' . $v->getId() . '" ><i class="fa fa-eye' . ($v->getStatus() == 0 ? '-slash' : null) . '" ></i></div>'
			. '<div class="btn btn-default" data-editar="' . $v . '" ><i class="fa fa-edit" ></i></div>'
			. '<div class="btn btn-danger" data-excluir="' . $v->getId() . '" ><i class="fa fa-remove" ></i></div>'
			. '</div>', 'text-center');
	    }

	    echo (new PanelBootstrap)->setBody($t->display());
	}

	/** @return UsersModel */
	static function getModel() {
	    return APP::getInstance('UsersModel');
	}

    }
    