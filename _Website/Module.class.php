<?php

    class Module {
	
    }

    if (!IS_AJAX) {

	Seo::setTitle('SERCONS :: Prestação de Serviços e Construção');

	Seo::addCss('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
	Seo::addCss('https://fonts.googleapis.com/css?family=Maven+Pro:400,700,500');
	Seo::addCss('https://fonts.googleapis.com/css?family=Lato:400,700');

	Seo::addCss(source('_cdn/bootstrap/css/bootstrap.min.css'));
	Seo::addCss(source('_cdn/bootstrap/css/bootstrap-theme.min.css'));
	Seo::addCss(source('css/owl.carousel.css'));
	Seo::addCss(source('css/owl.theme.css'));
	Seo::addCss(source('css/owl.transitions.css'));
	Seo::addCss(source('css/style.css'));

	Seo::addJs('https://maps.google.com/maps/api/js');
	Seo::addJs(source('_cdn/js/jquery.min.js'));
	Seo::addJs(source('_cdn/bootstrap/js/bootstrap.min.js'));
	Seo::addJs(source('_cdn/js/fastclick.js'));
	Seo::addJs(source('_cdn/js/modernizr.min.js'));
	Seo::addJs(source('_cdn/js/jquery.form.js'));
	Seo::addJs(source('_cdn/js/jquery.serializeObject.js'));
	Seo::addJs(source('_cdn/js/jquery.mask.js'));
	Seo::addJs(source('_cdn/js/mask.js'));
	Seo::addJs(source('_cdn/js/animaBanners.js'));
	Seo::addJs(source('_cdn/js/admin.js'));
	Seo::addJs(source('js/owl.carousel.min.js'));
	Seo::addJs(source('js/main.js'));
	Seo::addJs(source('_cdn/js/google.maps.js'));
    }

    function displayLayout($body = null, array $vars = null) {
	view_load('layout/site', extend([
	    'body' => $body,
	    'class' => 'no-home',
	    'site' => newModel('SiteConfig')->get(),
			], $vars));
    }
    