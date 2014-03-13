<?php

/**
 * @return string
 */
function baseUrl()
{
	return \Slim\Slim::getInstance()->request()->getUrl() . baseUri() . '/';
}

/**
 * @param $path
 * @return string
 */
function url($path)
{
	if (substr($path, 0, 1) === '/') $path = substr($path, 1);

	return baseUrl() . $path;
}

/**
 * @return mixed
 */
function baseUri()
{
	return \Slim\Slim::getInstance()->request()->getRootUri();
}

/**
 * @param $path
 * @return string
 */
function uri($path)
{
	return baseUri() . '/' . $path;
}

/**
 * @return mixed
 */
function slimMode()
{
	return \Slim\Slim::getInstance()->config('mode');
}

/**
 * Do an UTF-8 htmlspecialchars
 * @param $text
 * @return string
 */
function secur($text)
{
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

/**
 * Do an UTF-8 htmlspecialchars
 *
 * @param $text
 * @return string
 */
function e($text)
{
	return secur($text);
}

/**
 * @param $name
 * @return bool
 */
function inputGet($name)
{
	if (!isset($_GET[$name])) return false;

	return $_GET[$name];
}

/**
 * @param $name
 * @return bool
 */
function inputPost($name)
{
	if (!isset($_POST[$name])) return false;

	return $_POST[$name];
}

/**
 * @param $name
 * @return string
 */
function formValue($name)
{
	if (!isset($_POST[$name])) return '';

	return secur($_POST[$name]);
}

/**
 * @param $bytes
 * @param int $decimals
 * @return string
 */
function human_filesize($bytes, $decimals = 2)
{
	$sz     = array('Octets', 'Ko', 'Mo', 'Go', 'To', 'Po');
	$factor = floor((strlen($bytes) - 1) / 3);

	return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$sz[$factor];
}

/**
 * @param $address
 * @return bool
 */
function valid_email($address)
{
	return (bool) preg_match("/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix", $address);
}
