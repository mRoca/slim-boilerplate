<?php

namespace Perso;

class LayoutView extends \Slim\View
{
	public static $template_vars = array(
		'title'              => '',
		'defaultTitle'       => SITE_NAME,
		'description'        => '',
		'defaultDescription' => '',
		'keywords'           => '',
		'js'                 => '',
		'jsCallback'         => '',
		'css'                => '',
	);
	protected $layoutFile = '_layout.php';
	protected $layoutData = array();

	public function __construct()
	{

		parent::__construct();
	}

	public static function addCss($css)
	{
		static::$template_vars['css'][] = $css;
	}

	public static function addJs($js, $callback = '')
	{
		static::$template_vars['js'][] = $js;
		if ($callback)
			static::$template_vars['jsCallback'][$js] = $callback;
	}

	public static function setDefaultTitle($text)
	{
		static::$template_vars['defaultTitle'] = $text;
	}

	public static function setDefaultDescription($text)
	{
		static::$template_vars['defaultDescription'] = $text;
	}

	public static function setTitle($text)
	{
		static::$template_vars['title'] = $text;
	}

	public static function setDescription($text)
	{
		static::$template_vars['description'] = $text;
	}

	public static function setKeywords($text)
	{
		static::$template_vars['keywords'] = $text;
	}

	public function setLayoutFile($layoutFile = NULL, $layoutData = array())
	{
		$this->layoutFile = $layoutFile;
		$this->layoutData = $layoutData;
	}

	public function setLayoutData($layoutData = array())
	{
		$this->layoutData = $layoutData;
	}

	public function render($template, $data = null)
	{
		return $this->renderLayout(parent::render($template, $data));
	}

	public function renderLayout($output)
	{
		$templatePathname = $this->getTemplatePathname($this->layoutFile);
		if (!is_file($templatePathname)) {
			throw new \RuntimeException("View cannot render `$this->layoutFile` : the layout template does not exist");
		}

		extract($this->layoutData);
		extract(static::$template_vars);

		ob_start();
		require $templatePathname;

		return ob_get_clean();
	}

}