<!DOCTYPE html>
<!--[if lte IE 8]> <html class="no-js lte-ie8"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<!--[if IE]><meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"><![endif]-->

	<!--
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="apple-mobile-web-app-status-bar-style" content="black" />

	<link rel="shortcut icon" href="<?= baseUrl() ?>favicon.ico" />
	<link rel="icon" type="image/x-icon" href="<?= baseUrl() ?>favicon.ico" />
	<link rel="icon" type="image/png" href="<?= baseUrl() ?>favicon.png" />
	-->

	<title><?= isset($title) && $title ? SITE_NAME . ' | ' . $title : (isset($defaultTitle) ? $defaultTitle : SITE_NAME) ?></title>

	<meta name="author" content="Michel Roca" />

	<meta name="description" content="<?= isset($description) && $description ? $description : (isset($defaultDescription) ? $defaultDescription : '') ?>" />
	<meta name="keywords" content="<?= isset($keywords) ? $keywords : '' ?>" />

	<link id="baseUrl" rel="canonical" href="<?= baseUrl() ?>" />

	<?php if(isset($css)): foreach($css as $file): ?>
		<link rel="stylesheet" href="<?= $file ?>" />
	<?php endforeach; endif; ?>

	<!--[if lt IE 9]>
	<script src="<?= baseUrl() ?>assets/js/vendor/html5shiv-printshiv-3.7.1.min.js"></script>
	<script src="<?= baseUrl() ?>assets/js/vendor/respond-1.4.2.min.js"></script>
	<![endif]-->
</head>
<body>
	<!--[if lt IE 8]>
		<p class="chromeframe">You are using an obsolete browser. You can  <a href="http://browsehappy.com/" target="_blank">update it</a> or <a href="http://www.google.com/chromeframe/?redirect=true" target="_blank">activate Google Chrome Frame</a>.</p>
	<![endif]-->

	<header>
		<h1><?= SITE_NAME ?></h1>
	</header>

	<section class="main-container">
		<div class="container">
            <p><a href="<?= url('/') ?>">Home</a></p>
			<?= $output ?>
		</div>
	</section>

	<?php if(isset($js)): foreach($js as $file): ?>
		<script src="<?= $file ?>" type="text/javascript"></script>
	<?php endforeach; endif; ?>
</body>
</html>
