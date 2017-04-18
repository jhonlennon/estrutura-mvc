<?php
    
    Session::start(APP::getCurrentModule());

    class Module {

	private static $UserOnline;

	public function __construct() {
	    if (!self::getUserLoged()) {

		# Logando usuário
		if (inputPost('logIn')) {
		    try {
			self::logInUser(inputPost('login'), inputPost('senha'));
			exitJson('Logado com sucesso.', 1);
		    } catch (Exception $ex) {
			exitJson($ex->getMessage());
		    }
		} else if (IS_AJAX) {
		    json('Você não está logado aperte F5 para acessar o sistema novamente.');
		}

		# Verifica se foi feito o LogIn
		if (!self::getUserLoged()) {
		    view_load('layout/login');
		    exit;
		}
	    }
	}

	/**
	 * Desloga o usuário do sistema
	 */
	public function logOut() {
	    APP::getInstance('UsersModel')->LogOut(APP::getCurrentModule());
	}

	/**
	 * Loga o usuário na sessão
	 * @param string $login
	 * @param string $senha
	 * @return boolean
	 * @throws Exception
	 */
	public static function logInUser($login, $senha) {
	    try {
		APP::getInstance('UsersModel')->LogIn($login, $senha, APP::getCurrentModule());
		return true;
	    } catch (Exception $ex) {
		throw new Exception($ex->getMessage());
	    }
	}

	/**
	 * Retorna o usuário logado
	 * @return UserVO
	 */
	public static function getUserLoged() {
	    if (self::$UserOnline or self::$UserOnline = APP::getInstance('UsersModel')->getLogedUser(APP::getCurrentModule())) {
		return self::$UserOnline;
	    } else {
		return null;
	    }
	}

    }

    if (!IS_AJAX) {

	Seo::setTitle('Administrador');

	Seo::addJs(source('_dashboard/plugins/jQuery/jQuery-2.1.4.min.js'));
	Seo::addJs(source('_cdn/bootstrap/js/bootstrap.min.js'));
	Seo::addJs(source('_cdn/js/fastclick.js'));
	Seo::addJs(source('_cdn/js/modernizr.min.js'));
	Seo::addJs(source('_cdn/js/jquery.form.js'));
	Seo::addJs(source('_cdn/js/jquery.serializeObject.js'));
	Seo::addJs(source('_cksource/ckeditor/ckeditor.js'));
	Seo::addJs(source('_cdn/js/mask.js'));
	Seo::addJs(source('_cdn/js/jquery.mask.js'));
	Seo::addJs('https://maps.google.com/maps/api/js?sensor=true');
	Seo::addJs(source('_cdn/js/gmaps.js'));
	Seo::addJs(source('_cdn/js/admin.js'));
	Seo::addJs(source('_dashboard/dist/js/app.js'));

	Seo::addCss(source('_cdn/bootstrap/css/bootstrap.min.css'));
	Seo::addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
	Seo::addCss('https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css');
	Seo::addCss(source('_dashboard/dist/css/AdminLTE.css'));
	Seo::addCss(source('_dashboard/dist/css/skins/_all-skins.min.css'));
    }

    function displayLayout($Body = null, array $Variables = null, $Search = false) {
	view_load('layout/default', array_merge((array) $Variables, [
	    'Body' => (string) $Body,
	    'Search' => $Search,
	    'Menu' => APP::getInstanceModel('Menu')->getMenu(APP::getModule()->getUserLoged()->getType()),
		]), false);
    }
    