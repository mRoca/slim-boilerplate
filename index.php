<?php

//session_start();

// Init Slim framework, other composer libs and PSR-0 autoloader
require_once './vendor/autoload.php';

//The config file
require_once './config.php';

//Add some helpers
require_once './app/lib/SlimBoilerplate/SlimHelpers.php';


// ########################################
// ############### Config #################

$app = new \Slim\Slim(array(
	'mode'           => $_SERVER['SERVER_NAME'] === 'server.production.com' ? 'production' : 'development',
	'log.path'       => './app/logs',
	'templates.path' => './app/templates',
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
	$app->view(new SlimBoilerplate\Layout\LayoutView());

	$app->notFound(function () use ($app) {
		include("404.html");
	});
}

// ########################################
// ###### DEFAULT LAYOUT CONFIG ###########

SlimBoilerplate\Layout\LayoutView::addCss('assets/css/vendor/normalize.min.css');
SlimBoilerplate\Layout\LayoutView::addCss('assets/css/style.css');
SlimBoilerplate\Layout\LayoutView::addJs('assets/js/vendor/modernizr-2.6.2-custom.min.js');
//SlimBoilerplate\Layout\LayoutView::setDefaultDescription('Slim PHP boilerplate');


// ########################################
// ############### ROUTES #################

// REST API
// Very simple Rest server for the "account" sql table
$restAccounts = new \SlimBoilerplate\Rest\RestServer('account');
$restAccounts->createRoutes($app);

// HTML

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
	$message = 'Un nouveau message a été posté sur le site ' . SITE_NAME . '.<br /><br />';

	$fields = array(
		'Nom'     => 'contact_name',
		'Société' => 'contact_company',
		'Email'   => 'contact_email',
		'Message' => 'contact_message',
	);

	foreach ($fields as $fullName => $name) {
		if (!trim(inputPost($name))) {
			$error .= "Le champ $fullName' est invalide.<br />";
		}

		if ($name === 'contact_email' && !valid_email(inputPost($name))) {
			$error .= "L'adresse mail est invalide.<br />";
		}

		if ($name !== 'contact_message')
			$message .= "$fullName : <strong>" . inputPost($name) . "</strong><br />";
		else {
			$message .= "<br />------------------ Message : --------------<br /><br />";
			$message .= nl2br(strip_tags(inputPost($name))) . "<br />";
		}
	}

	if (!$error) {
		try {
			$mailer = new SimpleMail();
			$send = $mailer->setTo(CONTACT_EMAIL, SITE_NAME)
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
	$page = preg_replace('/[^0-9a-zA-Z\-_+]/', '', $page);

	if (file_exists("./app/templates/front/$page.php")) {
		$app->render("front/$page.php");
	} else $app->notFound();
});

$app->get('/:folder/:page', function ($folder, $page) use ($app) {
	$page = preg_replace('/[^0-9a-zA-Z\-_+]/', '', $page);
	$folder = preg_replace('/[^0-9a-zA-Z\-_+]/', '', $folder);

	if (file_exists("./app/templates/front/$folder/$page.php")) {
		$app->render("front/$folder/$page.php");
	} else $app->notFound();
});

$app->run();
