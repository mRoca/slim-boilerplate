<?php

namespace Perso;

use \PDO;

require_once(dirname(__FILE__) . '/../../config.php');

class DB
{

	/**
	 * @var DB
	 */
	private static $singleton = null;
	protected $type = DB_TYPE;
	protected $host = DB_HOST;
	protected $base = DB_BASE;
	protected $user = DB_USER;
	protected $pass = DB_PASSWORD;
	protected $connectionDone = false;
	/**
	 * @var PDO
	 */
	protected $pdoObj = null;

	private function __construct()
	{

	}

	/**
	 * @return DB
	 */
	public static function getInstance()
	{
		if (self::$singleton === null) {
			self::$singleton = new self();
		}

		return self::$singleton;
	}

	/**
	 * Execute the PDO 'query' function => SELECT
	 *
	 * Example: $db->query('SELECT * FROM table WHERE name = ? AND firstname = ?', array('My Name', 'My firstname'));
	 * Example: $db->query('SELECT * FROM table WHERE name = :name AND firstname = :firstname', array('name' => 'My Name', 'firstname' => 'My firstname'));
	 *
	 * @param string $sql
	 * @param array $values
	 * @return array
	 */
	public function query($sql, $values = array())
	{
		if (!$this->isConnectionDone())
			$this->connection();

		try {
			if (!is_array($values))
				$values = array($values);

			if (count($values) > 0) {
				$req = $this->pdoObj->prepare($sql);
				$req->execute($values);

				return $req->fetchAll(PDO::FETCH_ASSOC);
			} else {
				return $this->pdoObj->query($sql)->fetchAll(PDO::FETCH_ASSOC);
			}
		} catch (\Exception $e) {
			$this->error($e->getMessage());

			return array();
		}
	}

	/**
	 * @return bool
	 */
	public function isConnectionDone()
	{
		return $this->connectionDone;
	}

	/**
	 * Do the database connection
	 *
	 * @return bool
	 */
	public function connection()
	{
		try {
			$pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$this->pdoObj                   = new PDO($this->type . ':host=' . $this->host . ';dbname=' . $this->base . '', $this->user, $this->pass, $pdo_options);
			$this->connectionDone           = true;

			return true;

		} catch (\Exception $e) {
			$this->connectionDone = false;
			$this->error($e->getMessage());

			return false;
		}
	}

	/**
	 * Display an error
	 *
	 * @param string $text
	 */
	protected function error($text = '')
	{
		die('Database lib error : ' . $text);
	}

	/**
	 * Execute the PDO 'exec' function => INSERT, UPDATE adn DELETE
	 *
	 * Example: $db->exec('UPDATE table SET name = ? WHERE id = ?', array('My True Name', 1));
	 * Example: $db->exec('UPDATE table SET name = :name WHERE id = :id', array('name' => 'My True Name', 'id' => 1));
	 * Example: $db->exec('DELETE FROM table WHERE id = :id', array('id' => 1));
	 *
	 * @param string $sql
	 * @param array $values
	 *
	 * @return bool|int
	 */
	public function exec($sql, $values = array())
	{
		if (!$this->isConnectionDone())
			$this->connection();

		try {
			if (is_array($values) && count($values) > 0) {
				$req = $this->pdoObj->prepare($sql);

				return $req->execute($values);
			} else {
				return $this->pdoObj->exec($sql);
			}
		} catch (\Exception $e) {
			$this->error($e->getMessage());

			return false;
		}
	}

	/**
	 * Read the $binaryFields files contents and execute the PDO 'execute' function with this blobs => INSERT and UPDATE
	 *
	 * Example: $db->exec('INSERT INTO table (name, image) VALUES (:name, :imagefile)', array('name' => 'The Name'), array('imagefile' => '/tmp/img.jpg'));
	 *
	 * @param string $sql
	 * @param array $textFields
	 * @param array $binaryFields
	 * @return bool|int|string
	 */
	public function executeWithBinaryFiles($sql, $textFields = array(), $binaryFields = array())
	{
		if (!$this->isConnectionDone())
			$this->connection();

		try {
			$req = $this->pdoObj->prepare($sql);

			foreach ($binaryFields as $key => $value) {
				$fic = fopen($value, 'rb');
				$req->bindParam($key, $fic, PDO::PARAM_LOB);
			}

			foreach ($textFields as $key => &$value)
				$req->bindParam($key, $value);

			if ($req->execute())
				return $this->pdoObj->lastInsertId();

			return false;

		} catch (\Exception $e) {
			$this->error($e->getMessage());

			return false;
		}
	}

}
