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

$app->get('/contact', function () use ($app) {
	$data = inputGet('success') ? array('success' => inputGet('success')) : array();
	$app->render('contact.php', $data);
});

$app->post('/contact', function () use ($app) {

	$error = '';
	$message = 'Un nouveau message a été posté sur le site '. SITE_NAME .'.<br /><br />';

	if(! trim(inputPost('contact_name'))){
		$error .= "Le champ 'Nom' est invalide.<br />";
	}

	$message .= "Nom de l'expéditeur : " . inputPost('contact_name') ."<br />";

	if(! trim(inputPost('contact_company'))){
		$error .= "Le champ 'Société' est invalide.<br />";
	}

	$message .= "Société : " . inputPost('contact_company') ."<br />";

	if(! inputPost('contact_email') || ! valid_email(inputPost('contact_email'))){
		$error .= "L'adresse mail est invalide.<br />";
	}

	$message .= "Email : " . inputPost('contact_email') ."<br />";

	if(! trim(inputPost('contact_message'))){
		$error .= "Le champ 'Message' est invalide.<br />";
	}

	$message .= "<br />------------------ Message : --------------<br /><br />";
	$message .= nl2br(inputPost('contact_message')) ."<br />";

	if (!$error) {
		try {
			$mailer = new SimpleMail();
			$send   = $mailer->setTo(CONTACT_EMAIL, SITE_NAME)
				->setSubject("[" . SITE_NAME . "] Nouveau message de contact")
				->setFrom(CONTACT_EMAIL, SITE_NAME)
				->addMailHeader('Reply-To', inputPost('contact_email'), inputPost('contact_name'))
				->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
				->addGenericHeader('Content-Type', 'text/html; charset="utf-8"')
				->setMessage($message)
				->setWrap(100)
				->send();

			if (!$send) {
				$error = 'Erreur lors de l\'envoi du mail';
			}

		} catch (Exception $e) {
			$error = $e->getMessage();
		}
	}

	if ($error)
		$app->render('contact.php', array('error' => $error));
	else
		$app->redirect(uri('contact?success=1'));
});

//Default controller for front pages
$app->get('/:page', function ($page) use ($app) {
	if(file_exists("../templates/front/$page.php")){
		$app->render("front/$page.php");
	} else $app->notFound();
});

$app->get('/:folder/:page', function ($folder, $page) use ($app) {
	$folder = str_replace('.', '', $folder);

	if (file_exists("../templates/front/$folder/$page.php")) {
		$app->render("front/$folder/$page.php");
	} else $app->notFound();
});

$app->run();
