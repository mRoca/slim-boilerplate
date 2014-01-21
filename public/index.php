<?php

//session_start();

// Init Slim framework, other composer libs and PSR-0 autoloader
require_once '../vendor/autoload.php';

//The config file
require_once '../config.php';

//Add some helpers
require_once '../lib/Perso/SlimHelpers.php';


// ########################################
// ############### Config #################

$app = new \Slim\Slim(array(
	'mode'           => $_SERVER['SERVER_NAME'] === 'server.production.com' ? 'production' : 'development',
	'log.path'       => '../logs',
	'templates.path' => '../templates',
));

$app->configureMode('production', function () use ($app) {
	$app->config(array(
		'log.level'  => \Slim\Log::WARN,
		'log.enable' => true,
		'debug'      => false
	));
});

$app->configureMode('development', function () use ($app) {
	$app->config(array(
		'log.enable' => false,
		'debug'      => true
	));
});

//Use the layout class if not Ajax request
if (!$app->request()->isAjax()) {
	$app->view(new Perso\LayoutView());

	$app->notFound(function () use ($app) {
		include("404.html");
	});
}

// ########################################
// ###### DEFAULT LAYOUT CONFIG ###########

Perso\LayoutView::addCss('css/vendor/normalize.min.css');
Perso\LayoutView::addCss('css/style.css');
Perso\LayoutView::addJs('js/vendor/modernizr-2.6.2-custom.min.js');
//Perso\LayoutView::setDefaultDescription('Slim PHP boilerplate');


// ########################################
// ############### ROUTES #################

$app->get('/', function () use ($app) {
	$app->render('home.php');
})->name('home');

$app->get('/error', function () use ($app) {
	$app->render('error.php');
})->name('error');

//Default controller for front pages
$app->get('/:page', function ($page) use ($app) {
	if(file_exists("../templates/front/$page.php")){
		$app->render("front/$page.php");
	} else $app->notFound();
});

$app->run();
