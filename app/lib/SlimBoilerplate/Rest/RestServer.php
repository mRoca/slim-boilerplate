<?php

namespace SlimBoilerplate\Rest;

use SlimBoilerplate\Dbal\TableModel;

/**
 * Automatize RESTFULL CRUD routing
 *
 * Usage:
 *    $restCategories = new \SlimBoilerplate\RestServer('categories');
 *    $restCategories->createRoutes($app, $authenticateAdmin);
 *
 * Class RestServer
 * @package Perso
 */
class RestServer
{

	/**
	 * The table or collection name
	 * @var string
	 */
	protected $collection = '';

	public function __construct($collection)
	{
		$this->collection = $collection;
	}

	public function __call($method, $args)
	{
		if (isset($this->$method)) {
			$func = $this->$method;

			return call_user_func_array($func, $args);
		}

		return null;
	}

	/**
	 * @param \Slim\Slim $app
	 * @param mixed $middleware
	 */
	public function createRoutes(& $app, & $middleware = null)
	{

		if ($middleware === null) {
			$middleware = function () {
				return function () {
				};
			};
		}

		if (!$this->collection)
			self::error('Collection unknow');

		//READ the table and return all objects
		$app->get(
			"/$this->collection",
			$middleware(),
			function () use ($app) {
				$params = $app->request()->params();
				$model = new TableModel($this->collection);

				try {
					$items = count($params) ? $model->read(key($params), current($params)) : $model->readAll();
					self::response($items);
				} catch (\Exception $e) {
					self::error($e->getMessage());
				}
			}
		);

		//READ an object and return it
		$app->get(
			"/$this->collection/:id",
			$middleware(),
			function ($id) use ($app) {
				$model = new TableModel($this->collection);

				try {
					$obj = $model->readById($id);

					if (!is_array($obj) || !count($obj))
						self::error('Item not found', 404);
					else
						self::response($obj);

				} catch (\Exception $e) {
					self::error($e->getMessage());
				}
			}
		);

		//INSERT a new object and return it
		$app->post(
			"/$this->collection",
			$middleware(),
			function () use ($app) {

				$values = $this->getInputValues($app);

				if (!count($values))
					self::error('No input data', 400);
				else {
					$model = new TableModel($this->collection);

					try {
						$newId = $model->create($values);

						if (!intval($newId))
							self::error('Error inserting object');
						else {
							$obj = $model->readById($newId);

							if (!is_array($obj) || !count($obj))
								self::error('New item not found', 404);
							else
								self::response($obj);
						}
					} catch (\Exception $e) {
						self::error($e->getMessage());
					}
				}
			}
		);

		//UPDATE an object and return it
		$updateFunction = function () use ($app) {
			return function ($id) use ($app) {
				$values = $this->getInputValues($app);

				if (!count($values))
					self::error('No input data', 400);
				else {
					$model = new TableModel($this->collection);

					try {
						if (!$model->updateById($id, $values))
							self::error('Error updating object');
						else {
							$obj = $model->readById($id);

							if (!is_array($obj) || !count($obj))
								self::error('Updated item not found', 404);
							else
								self::response($obj);
						}
					} catch (\Exception $e) {
						self::error($e->getMessage());
					}
				}
			};
		};

		$app->put(
			"/$this->collection/:id",
			$middleware(),
			$updateFunction()
		);

		$app->post(
			"/$this->collection/:id",
			$middleware(),
			$updateFunction()
		);

		//DELETE an object
		$app->delete(
			"/$this->collection/:id",
			$middleware(),
			function ($id) use ($app) {
				$model = new TableModel($this->collection);

				if (!$model->deleteById($id))
					self::error('Error deleting object');

				self::response(array('success' => 'Object deleted'));
			}
		);
	}

	/**
	 * @param string $text
	 * @param int $status
	 */
	public static function error($text, $status = 400)
	{
		//throw new \ErrorException($text);

		self::response(array('error' => $text), $status);
		exit;
	}

	/**
	 * @param mixed $data
	 * @param int $status
	 */
	public static function response($data, $status = 200)
	{
		header("Access-Control-Allow-Origin: *");
		header("Access-Control-Allow-Methods: *");
		header("Content-Type: application/json");

		header("HTTP/1.1 " . $status . " " . self::requestStatus($status));

		echo json_encode($data);
	}

	/**
	 * @param int $code
	 * @return string
	 */
	protected static function requestStatus($code)
	{
		$status = array(
			100 => 'Continue',
			101 => 'Switching Protocols',
			200 => 'OK',
			201 => 'Created',
			202 => 'Accepted',
			203 => 'Non-Authoritative Information',
			204 => 'No Content',
			205 => 'Reset Content',
			206 => 'Partial Content',
			300 => 'Multiple Choices',
			301 => 'Moved Permanently',
			302 => 'Found',
			303 => 'See Other',
			304 => 'Not Modified',
			305 => 'Use Proxy',
			306 => '(Unused)',
			307 => 'Temporary Redirect',
			400 => 'Bad Request',
			401 => 'Unauthorized',
			402 => 'Payment Required',
			403 => 'Forbidden',
			404 => 'Not Found',
			405 => 'Method Not Allowed',
			406 => 'Not Acceptable',
			407 => 'Proxy Authentication Required',
			408 => 'Request Timeout',
			409 => 'Conflict',
			410 => 'Gone',
			411 => 'Length Required',
			412 => 'Precondition Failed',
			413 => 'Request Entity Too Large',
			414 => 'Request-URI Too Long',
			415 => 'Unsupported Media Type',
			416 => 'Requested Range Not Satisfiable',
			417 => 'Expectation Failed',
			418 => 'Im a teapot',
			500 => 'Internal Server Error',
			501 => 'Not Implemented',
			502 => 'Bad Gateway',
			503 => 'Service Unavailable',
			504 => 'Gateway Timeout',
			505 => 'HTTP Version Not Supported'
		);

		return ($status[$code]) ? $status[$code] : $status[500];
	}

	/**
	 * Return input value passed by POST or RAW DATA (Payload)
	 * @param \Slim\Slim $app
	 * @return array
	 */
	public function getInputValues(& $app)
	{
		return self::getInput($app);
	}

	/**
	 * Return input values : POST, PUT or RAW DATA
	 * @param \Slim\Slim $app
	 * @return array|mixed
	 */
	public static function getInput(& $app)
	{
		if (count($app->request->post()))
			return $app->request->post();

		if (count($app->request->put()))
			return $app->request->put();

		if (count($app->request->getBody()))
			return json_decode($app->request->getBody(), true);

		return array();
	}
} 
